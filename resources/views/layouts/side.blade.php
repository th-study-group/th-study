<div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mobileSidebarLabel">메뉴</h5>
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