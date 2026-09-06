<?php

namespace App\Services;

use App\Events\NoteHistoryEvent;
use App\Http\Requests\Notes\StoreNoteRequest;
use App\Http\Requests\Notes\UpdateNoteRequest;
use App\Models\Note;
use App\Models\NoteTag;
use App\Models\NoteTopic;
use App\Repositories\NoteRepository;
use App\Repositories\NoteTagMapRepository;
use App\Repositories\NoteTagRepository;
use App\Repositories\NoteTopicRepository;
use App\Support\EditorContentProcessor;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Auth;
use App\Jobs\NoteImageProcessingJob;

/**
 * 노트 서비스
 */
class NoteService
{
    public function __construct(
        private NoteTopicRepository $noteTopicRepository,
        private NoteRepository $noteRepository,
        private NoteTagRepository $noteTagRepository,
        private NoteTagMapRepository $noteTagMapRepository,
        private EditorContentProcessor $editorContentProcessor,
        private ContentCacheService $contentCacheService
    ) {}

    /**
     * 노트 카테고리 목록 조회
     *
     * @param string $groupCode
     * @return Collection
     */
    public function getNoteCategories(string $groupCode): Collection
    {
        $resolvedGroupCode = (string) config("note.group.{$groupCode}", $groupCode);
        $categories = $this->noteTopicRepository->getCategoriesByGroupCode($resolvedGroupCode);

        Log::info('[Note][Category][List] 조회 완료', [
            'user_idx' => Auth::id(),
            'group_code' => $groupCode,
            'resolved_group_code' => $resolvedGroupCode,
            'count' => $categories->count(),
            'ip' => request()->ip(),
        ]);

        return $categories;
    }

    /**
     * 노트 주제 목록 조회
     *
     * @param string $groupCode
     * @param string $categoryCode
     * @return Collection
     */
    public function getNoteTopics(string $groupCode, string $categoryCode): Collection
    {
        $resolvedGroupCode = config("note.group.{$groupCode}", $groupCode);
        $topics = $this->noteTopicRepository->getActiveTopicsByGroupAndCategory($resolvedGroupCode, $categoryCode);

        Log::info('[Note][Topic][List] 조회 완료', [
            'user_idx' => Auth::id(),
            'group_code' => $groupCode,
            'resolved_group_code' => $resolvedGroupCode,
            'category_code' => $categoryCode,
            'count' => $topics->count(),
            'ip' => request()->ip(),
        ]);

        return $topics;
    }

