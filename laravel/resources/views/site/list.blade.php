@extends('layouts.site')
@section('title', $title.' | '.$site['name'])
@section('content')
<header class="page-header-alt">
    <div class="site-container">
        <h1>{{ $title }}</h1>
        <p>{{ $description ?? 'این صفحه از داده‌های پویا و قابل مدیریت پنل ساخته شده است.' }}</p>
    </div>
</header>
<main class="site-container asnaf-page">
    <div class="asnaf-grid three-col">
        @forelse($items as $item)
            <article class="asnaf-card">
                @isset($item['image'])<img class="asnaf-card-img" src="{{ asset($item['image']) }}" alt="{{ $item['title'] }}">@endisset
                <span class="admin-chip">{{ $item['category'] ?? $item['type'] ?? $item['status'] ?? 'فعال' }}</span>
                <h3>{{ $item['title'] }}</h3>
                <p>{{ $item['summary'] ?? $item['description'] ?? $item['content'] ?? '' }}</p>
                @isset($item['url'])<a class="howto-link" href="{{ $item['url'] }}">ورود / مشاهده ←</a>@endisset
            </article>
        @empty
            <article class="asnaf-card"><h3>موردی برای نمایش وجود ندارد</h3><p>پس از ثبت اطلاعات از پنل مدیریت، این بخش به‌صورت خودکار تکمیل می‌شود.</p></article>
        @endforelse
    </div>
</main>
@endsection
