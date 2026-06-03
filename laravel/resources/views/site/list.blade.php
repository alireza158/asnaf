@extends('layouts.site')
@section('title', $title.' | '.$site['name'])
@section('content')
<main class="site-container asnaf-page">
    <div class="section-heading"><h1>{{ $title }}</h1><p>{{ $description ?? 'این صفحه از داده‌های پویا و قابل مدیریت پنل ساخته شده است.' }}</p></div>
    <div class="asnaf-grid three-col">
        @foreach($items as $item)
            <article class="asnaf-card">
                @isset($item['image'])<img class="asnaf-card-img" src="{{ asset($item['image']) }}" alt="{{ $item['title'] }}">@endisset
                <span class="admin-chip">{{ $item['category'] ?? $item['type'] ?? $item['status'] ?? 'فعال' }}</span>
                <h3>{{ $item['title'] }}</h3>
                <p>{{ $item['summary'] ?? $item['description'] ?? $item['content'] ?? '' }}</p>
                @isset($item['url'])<a class="howto-link" href="{{ $item['url'] }}">ورود / مشاهده ←</a>@endisset
            </article>
        @endforeach
    </div>
</main>
@endsection
