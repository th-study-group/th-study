<?php

namespace App\Http\Controllers;

use App\Http\Requests\Notes\DestroyNoteRequest;
use App\Http\Requests\Notes\DestroyNoteTagRequest;
use App\Http\Requests\Notes\DestroyNoteThumbnailRequest;
use App\Http\Requests\Notes\StoreNoteRequest;
use App\Http\Requests\Notes\UpdateNoteRequest;
use App\Http\Requests\Notes\UpdateNoteUseFlagRequest;
use App\Models\Note;
use App\Services\NoteService;
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
    public function index(Request $request, ?string $slug = null) : View
    {
        $noteGroup = $request->route('group');
        $resolvedSlug = (string) ($slug ?? $request->route('slug', ''));
        $notes = $resolvedSlug !== '' ? $this->noteService->getNotes($noteGroup, $resolvedSlug) : null;

        return view("{$noteGroup}.index", [
            'group' => $noteGroup,
            'slug' => $resolvedSlug,
            'notes' => $notes,
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
    public function show(Request $request, string $slug, string $idx) : View
    {
        $noteGroup = $request->route('group');
        $note = $this->noteService->getNoteDetail($noteGroup, $slug, $idx);
        $contentHtml = $this->noteService->toRenderableHtml($note->content ?? '');

        return view("{$noteGroup}.show", [
            'group' => $noteGroup,
            'slug' => $slug,
            'note' => $note,
            'contentHtml' => $contentHtml,
            'useFlag' => $note->use_flag ?? 'N',
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
}
