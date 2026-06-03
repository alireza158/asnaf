@extends('layouts.admin')
@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div><span class="badge text-bg-primary mb-2">{{ $jalaliDate }}</span><h1 class="h3 fw-black mb-1">داشبورد مدیریت</h1><p class="text-muted mb-0">نمای کلی سیستم؛ همه مدیریت‌ها از سایدبار گروه‌بندی‌شده و لینک‌های خودکار انجام می‌شود.</p></div>
        <a class="btn btn-primary rounded-pill px-4" href="{{ route('home') }}">مشاهده سایت</a>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3"><div class="card border-0 shadow-sm rounded-4"><div class="card-body"><div class="fs-3 fw-black text-primary">{{ \App\Support\JalaliDate::faNumber(count($news)) }}</div><div class="text-muted">محتوا</div></div></div></div>
        <div class="col-6 col-xl-3"><div class="card border-0 shadow-sm rounded-4"><div class="card-body"><div class="fs-3 fw-black text-success">{{ \App\Support\JalaliDate::faNumber(count($guilds)) }}</div><div class="text-muted">اتحادیه</div></div></div></div>
        <div class="col-6 col-xl-3"><div class="card border-0 shadow-sm rounded-4"><div class="card-body"><div class="fs-3 fw-black text-warning">{{ \App\Support\JalaliDate::faNumber(count($roles)) }}</div><div class="text-muted">نقش</div></div></div></div>
        <div class="col-6 col-xl-3"><div class="card border-0 shadow-sm rounded-4"><div class="card-body"><div class="fs-3 fw-black text-danger">{{ \App\Support\JalaliDate::faNumber(count($homeSections)) }}</div><div class="text-muted">سکشن اصلی</div></div></div></div>
    </div>
    <div class="row g-4">
        @foreach(adminModuleGroups() as $groupTitle => $groupModules)
            <div class="col-xl-6"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-header bg-white border-0 pt-4 px-4"><h2 class="h5 fw-bold mb-0">{{ $groupTitle }}</h2></div><div class="card-body p-4 pt-2"><div class="row g-2">
                @foreach($groupModules as $moduleKey => $module)
                    <div class="col-sm-6"><a class="btn btn-outline-primary w-100 text-end rounded-3" href="{{ route('admin.module', $moduleKey) }}">{{ $module['title'] }}</a></div>
                @endforeach
            </div></div></div></div>
        @endforeach
    </div>
</div>
@endsection
