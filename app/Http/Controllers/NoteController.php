<?php

namespace App\Http\Controllers;

use App\Http\Requests\Notes\DestroyNoteRequest;
use App\Http\Requests\Notes\DestroyNoteTagRequest;
use App\Http\Requests\Notes\DestroyNoteThumbnailRequest;
use App\Http\Requests\Notes\IndexNoteRequest;
use App\Http\Requests\Notes\StoreNoteRequest;
use App\Http\Requests\Notes\UpdateNoteRequest;
use App\Http\Requests\Notes\UpdateNoteUseFlagRequest;
use App\Models\Note;
use App\Services\NoteService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * 노트(블로그) 컨트롤러
 */
class NoteController extends Controller
{
    public function __construct(
        private NoteService $noteService
    ) {}

    /**
     * 글 목록 
     *
     * @param Request $request
     * @param string|null $slug
     * 
     * @return View
     */
    public function index(IndexNoteRequest $request, ?string $slug = null)
    {
        $noteGroup = (string) $request->route('group');
        $resolvedSlug = trim((string) ($slug ?? ''));
        $categoryConfig = config("note.{$noteGroup}", []);
        $categoryCodes = is_array($categoryConfig) ? array_keys($categoryConfig) : [];

        if ($resolvedSlug === $noteGroup) {
            $resolvedSlug = '';
        }

        if ($resolvedSlug !== '' && ! in_array($resolvedSlug, $categoryCodes, true)) {
            abort(404);
        }

        $canCreate = (bool) optional($request->user())->can('create', Note::class);
        $categoryTitle = (string) data_get($categoryConfig, "{$resolvedSlug}.title", '');
        $listTitle = $resolvedSlug !== '' && $categoryTitle !== '' ? "{$categoryTitle} 글" : '전체 글';

        $filters = [
            'search_select_type' => (string) $request->query('search_select_type', 'title'),
            'search_keyword' => trim((string) $request->query('search_keyword', '')),
        ];
        $notes = $this->noteService->getNotes(
            $noteGroup,
            $resolvedSlug !== '' ? $resolvedSlug : null,
            $filters,
            10
        );

        if ($request->ajax()) {
            return response()->json($this->buildNoteListResponse($noteGroup, $resolvedSlug, $notes, $filters));
        }

        $initialPayload = $this->buildNoteListResponse($noteGroup, $resolvedSlug, $notes, $filters);

        return view("{$noteGroup}.index", [
            'group' => $noteGroup,
            'slug' => $resolvedSlug,
            'notes' => $notes,
            'filters' => $filters,
            'listTitle' => $listTitle,
            'writeUrl' => $resolvedSlug !== '' && $canCreate ? route("{$noteGroup}.create", ['slug' => $resolvedSlug]) : null,
            'initialPayload' => $initialPayload,
        ]);
    }

    /**
     * 상세내역 
     *
     * @param Request $request
     * @param string $slug
     * @param string $idx
     * 
     * @return View 
     */
    public function show(Request $request, string $slug, string $idx)
    {
        $noteGroup = $request->route('group');
        $note = $this->noteService->getNoteDetail($noteGroup, $slug, $idx);
        $contentHtml = $this->noteService->toRenderableHtml($note->content ?? '');
        
        $metaTitle = (string) ($note->subject ?? '상세내역');
        $plainContent = trim((string) preg_replace('/\s+/u', ' ', strip_tags($contentHtml)));
        $metaDescription = $plainContent !== '' ? Str::limit($plainContent, 160) : '상세내역';
        $metaImage = ! empty($note->thumbnail_path)
            ? url(Storage::url((string) $note->thumbnail_path))
            : asset('images/og/001.png');

        if ($request->ajax()) {
            return response()->json($this->buildNoteDetailResponse($request, $noteGroup, $slug, $note, $contentHtml));
        }

        return view("{$noteGroup}.show", [
            'group' => $noteGroup,
            'slug' => $slug,
            'note' => $note,
            'contentHtml' => $contentHtml,
            'useFlag' => $note->use_flag ?? 'N',
            'metaTitle' => $metaTitle,
            'metaDescription' => $metaDescription,
            'metaImage' => $metaImage,
        ]);
    }

