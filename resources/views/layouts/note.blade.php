@if (request()->route('showSide'))
    <div class="offcanvas offcanvas-start text-bg-dark d-lg-none mt-0" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="mobileSidebarLabel">노트</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <nav class="nav flex-column gap-2">
                @foreach($sideNotes as $note)
                    <a class="nav-link text-white" href="{{ $note['url'] }}">{{ $note['title'] }}</a>
                @endforeach
            </nav>
        </div>
    </div>

    <aside class="col-lg-2 sidebar-col d-none d-lg-block">
        <div class="sidebar-panel text-white rounded-3 p-4 h-100">
            <h6 class="text-uppercase text-secondary small">노트</h6>
            <nav class="nav flex-column gap-2 mt-3">
                @foreach($sideNotes as $note)
                    <a class="nav-link text-white" href="{{ $note['url'] }}">{{ $note['title'] }}</a>
                @endforeach
            </nav>
        </div>
    </aside>
@endif