    /**
     * 노트 생성
     *
     * @param StoreNoteRequest $request
     * @param string $groupCode
     * @param string $categoryCode
     * @return Note
     */
    public function createNote(StoreNoteRequest $request, string $groupCode, string $categoryCode): Note
    {
        $resolvedGroupCode = (string) config("note.group.{$groupCode}", $groupCode);
        $topicIdx = (int) $request->integer('topic');
        $topic = NoteTopic::with(['category.group'])->findOrFail($topicIdx);

        $isMatch = ($topic->use_flag ?? 'N') === 'Y'
            && ($topic->category?->code === $categoryCode)
            && ($topic->category?->group?->code === $resolvedGroupCode);

        if (! $isMatch) {
            Log::warning('[Note][Create] topic mismatch', [
                'user_idx' => Auth::id(),
                'group_code' => $groupCode,
                'resolved_group_code' => $resolvedGroupCode,
                'category_code' => $categoryCode,
                'topic_idx' => $topicIdx,
                'ip' => request()->ip(),
            ]);

            throw ValidationException::withMessages([
                'topic' => '해당 카테고리에서 사용 가능한 주제를 선택해 주세요.',
            ]);
        }

        return DB::transaction(function () use ($request, $topic, $resolvedGroupCode, $topicIdx): Note {
            $userIdx = (int) (Auth::id() ?? 0);
            $thumbnailPath = null;
            $ogImagePath = null;

            if ($request->hasFile('thumbnail_path')) {
                $thumbnailPath = $this->storeThumbnail($request->file('thumbnail_path'));
            }

            $sanitizedContent = $this->editorContentProcessor->sanitizeForStorage(
                (string) $request->input('content')
            );

            $note = $this->noteRepository->create([
                'group_idx' => $topic->category->group_idx,
                'categories_idx' => $topic->categories_idx,
                'topic_idx' => $topic->idx,
                'group_code' => $resolvedGroupCode,
                'categories_code' => $topic->category->code,
                'subject' => $request->input('subject'),
                'content' => $sanitizedContent,
                'thumbnail_path' => $thumbnailPath,
                'og_image_path' => null,
                'use_flag' => 0,
                'create_user_idx' => $userIdx,
            ]);

            $note = $this->noteRepository->update($note, [
                'access_page' => $this->makeBlogAccessPage(
                    (string) $topic->category->code,
                    (int) $note->idx
                ),
            ]);

            $tagNames = $this->parseTagNames((string) $request->input('tags', ''));
            $tagIdxList = [];

            foreach ($tagNames as $tagName) {
                $tag = $this->noteTagRepository->findOrCreateByName($tagName, $userIdx);
                $tagIdxList[] = (int) $tag->idx;
            }

            $tagIdxList = array_values(array_unique($tagIdxList));
            $this->noteTagMapRepository->insertMappings((int) $note->idx, $tagIdxList);

            event(new NoteHistoryEvent(
                noteIdx: $note->idx,
                jobType: '등록',
                createUserIdx: $userIdx,
                ip: $request->ip(),
                userAgent: $request->userAgent() ?? '',
            ));

            /*
             * DB 트랜잭션이 커밋된 후 Job을 Queue에 등록한다.
             */
            if ($thumbnailPath !== null) {
                NoteImageProcessingJob::dispatch(
                    noteIdx: (int) $note->idx,
                    sourceThumbnailPath: $thumbnailPath,
                    imageType: 'og_image',
                    previousImagePath: null
                )->afterCommit();
            }

            Log::info('[Note][Create] 등록 완료', [
                'user_idx' => Auth::id(),
                'note_idx' => $note->idx,
                'group_code' => $resolvedGroupCode,
                'category_code' => $topic->category->code,
                'topic_idx' => $topicIdx,
                'thumbnail_path' => $thumbnailPath,
                'og_image_path' => $ogImagePath,
                'tag_count' => count($tagIdxList),
                'ip' => request()->ip(),
            ]);

            $this->forgetContentCache($resolvedGroupCode);

            return $note;
        });
    }

    /**
     * 노트 상세 조회
     *
     * @param string $groupCode
     * @param string $categoryCode
     * @param int $idx
     * @return Note
     */
    public function getNoteDetail(string $groupCode, string $categoryCode, int $idx): Note
    {
        $resolvedGroupCode = (string) config("note.group.{$groupCode}", $groupCode);
        $userIdx = (int) (Auth::id() ?? 0);
        $isAdmin = Auth::check() && (Auth::user()?->level === 'admin');
        $note = $this->noteRepository->findByIdxAndCodes($idx, $resolvedGroupCode, $categoryCode);

        if (! $isAdmin && ($note->use_flag ?? 'N') !== 'Y') {
            abort(404);
        }

        if ($groupCode !== 'blogs') {
            event(new NoteHistoryEvent(
                noteIdx: $note->idx,
                jobType: '조회',
                createUserIdx: $userIdx,
                ip: request()->ip(),
                userAgent: request()->userAgent() ?? '',
            ));
        }

        Log::info('[Note][View] 조회 완료', [
            'user_idx' => $userIdx,
            'note_idx' => $note->idx,
            'group_code' => $resolvedGroupCode,
            'category_code' => $categoryCode,
            'ip' => request()->ip(),
        ]);

        return $note;
    }

