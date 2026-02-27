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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\ImageManager;

/**
 * 노트 서비스
 */
class NoteService
{
    /**
     * 허용 HTML 태그 목록
     *
     * @var array<int, string>
     */
    private array $allowedTags = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'ul', 'ol', 'li', 'blockquote', 'pre', 'code', 'a',
    ];

    public function __construct(
        private NoteTopicRepository $noteTopicRepository,
        private NoteRepository $noteRepository,
        private NoteTagRepository $noteTagRepository,
        private NoteTagMapRepository $noteTagMapRepository
    ) {}

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
            'user_idx' => auth()->id(),
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
                'user_idx' => auth()->id(),
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
            $userIdx = (int) (auth()->id() ?? 0);
            $thumbnailPath = null;

            if ($request->hasFile('thumbnail_path')) {
                $thumbnailPath = $this->storeThumbnail($request->file('thumbnail_path'));
            }

            $normalizedContent = $this->normalizeUtf8((string) $request->input('content'));
            $sanitizedContent = $this->sanitizeHtml($normalizedContent);

            $note = $this->noteRepository->create([
                'group_idx' => $topic->category->group_idx,
                'categories_idx' => $topic->categories_idx,
                'topic_idx' => $topic->idx,
                'group_code' => $resolvedGroupCode,
                'categories_code' => $topic->category->code,
                'subject' => $request->input('subject'),
                'content' => $sanitizedContent,
                'thumbnail_path' => $thumbnailPath,
                'use_flag' => 0,
                'create_user_idx' => $userIdx,
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

            Log::info('[Note][Create] 등록 완료', [
                'user_idx' => auth()->id(),
                'note_idx' => $note->idx,
                'group_code' => $resolvedGroupCode,
                'category_code' => $topic->category->code,
                'topic_idx' => $topicIdx,
                'thumbnail_path' => $thumbnailPath,
                'tag_count' => count($tagIdxList),
                'ip' => request()->ip(),
            ]);

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
        $userIdx = (int) (auth()->id() ?? 0);
        $isAdmin = auth()->check() && (auth()->user()?->level === 'admin');
        $note = $this->noteRepository->findByIdxAndCodes($idx, $resolvedGroupCode, $categoryCode);

        if (! $isAdmin && ($note->use_flag ?? 'N') !== 'Y') {
            abort(404);
        }

        event(new NoteHistoryEvent(
            noteIdx: $note->idx,
            jobType: '조회',
            createUserIdx: $userIdx,
            ip: request()->ip(),
            userAgent: request()->userAgent() ?? '',
        ));

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
    public function getNotes(string $groupCode, string $categoryCode): LengthAwarePaginator
    {
        $resolvedGroupCode = (string) config("note.group.{$groupCode}", $groupCode);
        $isAdmin = auth()->check() && (auth()->user()?->level === 'admin');

        return $this->noteRepository->paginateByCodes($resolvedGroupCode, $categoryCode, $isAdmin, 20);
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

        return $this->noteRepository->findByIdxAndCodes($idx, $resolvedGroupCode, $categoryCode);
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
        $userIdx = (int) (auth()->id() ?? 0);

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
            $thumbnailPath = $note->thumbnail_path;

            if ($request->hasFile('thumbnail_path')) {
                $this->deletePublicThumbnail($thumbnailPath);
                $thumbnailPath = $this->storeThumbnail($request->file('thumbnail_path'));
            }

            $sanitizedContent = $this->sanitizeHtml($this->normalizeUtf8((string) $request->input('content')));

            $note = $this->noteRepository->update($note, [
                'group_idx' => $topic->category->group_idx,
                'categories_idx' => $topic->categories_idx,
                'topic_idx' => $topic->idx,
                'group_code' => $resolvedGroupCode,
                'categories_code' => $topic->category->code,
                'subject' => $request->input('subject'),
                'content' => $sanitizedContent,
                'thumbnail_path' => $thumbnailPath,
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

            Log::info('[Note][Update] 수정 완료', [
                'user_idx' => $userIdx,
                'note_idx' => $note->idx,
                'group_code' => $resolvedGroupCode,
                'category_code' => $topic->category->code,
                'topic_idx' => $topic->idx,
                'thumbnail_path' => $thumbnailPath,
                'tag_count' => count($tagIdxList),
                'ip' => request()->ip(),
            ]);

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

            $note = $this->noteRepository->update($note, [
                'thumbnail_path' => null,
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
        $content = $this->normalizeUtf8($content);

        if ($this->looksLikeHtml($content)) {
            return $this->sanitizeHtml($content);
        }

        $markdownHtml = Str::markdown($content, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return $this->sanitizeHtml((string) $markdownHtml);
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

        // 저장 폴더명 규칙: YYYYMM
        $dir = date('Ym');
        $filename = Str::uuid()->toString() . '.' . $extension;
        $path = $dir . '/' . $filename;

        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
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
     * 문자열이 HTML 형태인지 판단
     *
     * @param string $value
     * @return bool
     */
    private function looksLikeHtml(string $value): bool
    {
        return (bool) preg_match('/<\s*\/?\s*(p|br|strong|b|em|i|u|s|h[1-6]|ul|ol|li|blockquote|pre|code|a)\b/i', $value);
    }

    /**
     * 허용 태그/속성 기준 HTML 정제
     *
     * @param string $html
     * @return string
     */
    private function sanitizeHtml(string $html): string
    {
        $html = trim($this->normalizeUtf8($html));

        if ($html === '') {
            return '';
        }

        if (! class_exists(\DOMDocument::class)) {
            return strip_tags($html, '<' . implode('><', $this->allowedTags) . '>');
        }

        $wrappedHtml = '<!doctype html><html><body>' . $html . '</body></html>';
        $doc = new \DOMDocument('1.0', 'UTF-8');

        $previousInternalErrors = libxml_use_internal_errors(true);
        $loadHtml = '<?xml encoding="UTF-8">' . $wrappedHtml;
        if (function_exists('mb_convert_encoding')) {
            $loadHtml = mb_convert_encoding($loadHtml, 'HTML-ENTITIES', 'UTF-8');
        }

        $doc->loadHTML($loadHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previousInternalErrors);

        $xpath = new \DOMXPath($doc);
        foreach ($xpath->query('//processing-instruction("xml")') as $xmlNode) {
            $xmlNode->parentNode?->removeChild($xmlNode);
        }

        foreach ($xpath->query('//*') as $node) {
            if (! $node instanceof \DOMElement) {
                continue;
            }

            $tag = strtolower($node->tagName);

            if (! in_array($tag, $this->allowedTags, true) && $tag !== 'html' && $tag !== 'body') {
                $node->parentNode?->removeChild($node);
                continue;
            }

            if ($node->hasAttributes()) {
                $attrsToRemove = [];

                foreach ($node->attributes as $attribute) {
                    if (! $attribute instanceof \DOMAttr) {
                        continue;
                    }

                    $attrName = strtolower($attribute->name);

                    // 이벤트 속성 제거 (onclick 등)
                    if (str_starts_with($attrName, 'on')) {
                        $attrsToRemove[] = $attribute->name;
                        continue;
                    }

                    // 기본적으로 href 외 속성 제거
                    if ($tag !== 'a' || $attrName !== 'href') {
                        $attrsToRemove[] = $attribute->name;
                        continue;
                    }

                    $href = trim((string) $attribute->value);
                    if ($href === '' || preg_match('/^\s*javascript:/i', $href) || preg_match('/^\s*data:/i', $href)) {
                        $attrsToRemove[] = $attribute->name;
                    }
                }

                foreach ($attrsToRemove as $attrName) {
                    $node->removeAttribute($attrName);
                }
            }
        }

        $body = $doc->getElementsByTagName('body')->item(0);
        if (! $body) {
            return '';
        }

        $result = '';
        foreach ($body->childNodes as $childNode) {
            $result .= $doc->saveHTML($childNode);
        }

        return trim($result);
    }

    /**
     * 저장/조회 중 잘못 인코딩된 UTF-8 텍스트 복원
     *
     * @param string $value
     * @return string
     */
    private function normalizeUtf8(string $value): string
    {
        if (! $this->looksLikeMojibake($value)) {
            return $value;
        }

        if (! function_exists('mb_convert_encoding')) {
            return $value;
        }

        $converted = @mb_convert_encoding($value, 'ISO-8859-1', 'UTF-8');
        if (! is_string($converted) || $converted === '') {
            return $value;
        }

        if (function_exists('mb_check_encoding') && ! mb_check_encoding($converted, 'UTF-8')) {
            return $value;
        }

        return $this->looksLikeMojibake($converted) ? $value : $converted;
    }

    /**
     * 흔한 UTF-8 깨짐 패턴 감지
     *
     * @param string $value
     * @return bool
     */
    private function looksLikeMojibake(string $value): bool
    {
        return (bool) preg_match('/(?:Ã.|Â.|ì.|ë.|ê.|í.|î.|ð.|�)/u', $value);
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
}
