@extends('layouts.admin')
@section('content')
<header class="admin-header">
    <div><span>{{ $jalaliDate }}</span><h1>داشبورد مدیریت</h1><p>خلاصه وضعیت سایت؛ مدیریت جزئیات از منوی سمت راست انجام می‌شود.</p></div>
    <a class="admin-primary" href="{{ route('home') }}">مشاهده سایت</a>
</header>

<section class="admin-stats">
    <article><strong>{{ \App\Support\JalaliDate::faNumber(count($news)) }}</strong><span>محتوا</span></article>
    <article><strong>{{ \App\Support\JalaliDate::faNumber(count($guilds)) }}</strong><span>اتحادیه</span></article>
    <article><strong>{{ \App\Support\JalaliDate::faNumber(count($roles)) }}</strong><span>نقش</span></article>
    <article><strong>{{ \App\Support\JalaliDate::faNumber(count($homeSections)) }}</strong><span>سکشن صفحه اصلی</span></article>
</section>

<section class="admin-grid">
    <article class="admin-panel wide"><h2>دسترسی سریع</h2><div class="admin-module-grid">
        @foreach($modules as $key => $module)
            <a class="system-row" href="{{ route('admin.module', $key) }}"><strong>{{ $module['title'] }}</strong><span>مدیریت</span></a>
        @endforeach
    </div></article>
    <article class="admin-panel"><h2>چرخه انتشار</h2>@foreach($workflow as $step)<div class="workflow-step"><strong>{{ $step['step'] }}</strong><p>{{ $step['description'] }}</p></div>@endforeach</article>
</section>
@endsection