    /**
     * 노트 목록 조회
     *
     * @param string $groupCode
     * @param string $categoryCode
     * @return LengthAwarePaginator
     */
    public function getNotes(
        string $groupCode,
        ?string $categoryCode,
        array $filters = [],
        int $perPage = 10
    ): LengthAwarePaginator
    {
        $resolvedGroupCode = (string) config("note.group.{$groupCode}", $groupCode);
        $isAdmin = Auth::check() && (Auth::user()?->level === 'admin');
        $searchType = (string) ($filters['search_select_type'] ?? 'title');
        $searchKeyword = trim((string) ($filters['search_keyword'] ?? ''));
        $searchTopic = trim((string) ($filters['search_topic'] ?? ''));
        $notes = $this->noteRepository->paginateByCodes(
            $resolvedGroupCode,
            $categoryCode,
            $isAdmin,
            $perPage,
            $searchType,
            $searchKeyword,
            $searchTopic
        );

        Log::info('[Note][List] 조회 완료', [
            'user_idx' => Auth::id(),
            'group_code' => $groupCode,
            'resolved_group_code' => $resolvedGroupCode,
            'category_code' => $categoryCode,
            'search_type' => $searchType,
            'search_keyword' => $searchKeyword,
            'search_topic'   => $searchTopic,
            'page' => $notes->currentPage(),
            'per_page' => $notes->perPage(),
            'total' => $notes->total(),
            'ip' => request()->ip(),
        ]);

        return $notes;
    }

    /**
     * 같은 카테고리 최신 관련 노트 조회 (현재 노트 제외)
     *
     * @param string $groupCode
     * @param string $categoryCode
     * @param integer $noteIdx
     * @param integer $topicIdx
     * @param integer $limit
     * @return Collection
     */
    public function getLatestRelatedNotes(
        string $groupCode,
        string $categoryCode,
        int $noteIdx,
        int $topicIdx,
        int $limit = 5
    ): Collection {
        $resolvedGroupCode = config("note.group.{$groupCode}", $groupCode);
        $isAdmin = Auth::check() && (Auth::user()?->level === 'admin');
        $relatedNotes = $this->noteRepository->getLatestByCodesExcluding(
            $resolvedGroupCode,
            $categoryCode,
            $noteIdx,
            $topicIdx,
            $isAdmin,
            $limit
        );

        Log::info('[Note][Related][List] 조회 완료', [
            'user_idx' => Auth::id(),
            'group_code' => $groupCode,
            'resolved_group_code' => $resolvedGroupCode,
            'category_code' => $categoryCode,
            'note_idx' => $noteIdx,
            'topic_idx' => $topicIdx,   
            'count' => $relatedNotes->count(),
            'ip' => request()->ip(),
        ]);

        return $relatedNotes;
    }

    /**
     * 수정/삭제용 노트 조회
     *
     * @param string $groupCode
     * @param string $categoryCode
     * @param int $idx
     * @return Note
     */
    public function getNote(string $groupCode, string $categoryCode, int $idx): Note
    {
        $resolvedGroupCode = (string) config("note.group.{$groupCode}", $groupCode);
        $note = $this->noteRepository->findByIdxAndCodes($idx, $resolvedGroupCode, $categoryCode);

        Log::info('[Note][Target] 조회 완료', [
            'user_idx' => Auth::id(),
            'note_idx' => $note->idx,
            'group_code' => $groupCode,
            'resolved_group_code' => $resolvedGroupCode,
            'category_code' => $categoryCode,
            'ip' => request()->ip(),
        ]);

        return $note;
    }

