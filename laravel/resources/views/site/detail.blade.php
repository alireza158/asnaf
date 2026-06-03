@extends('layouts.site')
@section('title', $item['title'].' | '.$site['name'])
@section('content')
<main class="site-container asnaf-page">
    <article class="asnaf-detail-card">
        <span class="admin-chip">{{ $item['category'] ?? $item['type'] ?? $item['approval'] ?? 'صفحه پویا' }}</span>
        <h1>{{ $item['title'] }}</h1>
        <p class="lead">{{ $item['summary'] ?? $item['description'] ?? '' }}</p>
        <div class="content-box">{!! nl2br(e($item['content'] ?? $item['summary'] ?? 'محتوای این صفحه از پنل مدیریت و ادیتور قدرتمند قابل تکمیل است.')) !!}</div>
        @if(! empty($item['gallery']))
            <div class="media-grid detail-gallery">@foreach($item['gallery'] as $image)<img src="{{ asset($image) }}" alt="{{ $item['title'] }}">@endforeach</div>
        @endif
        @if(isset($item['features']))
            <h2>امکانات اتحادیه</h2><div class="feature-list">@foreach($item['features'] as $feature)<span>{{ $feature }}</span>@endforeach</div>
            <p><strong>ثبت شکایت:</strong> {{ $item['complaints_enabled'] ? 'برای این اتحادیه فعال است.' : 'به انتخاب اتحادیه غیرفعال است.' }}</p>
        @endif
    </article>
</main>
@endsection
