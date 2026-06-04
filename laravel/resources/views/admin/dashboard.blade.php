@extends('layouts.admin')
@section('title', 'صفحه شروع مدیریت | پنل مدیریت')
@section('content')
<div class="container-fluid px-0">
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-4 p-lg-5 bg-white">
            <span class="badge text-bg-primary mb-3">{{ $jalaliDate }}</span>
            <h1 class="h2 fw-black mb-2">از اینجا شروع کنید</h1>
            <p class="lead text-muted mb-0">این پنل برای کاربر تازه‌کار ساده شده است: از کارت‌های زیر شروع کنید، هر بخش راهنمای کوتاه دارد، و همه لینک‌ها از منوی سمت راست آماده هستند.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach(adminQuickStart() as $item)
            <div class="col-md-6 col-xl-3">
                <a class="card border-0 shadow-sm rounded-4 h-100 text-decoration-none text-dark" href="{{ route('admin.module', $item['module']) }}">
                    <div class="card-body p-4">
                        <div class="badge text-bg-light mb-3">قدم {{ \App\Support\JalaliDate::faNumber($loop->iteration) }}</div>
                        <h2 class="h5 fw-bold">{{ $item['title'] }}</h2>
                        <p class="text-muted small mb-0">{{ $item['text'] }}</p>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3"><div class="card border-0 shadow-sm rounded-4"><div class="card-body"><div class="fs-3 fw-black text-primary">{{ \App\Support\JalaliDate::faNumber(count($news)) }}</div><div class="text-muted">خبر و اطلاعیه</div></div></div></div>
        <div class="col-6 col-xl-3"><div class="card border-0 shadow-sm rounded-4"><div class="card-body"><div class="fs-3 fw-black text-success">{{ \App\Support\JalaliDate::faNumber(count($guilds)) }}</div><div class="text-muted">اتحادیه</div></div></div></div>
        <div class="col-6 col-xl-3"><div class="card border-0 shadow-sm rounded-4"><div class="card-body"><div class="fs-3 fw-black text-warning">{{ \App\Support\JalaliDate::faNumber(count($roles)) }}</div><div class="text-muted">سطح دسترسی</div></div></div></div>
        <div class="col-6 col-xl-3"><div class="card border-0 shadow-sm rounded-4"><div class="card-body"><div class="fs-3 fw-black text-danger">{{ \App\Support\JalaliDate::faNumber(count($homeSections)) }}</div><div class="text-muted">بخش صفحه اصلی</div></div></div></div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h2 class="h4 fw-bold mb-3">همه بخش‌های پنل</h2>
            <div class="row g-3">
                @foreach(adminModuleGroups() as $groupTitle => $groupModules)
                    <div class="col-lg-4">
                        <div class="border rounded-4 p-3 h-100 bg-light">
                            <h3 class="h6 fw-bold mb-3">{{ $groupTitle }}</h3>
                            <div class="d-grid gap-2">
                                @foreach($groupModules as $moduleKey => $module)
                                    <a class="btn btn-outline-primary text-end" href="{{ route('admin.module', $moduleKey) }}">{{ $module['title'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