    /**
     * 노트 수정
     *
     * @param UpdateNoteRequest $request
     * @param string $groupCode
     * @param string $categoryCode
     * @param int $idx
     * @return Note
     */
    public function updateNote(UpdateNoteRequest $request, string $groupCode, string $categoryCode, int $idx): Note
    {
        $resolvedGroupCode = (string) config("note.group.{$groupCode}", $groupCode);
        $note = $this->noteRepository->findByIdxAndCodes($idx, $resolvedGroupCode, $categoryCode);
        $topicIdx = (int) $request->integer('topic');
        $topic = NoteTopic::with(['category.group'])->findOrFail($topicIdx);
        $userIdx = (int) (Auth::id() ?? 0);

        $isMatch = ($topic->use_flag ?? 'N') === 'Y'
            && ($topic->category?->code === $categoryCode)
            && ($topic->category?->group?->code === $resolvedGroupCode);

        if (! $isMatch) {
            throw ValidationException::withMessages([
                'topic' => '해당 카테고리에서 사용 가능한 주제를 선택해 주세요.',
            ]);
        }

        return DB::transaction(function () use ($request, $note, $topic, $resolvedGroupCode, $userIdx): Note {
            $beforeTagIdxList = $this->noteTagMapRepository->getTagIdxListByNote((int) $note->idx);
            
            $previousThumbnailPath = $note->thumbnail_path;
            $previousOgImagePath = $note->og_image_path;

            $thumbnailPath = $previousThumbnailPath;
            $ogImagePath = $previousOgImagePath;

            $hasNewThumbnail = $request->hasFile('thumbnail_path');

            // 새 일반 썸네일만 먼저 저장한다.
            // 기존 썸네일과 기존 OG 이미지는 아직 삭제하지 않는다.
            if ($hasNewThumbnail) {
                $thumbnailPath = $this->storeThumbnail($request->file('thumbnail_path'));
            }

            $sanitizedContent = $this->editorContentProcessor->sanitizeForStorage(
                (string) $request->input('content')
            );

            $note = $this->noteRepository->update($note, [
                'group_idx' => $topic->category->group_idx,
                'categories_idx' => $topic->categories_idx,
                'topic_idx' => $topic->idx,
                //'group_code' => $resolvedGroupCode,
                'categories_code' => $topic->category->code,
                'access_page' => $this->makeBlogAccessPage(
                    (string) $topic->category->code,
                    (int) $note->idx
                ),
                'subject' => $request->input('subject'),
                'content' => $sanitizedContent,
                'thumbnail_path' => $thumbnailPath,
                'og_image_path' => $ogImagePath,
                'use_flag' => $request->input('usg_flag', $note->use_flag ?? 'N'),
                'update_user_idx' => $userIdx,
            ]);

            $tagNames = $this->parseTagNames((string) $request->input('tags', ''));
            $tagIdxList = [];

            foreach ($tagNames as $tagName) {
                $tag = $this->noteTagRepository->findOrCreateByName($tagName, $userIdx);
                $tagIdxList[] = (int) $tag->idx;
            }

            $tagIdxList = array_values(array_unique($tagIdxList));
            $this->noteTagMapRepository->replaceMappings((int) $note->idx, $tagIdxList);
            $removedTagIdxList = array_values(array_diff($beforeTagIdxList, $tagIdxList));
            $this->softDeleteOrphanTags($removedTagIdxList, $userIdx);

            event(new NoteHistoryEvent(
                noteIdx: (int) $note->idx,
                jobType: '수정',
                createUserIdx: $userIdx,
                ip: (string) $request->ip(),
                userAgent: (string) ($request->userAgent() ?? ''),
            ));

            if ($hasNewThumbnail && !empty($thumbnailPath)) {
                // DB 커밋 후 새로운 OG 이미지 생성 Job 실행
                NoteImageProcessingJob::dispatch(
                    noteIdx: (int) $note->idx,
                    sourceThumbnailPath: (string) $thumbnailPath,
                    imageType: 'og_image',
                    previousImagePath: $previousOgImagePath
                )->afterCommit();

                // 기존 일반 썸네일은 DB 커밋이 성공한 뒤 삭제한다.
                // 기존 OG 이미지는 Job 성공 후 Job 내부에서 삭제한다.
                DB::afterCommit(function () use ($previousThumbnailPath, $thumbnailPath): void {
                    if (!empty($previousThumbnailPath) && $previousThumbnailPath !== $thumbnailPath) {
                        $this->deletePublicThumbnail((string) $previousThumbnailPath);
                    }
                });
            }

            Log::info('[Note][Update] 수정 완료', [
                'user_idx' => $userIdx,
                'note_idx' => $note->idx,
                'group_code' => $resolvedGroupCode,
                'category_code' => $topic->category->code,
                'topic_idx' => $topic->idx,
                'thumbnail_path' => $thumbnailPath,
                'og_image_path' => $ogImagePath,
                'tag_count' => count($tagIdxList),
                'ip' => request()->ip(),
            ]);

            $this->forgetContentCache($resolvedGroupCode);

            return $note;
        });
    }