    /**
     * 등록 폼
     *
     * @param Request $request
     * @param string $slug
     * 
     * @return View
     */
    public function create(Request $request, string $slug) : View
    {
        $noteGroup = $request->route('group');
        $this->authorize('create', Note::class);
        $topics = $this->noteService->getNoteTopics($noteGroup, $slug);

        return view("{$noteGroup}.create", [
            'group' => $noteGroup,
            'slug' => $slug,
            'note' => null,
            'formAction' => route("{$noteGroup}.store", ['slug' => $slug]),
            'topics' => $topics,
            'isEditMode' => false,
            'hasSavedThumbnail' => false,
            'savedThumbnailPath' => '',
            'savedThumbnailName' => '',
            'savedThumbnailUrl' => null,
            'initialTagsValue' => old('tags', ''),
            'thumbnailDestroyUrl' => null,
            'tagsDestroyUrl' => null,
        ]);
    }

    /**
     * 등록 처리
     *
     * @param Request $request
     * @param string $idx
     * @return void
     */
    public function store(StoreNoteRequest $request, string $slug)
    {
        $this->authorize('create', Note::class);
        $routeName = (string) $request->route()?->getName();
        $noteGroup = strtok($routeName, '.');

        if (empty($noteGroup)) {
            abort(404);
        }

        $note = $this->noteService->createNote($request, $noteGroup, $slug);

        return to_route("{$noteGroup}.show", [
            'slug' => $slug,
            'idx' => $note->idx,
        ]);
    }
    
    /**
     * 수정 폼
     *
     * @return void
     */
    public function edit(Request $request, string $slug, string $idx) : View
    {
        $noteGroup = $request->route('group');
        $note = $this->noteService->getNote($noteGroup, $slug, (int) $idx);
        $this->authorize('update', $note);
        $topics = $this->noteService->getNoteTopics($noteGroup, $slug);
        $savedThumbnailPath = (string) ($note->thumbnail_path ?? '');
        $hasSavedThumbnail = $savedThumbnailPath !== '';
        $existingTags = ($note->tags ?? collect())->pluck('name')->values()->all();
        $initialTagsValue = old('tags', implode(',', $existingTags));

        return view("{$noteGroup}.create", [
            'group' => $noteGroup,
            'slug' => $slug,
            'note' => $note,
            'topics' => $topics,
            'formAction' => route("{$noteGroup}.update", ['slug' => $slug, 'idx' => $idx]),
            'isEditMode' => true,
            'hasSavedThumbnail' => $hasSavedThumbnail,
            'savedThumbnailPath' => $savedThumbnailPath,
            'savedThumbnailName' => $hasSavedThumbnail ? basename($savedThumbnailPath) : '',
            'savedThumbnailUrl' => $hasSavedThumbnail ? Storage::url($savedThumbnailPath) : null,
            'initialTagsValue' => $initialTagsValue,
            'thumbnailDestroyUrl' => route("{$noteGroup}.thumbnail.destroy", ['slug' => $slug, 'idx' => $note->idx]),
            'tagsDestroyUrl' => route("{$noteGroup}.tags.destroy", ['slug' => $slug, 'idx' => $note->idx]),
        ]);
    }

    /**
     * 수정 처리 
     *
     * @param Request $request
     * @return void
     */
    public function update(UpdateNoteRequest $request, string $slug, string $idx)
    {
        $noteGroup = $request->route('group');
        $note = $this->noteService->updateNote($request, $noteGroup, $slug, (int) $idx);

        return to_route("{$noteGroup}.show", [
            'slug' => $slug,
            'idx' => $note->idx,
        ]);
    }

    /**
     * 삭제 처리 (soft delete)
     *
     * @param string $idx
     * @return void
     */
    public function destroy(DestroyNoteRequest $request, string $slug, string $idx)
    {
        $noteGroup = (string) $request->route('group');
        $note = $this->noteService->getNote($noteGroup, $slug, (int) $idx);
        $this->noteService->deleteNote(
            $note,
            (int) (auth()->id() ?? 0),
            (string) $request->ip(),
            (string) ($request->userAgent() ?? '')
        );

        return response()->json([
            'message' => '삭제되었습니다.',
        ]);
    }

    /**
     * 썸네일 삭제 (AJAX)
     *
     * @param Request $request
     * @param String $idx
     * @return void
     */
    public function destroyThumbnail(DestroyNoteThumbnailRequest $request, string $slug, string $idx)
    {
        $noteGroup = (string) $request->route('group');
        $note = $this->noteService->getNote($noteGroup, $slug, (int) $idx);
        $this->noteService->destroyNoteThumbnail(
            $note,
            (int) (auth()->id() ?? 0),
            (string) $request->ip(),
            (string) ($request->userAgent() ?? '')
        );

        return response()->json([
            'message' => '썸네일이 삭제되었습니다.',
            'thumbnail_path' => null,
        ]);
    }

