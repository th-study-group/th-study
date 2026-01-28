@if (request()->routeIs($sideMenuAuth))
    <div class="offcanvas offcanvas-start text-bg-dark d-lg-none mt-0" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel" data-bs-scroll="false">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="mobileSidebarLabel">메뉴</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body no-scrollbar">
            <nav class="nav flex-column gap-2">
                @foreach ($sideMenus as $route => $menu)
                    @php
                        $params = $menu['params'] ?? [];
                        $hasParams = empty($params) || !in_array(null, $params, true);
                        $menuLevel = $menu['level'] ?? null;
                        $isAllowed = $menuLevel === null || $menuLevel === $sideMenuFlag;
                        $isActive = request()->routeIs($route);
                    @endphp

                    @if ($hasParams && $isAllowed)
                        <a class="nav-link text-white {{ $isActive ? 'active' : '' }}" href="{{ route($route, $params) }}">
                            {{ $menu['title'] }}
                        </a>
                    @endif
                @endforeach
            </nav>
        </div>
    </div>

    <aside class="col-lg-2 sidebar-col d-none d-lg-block">
        <div class="sidebar-panel text-white rounded-3 p-4">
            <h6 class="text-uppercase text-secondary small">메뉴</h6>
            <nav class="nav flex-column gap-2 mt-3">
                @foreach ($sideMenus as $route => $menu)
                    @php
                        $params = $menu['params'] ?? [];
                        $hasParams = empty($params) || !in_array(null, $params, true);
                        $menuLevel = $menu['level'] ?? null;
                        $isAllowed = $menuLevel === null || $menuLevel === $sideMenuFlag;
                        $isActive = request()->routeIs($route);
                    @endphp

                    @if ($hasParams && $isAllowed)
                        <a class="nav-link text-white {{ $isActive ? 'active' : '' }}" href="{{ route($route, $params) }}">
                            {{ $menu['title'] }}
                        </a>
                    @endif
                @endforeach
            </nav>
        </div>
    </aside>
@endif