    /**
     * 노트 삭제
     *
     * @param Note $note
     * @param int $userIdx
     * @param string $ip
     * @param string $userAgent
     * @return void
     */
    public function deleteNote(Note $note, int $userIdx, string $ip, string $userAgent): void
    {
        DB::transaction(function () use ($note, $userIdx, $ip, $userAgent): void {
            $tagIdxList = $this->noteTagMapRepository->getTagIdxListByNote((int) $note->idx);

            if (! empty($note->thumbnail_path)) {
                $this->deletePublicThumbnail((string) $note->thumbnail_path);
            }

            if (! empty($note->og_image_path)) {
                $this->deletePublicThumbnail((string) $note->og_image_path);
            }

            $this->noteTagMapRepository->deleteMappingsByNote((int) $note->idx);
            $this->softDeleteOrphanTags($tagIdxList, $userIdx);

            Note::withTrashed()
                ->where('idx', $note->idx)
                ->update([
                    'delete_user_idx' => $userIdx,
                ]);

            $note->delete();

            event(new NoteHistoryEvent(
                noteIdx: (int) $note->idx,
                jobType: '삭제',
                createUserIdx: $userIdx,
                ip: $ip,
                userAgent: $userAgent,
            ));

            Log::info('[Note][Delete] 삭제 완료', [
                'user_idx' => $userIdx,
                'note_idx' => $note->idx,
                'ip' => $ip,
            ]);

            $this->forgetContentCache(
                (string) ($note->group_code ?? '')
            );
        });
    }

    /**
     * 노트 공개여부 토글
     *
     * @param Note $note
     * @param int $userIdx
     * @param string $ip
     * @param string $userAgent
     * @return Note
     */
    public function updateNoteUseFlag(Note $note, int $userIdx, string $ip, string $userAgent): Note
    {
        return DB::transaction(function () use ($note, $userIdx, $ip, $userAgent): Note {
            $nextUseFlag = ($note->use_flag ?? 'N') === 'Y' ? 'N' : 'Y';

            $note = $this->noteRepository->update($note, [
                'use_flag' => $nextUseFlag,
                'update_user_idx' => $userIdx,
            ]);

            event(new NoteHistoryEvent(
                noteIdx: (int) $note->idx,
                jobType: '수정',
                createUserIdx: $userIdx,
                ip: $ip,
                userAgent: $userAgent,
            ));

            Log::info('[Note][UseFlag] 변경 완료', [
                'user_idx' => $userIdx,
                'note_idx' => $note->idx,
                'use_flag' => $note->use_flag,
                'ip' => $ip,
            ]);

            $this->forgetContentCache(
                (string) ($note->group_code ?? '')
            );

            return $note;
        });
    }

    /**
     * 노트 썸네일 삭제
     *
     * @param Note $note
     * @param int $userIdx
     * @param string $ip
     * @param string $userAgent
     * @return Note
     */
    public function destroyNoteThumbnail(Note $note, int $userIdx, string $ip, string $userAgent): Note
    {
        return DB::transaction(function () use ($note, $userIdx, $ip, $userAgent): Note {
            if (! empty($note->thumbnail_path)) {
                $this->deletePublicThumbnail((string) $note->thumbnail_path);
            }

            if (! empty($note->og_image_path)) {
                $this->deletePublicThumbnail((string) $note->og_image_path);
            }

            $note = $this->noteRepository->update($note, [
                'thumbnail_path' => null,
                'og_image_path' => null,
                'update_user_idx' => $userIdx,
            ]);

            event(new NoteHistoryEvent(
                noteIdx: (int) $note->idx,
                jobType: '수정',
                createUserIdx: $userIdx,
                ip: $ip,
                userAgent: $userAgent,
            ));

            Log::info('[Note][Thumbnail] 삭제 완료', [
                'user_idx' => $userIdx,
                'note_idx' => $note->idx,
                'ip' => $ip,
            ]);

            return $note;
        });
    }

