@extends('layouts.site')

@section('content')
<main>
    <section class="hero-section site-container">
        <div class="hero-layout">
            <aside class="quick-menu-panel">
                <div class="quick-menu-header">دسترسی‌های پویا</div>
                <ul class="quick-menu-list">
                    @foreach($menus['quick'] as $item)
                        <li class="quick-menu-item"><a class="quick-menu-link" href="{{ $item['url'] }}"><span>{{ $item['icon'] }}</span>{{ $item['title'] }}</a></li>
                    @endforeach
                </ul>
            </aside>
            <div class="hero-news-grid">
                @foreach($importantNews->take(3) as $index => $item)
                    <article class="news-card {{ $index === 0 ? 'main-card' : 'side-card' }}">
                        <img alt="{{ $item['title'] }}" src="{{ asset($item['gallery'][0] ?? $site['hero_image']) }}">
                        <div class="news-overlay"></div>
                        <div class="news-content">
                            <span class="news-badge">{{ $item['type'] }} مهم</span>
                            <h2><a href="{{ route('news.show', $item['slug']) }}">{{ $item['title'] }}</a></h2>
                            <p>{{ $item['summary'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    @foreach($homeSections as $section)
        @continue(! $section['enabled'])
        @switch($section['key'])
            @case('services')
                <section class="site-container howto-section" id="services">
                    <div class="section-heading section-heading-centered"><h2>{{ $section['title'] }}</h2><p>هر خدمت به صفحه توضیحات و ادیتور محتوای اختصاصی متصل است.</p></div>
                    <div class="howto-grid">
                        @foreach($services as $service)
                            <a class="howto-card" href="{{ route('services.show', $service['slug']) }}"><div class="howto-icon">{{ $service['icon'] }}</div><h3>{{ $service['title'] }}</h3><p>{{ $service['summary'] }}</p><span class="howto-link">مشاهده راهنما ←</span></a>
                        @endforeach
                    </div>
                </section>
            @break

            @case('important_news')
                <section class="site-container asnaf-section" id="important-news">
                    <div class="section-heading"><h2>{{ $section['title'] }}</h2><p>اخبار، اطلاعیه‌ها و محتواها پس از تایید مدیرکل منتشر می‌شوند.</p></div>
                    <div class="asnaf-grid three-col">
                        @foreach($news as $item)
                            <article class="asnaf-card"><span class="admin-chip">{{ $item['approval'] }}</span><h3><a href="{{ route('news.show', $item['slug']) }}">{{ $item['title'] }}</a></h3><p>{{ $item['summary'] }}</p><small>{{ $item['published_at'] }} · {{ $item['type'] }}</small></article>
                        @endforeach
                    </div>
                </section>
            @break

            @case('guilds')
                <section class="site-container asnaf-section" id="guilds">
                    <div class="section-heading"><h2>{{ $section['title'] }}</h2><p>هر اتحادیه امکانات مستقل، شکایت اختیاری، اعضا و پیامک اختصاصی دارد.</p></div>
                    <div class="asnaf-grid three-col">
                        @foreach($guilds as $guild)
                            <article class="asnaf-card"><h3><a href="{{ route('guilds.show', $guild['slug']) }}">{{ $guild['title'] }}</a></h3><p>{{ $guild['summary'] }}</p><div class="meta-row"><span>{{ $guild['category'] }}</span><span>{{ \App\Support\JalaliDate::faNumber($guild['members_count']) }} عضو</span></div><div class="feature-list">@foreach($guild['features'] as $feature)<span>{{ $feature }}</span>@endforeach</div></article>
                        @endforeach
                    </div>
                </section>
            @break

            @case('commissions')
                <section class="site-container asnaf-section" id="commissions">
                    <div class="section-heading"><h2>{{ $section['title'] }}</h2><p>صفحات کمیسیون‌ها، توضیحات و جلسات قابل مدیریت هستند.</p></div>
                    <div class="asnaf-grid three-col">@foreach($commissions as $commission)<article class="asnaf-card"><h3>{{ $commission['title'] }}</h3><p>{{ $commission['summary'] }}</p><ul>@foreach($commission['meetings'] as $meeting)<li>{{ $meeting }}</li>@endforeach</ul></article>@endforeach</div>
                </section>
            @break

            @case('tourism')
                <section class="site-container asnaf-section" id="tourism">
                    <div class="section-heading"><h2>{{ $section['title'] }}</h2><p>صفحه گردشگری با دسته‌بندی و مکان‌های مستقل از پنل تنظیم می‌شود.</p></div>
                    <div class="media-grid">@foreach($tourism as $place)<a class="media-card" href="{{ route('tourism.show', $place['slug']) }}"><img src="{{ asset($place['image']) }}" alt="{{ $place['title'] }}"><div class="media-card-overlay"></div><div class="media-card-footer"><span>{{ $place['category'] }}</span><h3>{{ $place['title'] }}</h3></div></a>@endforeach</div>
                </section>
            @break

            @case('messages')
                <section class="site-container asnaf-section" id="messages">
                    <div class="section-heading"><h2>{{ $section['title'] }}</h2><p>پیام تبریک یا اطلاع‌رسانی مدیران اتحادیه‌ها نیز وارد چرخه تایید می‌شود.</p></div>
                    <div class="asnaf-grid two-col">@foreach($managerMessages as $message)<article class="asnaf-card"><span class="admin-chip">{{ $message['status'] }}</span><h3>{{ $message['title'] }}</h3><p>{{ $message['text'] }}</p><small>{{ $message['guild'] }}</small></article>@endforeach</div>
                </section>
            @break
        @endswitch
    @endforeach
</main>
@endsection