    /**
     * 노트 해시태그 삭제 (AJAX)
     *
     * @param DestroyNoteTagRequest $request
     * @param string $slug
     * @param string $idx
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyTag(DestroyNoteTagRequest $request, string $slug, string $idx)
    {
        $noteGroup = (string) $request->route('group');
        $note = $this->noteService->getNote($noteGroup, $slug, (int) $idx);
        $deletedCount = $this->noteService->destroyNoteTag(
            $note,
            (string) $request->input('tag'),
            (int) (auth()->id() ?? 0),
            (string) $request->ip(),
            (string) ($request->userAgent() ?? '')
        );

        return response()->json([
            'message' => '태그가 삭제되었습니다.',
            'deleted_count' => $deletedCount,
        ]);
    }

    /**
     * 노트 공개여부 설정
     *
     * @param Request $request
     * @param string $slug
     * @param string $idx
     * @return void
     */
    public function updateUseFlag(UpdateNoteUseFlagRequest $request, string $slug, string $idx)
    {
        $noteGroup = (string) $request->route('group');
        $note = $this->noteService->getNote($noteGroup, $slug, (int) $idx);
        $updatedNote = $this->noteService->updateNoteUseFlag(
            $note,
            (int) (auth()->id() ?? 0),
            (string) $request->ip(),
            (string) ($request->userAgent() ?? '')
        );

        return response()->json([
            'use_flag' => $updatedNote->use_flag,
        ]);
    }

    private function buildNoteListResponse(string $group, string $slug, $notes, array $filters): array
    {
        if (! $notes) {
            return [
                'items' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                    'has_more' => false,
                ],
                'filters' => $filters,
            ];
        }

        $items = $notes->getCollection()->map(function (Note $note) use ($group, $slug) {
            $content = trim((string) ($note->content ?? ''));
            $plainContent = preg_replace('/\s+/', ' ', trim(strip_tags($content))) ?? '';
            $thumbnailUrl = ! empty($note->thumbnail_path)
                ? Storage::url((string) $note->thumbnail_path)
                : asset('images/no_image.png');

            return [
                'idx' => (int) $note->idx,
                'subject' => (string) ($note->subject ?? ''),
                'group_topic_name' => (string) ($note->group_topic_name ?? '-'),
                'create_datetime' => $note->create_datetime?->format('Y-m-d H:i:s') ?? '-',
                'desc' => Str::limit($plainContent, 120),
                'thumbnail_url' => $thumbnailUrl,
                'show_url' => route("{$group}.show", [
                    'slug' => $slug !== '' ? $slug : (string) ($note->categories_code ?? ''),
                    'idx' => $note->idx,
                ]),
            ];
        })->values()->all();

        return [
            'items' => $items,
            'pagination' => [
                'current_page' => $notes->currentPage(),
                'last_page' => $notes->lastPage(),
                'per_page' => $notes->perPage(),
                'total' => $notes->total(),
                'has_more' => $notes->hasMorePages(),
            ],
            'filters' => $filters,
        ];
    }

    /**
     * 목록 팝업/상세 AJAX 응답용 노트 상세 페이로드 생성
     *
     * @return array<string, mixed>
     */
    private function buildNoteDetailResponse(Request $request, string $group, string $slug, Note $note, string $contentHtml): array
    {
        $user = $request->user();
        $canUpdate = $user ? $user->can('update', $note) : false;
        $canDelete = $user ? $user->can('delete', $note) : false;
        $canUpdateUseFlag = $user ? $user->can('updateUseFlag', $note) : false;

        return [
            'note' => [
                'idx' => $note->idx,
                'subject' => $note->subject ?? '',
                'group_topic_name' => $note->group_topic_name ?? '-',
                'create_datetime' => $note->create_datetime?->format('Y-m-d H:i:s') ?? '-',
                'content_html' => $contentHtml,
                'use_flag' => $note->use_flag ?? 'N',
                'use_flag_label' => config("const.use_flag.{$note->use_flag}", '-'),
                'tags' => ($note->tags ?? collect())
                    ->pluck('name')
                    ->filter(static fn ($name) => is_string($name) && trim($name) !== '')
                    ->values()
                    ->all(),
            ],
            'actions' => [
                'show_url' => route("{$group}.show", ['slug' => $slug, 'idx' => $note->idx]),
                'edit_url' => route("{$group}.edit", ['slug' => $slug, 'idx' => $note->idx]),
                'delete_url' => route("{$group}.soft.delete", ['slug' => $slug, 'idx' => $note->idx]),
                'use_flag_url' => route("{$group}.use_flag.update", ['slug' => $slug, 'idx' => $note->idx]),
            ],
            'permissions' => [
                'can_update' => $canUpdate,
                'can_delete' => $canDelete,
                'can_update_use_flag' => $canUpdateUseFlag,
            ],
        ];
    }
}
