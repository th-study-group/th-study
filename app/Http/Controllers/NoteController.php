<?php

namespace App\Http\Controllers;

use App\Http\Requests\Notes\StoreNoteRequest;
use App\Models\Note;
use App\Services\NoteService;
use Illuminate\Http\Request;
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
        $noteType = $request->route('group');

        return view("{$noteType}.index");
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
        $noteType = $request->route('group');
        $note = $this->noteService->getNoteDetail($noteType, $slug, $idx);
        $contentHtml = $this->noteService->toRenderableHtml($note->content ?? '');

        return view("{$noteType}.show", [
            'group' => $noteType,
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
        $noteType = $request->route('group');
        $topics = $this->noteService->getNoteTopics($noteType, $slug);

        return view("{$noteType}.create", [
            'group' => $noteType,
            'slug' => $slug,
            'formAction' => route("{$noteType}.store", ['slug' => $slug]),
            'topics' => $topics,
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
        $routeName = $request->route()?->getName();
        $noteType = strtok($routeName, '.');

        if (empty($noteType)) {
            abort(404);
        }

        $note = $this->noteService->createNote($request, $noteType, $slug);

        return to_route("{$noteType}.show", [
            'slug' => $slug,
            'idx' => $note->idx,
        ]);
    }
    
    /**
     * 수정 폼
     *
     * @return void
     */
    public function edit(Request $request, string $idx) : View
    {
        $noteType = $request->route('group');

        return view("{$noteType}.create");
    }

    /**
     * 수정 처리 
     *
     * @param Request $request
     * @return void
     */
    public function update(Request $request)
    {
    }

    /**
     * 삭제 처리 (soft delete)
     *
     * @param string $idx
     * @return void
     */
    public function destroy(string $idx)
    {
    }

    /**
     * 썸네일 삭제 (AJAX)
     *
     * @param Request $request
     * @param String $idx
     * @return void
     */
    public function destroyThumbnail(Request $request, String $idx)
    {
    }
}
