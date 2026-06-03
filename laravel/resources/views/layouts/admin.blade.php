<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'پنل مدیریت اتاق اصناف')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="{{ asset('theme/assets/css/styles.css') }}" rel="stylesheet">
</head>
<body class="admin-body bg-light">
    <div class="admin-shell d-flex min-vh-100 w-100">
        <aside class="admin-sidebar-bootstrap bg-dark text-white shadow-lg">
            <div class="p-4 border-bottom border-light border-opacity-10">
                <div class="fs-5 fw-black">پنل مدیریت</div>
                <div class="small text-white-50 mt-1">اتاق اصناف گرگان</div>
            </div>
            <nav class="p-3 admin-bootstrap-nav">
                <a class="btn btn-outline-light w-100 text-end mb-3 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">داشبورد</a>
                <div class="accordion accordion-flush admin-sidebar-accordion" id="adminSidebarAccordion">
                    @foreach(adminModuleGroups() as $groupTitle => $groupModules)
                        @php($accordionId = 'admin-group-'.\Illuminate\Support\Str::slug($groupTitle).'-'.$loop->index)
                        <div class="accordion-item bg-transparent border-0 mb-2">
                            <h2 class="accordion-header"><button class="accordion-button collapsed bg-white bg-opacity-10 text-white rounded-3 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $accordionId }}">{{ $groupTitle }}</button></h2>
                            <div id="{{ $accordionId }}" class="accordion-collapse collapse" data-bs-parent="#adminSidebarAccordion">
                                <div class="accordion-body p-2">
                                    <div class="list-group list-group-flush rounded-3 overflow-hidden">
                                        @foreach($groupModules as $moduleKey => $module)
                                            <a class="list-group-item list-group-item-action bg-transparent text-white-75 border-light border-opacity-10 d-flex justify-content-between align-items-center {{ request()->route('module') === $moduleKey ? 'active' : '' }}" href="{{ route('admin.module', $moduleKey) }}">
                                                <span>{{ $module['title'] }}</span><small class="badge text-bg-light text-dark">مدیریت</small>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-grid gap-2 mt-4">
                    <a class="btn btn-light" href="{{ route('home') }}">مشاهده سایت</a>
                    <form method="post" action="{{ route('logout') }}">@csrf<button class="btn btn-danger w-100" type="submit">خروج</button></form>
                </div>
            </nav>
        </aside>
        <main class="admin-main flex-grow-1 p-4 p-lg-5">@yield('content')</main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