    /**
     * 노트 태그 삭제
     *
     * @param Note $note
     * @param string $tagName
     * @param int $userIdx
     * @param string $ip
     * @param string $userAgent
     * @return int
     */
    public function destroyNoteTag(Note $note, string $tagName, int $userIdx, string $ip, string $userAgent): int
    {
        return DB::transaction(function () use ($note, $tagName, $userIdx, $ip, $userAgent): int {
            $tagIdx = $this->noteTagMapRepository->findTagIdxByNoteAndTagName((int) $note->idx, $tagName);
            $deletedCount = $this->noteTagMapRepository->deleteMappingByTagName((int) $note->idx, $tagName);

            if ($deletedCount > 0 && $tagIdx !== null) {
                $this->softDeleteOrphanTags([$tagIdx], $userIdx);
            }

            event(new NoteHistoryEvent(
                noteIdx: (int) $note->idx,
                jobType: '수정',
                createUserIdx: $userIdx,
                ip: $ip,
                userAgent: $userAgent,
            ));

            Log::info('[Note][Tag] 삭제 완료', [
                'user_idx' => $userIdx,
                'note_idx' => $note->idx,
                'tag_name' => $tagName,
                'deleted_count' => $deletedCount,
                'ip' => $ip,
            ]);

            return $deletedCount;
        });
    }

    /**
     * 노트 content를 렌더링용 HTML로 변환
     *
     * @param string $content
     * @return string
     */
    public function toRenderableHtml(string $content): string
    {
        return $this->editorContentProcessor->toRenderableHtml($content);
    }

    /**
     * OG 이미지 URL에 버전 파라미터를 붙여 캐시를 우회합니다.
     * 수정 이력이 있으면 update_datetime, 아니면 create_datetime 기준으로 v 값을 생성합니다.
     *
     * @param Note $note
     * @return string
     */
    public function buildMetaImageUrl(Note $note): string
    {
        // OG 전용 이미지가 있으면 우선 사용하고, 없으면 썸네일, 그것도 없으면 기본 OG 이미지를 사용합니다.
        $imageUrl = ! empty($note->og_image_path)
            ? url(Storage::url((string) $note->og_image_path))
            : (! empty($note->thumbnail_path)
                ? url(Storage::url((string) $note->thumbnail_path))
                : asset('images/og/001.png'));

        // update/create 시각을 비교해서 캐시 버전 기준 시각을 결정합니다.
        $versionAt = null;

        if ($note->update_datetime && $note->create_datetime) {
            $versionAt = $note->update_datetime->ne($note->create_datetime)
                ? $note->update_datetime
                : $note->create_datetime;
        } else {
            $versionAt = $note->update_datetime ?? $note->create_datetime;
        }

        // 기준 시각이 없으면 원본 URL 그대로 반환합니다.
        if (! $versionAt) {
            return $imageUrl;
        }

        // 기존 쿼리스트링이 있으면 &, 없으면 ? 로 v 파라미터를 붙입니다.
        $separator = str_contains($imageUrl, '?') ? '&' : '?';

        return $imageUrl . $separator . 'v=' . $versionAt->format('YmdHis');
    }

    /**
     * 노트 썸네일 저장
     *
     * @param UploadedFile $file
     * @return string
     */
    private function storeThumbnail(UploadedFile $file): string
    {
        $mime = (string) $file->getMimeType();
        $ext = strtolower((string) $file->getClientOriginalExtension());
        $isPng = $mime === 'image/png' || $ext === 'png';

        $manager = ImageManager::gd();
        $image = $manager->read($file->getPathname());

        // EXIF 회전 보정 (아이폰/아이패드 촬영 이미지 대응)
        $image = $image->orient();

        // 가로 최대 1600px (원본이 작으면 축소 안 함)
        if ($image->width() > 1600) {
            $image = $image->scaleDown(width: 1600);
        }

        if ($isPng) {
            $encoded = $image->encode(new PngEncoder());
            $extension = 'png';
        } else {
            $encoded = $image->encode(new JpegEncoder(quality: 80));
            $extension = 'jpg';
        }

        // 저장 폴더명 규칙: YYYYMM, 파일명 규칙: YmdHis(.ext)
        $dir = now()->format('Ym');
        $filename = $this->generateTimestampThumbnailName($dir, $extension);
        $path = $dir . '/' . $filename;

        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }

    /**
     * 썸네일 파일명 생성
     *
     * 기본 규칙은 YmdHis.ext 이고, 같은 초에 충돌 시에만 접미사를 붙인다.
     *
     * @param string $dir
     * @param string $extension
     * @return string
     */
    private function generateTimestampThumbnailName(string $dir, string $extension): string
    {
        $timestamp = now()->format('YmdHis');
        $filename = "{$timestamp}.{$extension}";
        $sequence = 1;

        while (Storage::disk('public')->exists($dir . '/' . $filename)) {
            $filename = sprintf('%s_%02d.%s', $timestamp, $sequence, $extension);
            $sequence++;
        }

        return $filename;
    }

    /**
     * 해시태그 문자열 파싱
     *
     * @param string $rawTags
     * @return array<int, string>
     */
    private function parseTagNames(string $rawTags): array
    {
        $tags = array_map(static function (string $tag): string {
            return trim($tag);
        }, explode(',', $rawTags));

        $tags = array_filter($tags, static function (string $tag): bool {
            return $tag !== '';
        });

        return array_values(array_unique($tags));
    }

    /**
     * public 디스크 썸네일 삭제
     *
     * @param string|null $path
     * @return void
     */
    private function deletePublicThumbnail(?string $path): void
    {
        $rawPath = trim((string) $path);

        if ($rawPath === '') {
            return;
        }

        $normalizedPath = preg_replace('#^/?storage/#', '', $rawPath) ?? $rawPath;
        $normalizedPath = ltrim($normalizedPath, '/');

        $deleted = Storage::disk('public')->delete($normalizedPath);

        if (! $deleted) {
            Log::warning('[Note][Thumbnail] 파일 삭제 실패', [
                'raw_path' => $rawPath,
                'normalized_path' => $normalizedPath,
            ]);
        }
    }

    /**
     * 매핑이 없는 태그 soft delete
     *
     * @param array<int, int> $tagIdxList
     * @param int $userIdx
     * @return void
     */
    private function softDeleteOrphanTags(array $tagIdxList, int $userIdx): void
    {
        $targetIdxList = array_values(array_unique(array_map('intval', $tagIdxList)));

        foreach ($targetIdxList as $tagIdx) {
            if ($tagIdx <= 0) {
                continue;
            }

            if ($this->noteTagMapRepository->countMappingsByTagIdx($tagIdx) > 0) {
                continue;
            }

            $tag = NoteTag::query()->find($tagIdx);

            if (! $tag || $tag->trashed()) {
                continue;
            }

            $tag->forceFill([
                'delete_user_idx' => $userIdx,
            ])->saveQuietly();

            $tag->delete();
        }
    }

    /**
     * 블로그 변경 시 관련 공용 캐시 무효화
     *
     * @param string $groupCode
     * @return void
     */
    private function forgetContentCache(string $groupCode): void
    {
        if ($groupCode !== 'blog') {
            return;
        }

        DB::afterCommit(function () {
            $this->contentCacheService->forgetResource(
                'blog',
                'public'
            );
        });
    }

    /**
     * th_notes.access_page 컬럼 값 생성
     *
     * @param string $categoryCode
     * @param integer $noteIdx
     * @return string
     */
    private function makeBlogAccessPage(string $categoryCode, int $noteIdx): string
    {
        return '/blogs/' . $categoryCode . '/' . $noteIdx . '/show';
    }
}
