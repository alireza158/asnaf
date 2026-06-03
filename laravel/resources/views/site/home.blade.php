@extends('layouts.site')

@section('content')
<main>
    <section class="hero-section site-container">
        <div class="hero-grid">
            <aside class="quick-menu" aria-label="منوی دسترسی سریع">
                <ul class="quick-menu-list">
                    @foreach($menus['quick'] as $item)
                        <li class="quick-menu-item has-submenu">
                            <button aria-expanded="false" class="quick-menu-link" type="button">
                                <span>{{ $item['title'] }}</span><b></b>
                            </button>
                            <ul class="quick-submenu">
                                <li><a href="{{ $item['url'] }}">ورود به {{ $item['title'] }}</a></li>
                                <li><a href="{{ $item['url'] }}">راهنما و جزئیات</a></li>
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </aside>

            <div aria-label="اسلایدر خبرهای اصلی" class="hero-slider swiper" dir="ltr">
                <div class="swiper-wrapper">
                    @foreach($importantNews->take(4) as $item)
                        <article class="news-card news-card-main swiper-slide">
                            <img alt="{{ $item['title'] }}" src="{{ asset($item['gallery'][0] ?? $site['hero_image']) }}">
                            <div class="news-overlay"></div>
                            <div class="news-content">
                                <span class="news-kicker">{{ $item['type'] }} مهم</span>
                                <h1><a href="{{ route('news.show', $item['slug']) }}">{{ $item['title'] }}</a></h1>
                            </div>
                        </article>
                    @endforeach
                </div>
                <button aria-label="خبر بعدی" class="hero-slider-arrow hero-slider-next" type="button"></button>
                <button aria-label="خبر قبلی" class="hero-slider-arrow hero-slider-prev" type="button"></button>
                <div class="hero-slider-pagination"></div>
            </div>

            <div aria-label="خبرهای کناری" class="side-news">
                @foreach($news as $item)
                    @if($loop->index < 2)
                        <article class="news-card side-card">
                            <img alt="{{ $item['title'] }}" src="{{ asset($item['gallery'][0] ?? $site['hero_image']) }}">
                            <div class="news-overlay"></div>
                            <div class="news-content"><h2><a href="{{ route('news.show', $item['slug']) }}">{{ $item['title'] }}</a></h2></div>
                        </article>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <section class="access-panel site-container" aria-label="دسترسی‌های پرکاربرد">
        @foreach(array_slice($menus['quick'], 0, 4) as $index => $item)
            <a class="access-item" href="{{ $item['url'] }}">
                <span class="access-icon {{ ['document-plus', 'document-text', 'document-search', 'document-text'][$index] ?? 'document-text' }}"></span>
                <span>{{ $item['title'] }}</span>
            </a>
        @endforeach
    </section>

    @foreach($homeSections as $section)
        @continue(! $section['enabled'])
        @switch($section['key'])
            @case('services')
                <section class="site-container howto-section" id="services">
                    <div class="section-heading section-heading-centered"><h2>{{ $section['title'] }}</h2><p>نحوه انجام خدمات، دریافت مجوزها و ثبت درخواست‌ها</p></div>
                    <div class="howto-grid">
                        @foreach($services as $service)
                            <a class="howto-card" href="{{ route('services.show', $service['slug']) }}"><div class="howto-icon">{{ $service['icon'] }}</div><h3>{{ $service['title'] }}</h3><p>{{ $service['summary'] }}</p><span class="howto-link">مشاهده راهنما ←</span></a>
                        @endforeach
                    </div>
                </section>
                @if(! empty($ads))
                    <section class="home-ad-banners site-container">
                        @foreach(array_slice($ads, 0, 2) as $ad)
                            <a class="ad-banner" href="{{ $ad['url'] ?? '#' }}">
                                <img alt="{{ $ad['title'] }}" src="{{ asset($ad['image'] ?? $site['hero_image']) }}">
                                <div class="ad-banner-overlay"></div>
                                <div class="ad-banner-text">{{ $ad['title'] }}</div>
                            </a>
                        @endforeach
                    </section>
                @endif
            @break

            @case('important_news')
                <section class="asnaf-section section-white" id="important-news"><div class="site-container">
                    <div class="section-heading"><h2>{{ $section['title'] }}</h2><p>اخبار، اطلاعیه‌ها و محتواها پس از تایید مدیرکل منتشر می‌شوند.</p></div>
                    <div class="asnaf-grid three-col">
                        @foreach($news as $item)
                            <article class="asnaf-card"><span class="admin-chip">{{ $item['approval'] }}</span><h3><a href="{{ route('news.show', $item['slug']) }}">{{ $item['title'] }}</a></h3><p>{{ $item['summary'] }}</p><small>{{ $item['published_at'] }} · {{ $item['type'] }}</small></article>
                        @endforeach
                    </div>
                </div></section>
            @break

            @case('guilds')
                <section class="asnaf-section ds-tint-block" id="guilds"><div class="site-container">
                    <div class="section-heading"><h2>{{ $section['title'] }}</h2><p>هر اتحادیه امکانات مستقل، شکایت اختیاری، اعضا و پیامک اختصاصی دارد.</p></div>
                    <div class="asnaf-grid three-col">
                        @foreach($guilds as $guild)
                            <article class="asnaf-card"><h3><a href="{{ route('guilds.show', $guild['slug']) }}">{{ $guild['title'] }}</a></h3><p>{{ $guild['summary'] }}</p><div class="meta-row"><span>{{ $guild['category'] }}</span><span>{{ \App\Support\JalaliDate::faNumber($guild['members_count']) }} عضو</span></div><div class="feature-list">@foreach($guild['features'] as $feature)<span>{{ $feature }}</span>@endforeach</div></article>
                        @endforeach
                    </div>
                </div></section>
            @break

            @case('commissions')
                <section class="asnaf-section section-white" id="commissions"><div class="site-container">
                    <div class="section-heading"><h2>{{ $section['title'] }}</h2><p>صفحات کمیسیون‌ها، توضیحات و جلسات قابل مدیریت هستند.</p></div>
                    <div class="asnaf-grid three-col">@foreach($commissions as $commission)<article class="asnaf-card"><h3>{{ $commission['title'] }}</h3><p>{{ $commission['summary'] }}</p><ul>@foreach($commission['meetings'] as $meeting)<li>{{ $meeting }}</li>@endforeach</ul></article>@endforeach</div>
                </div></section>
            @break

            @case('tourism')
                <section class="asnaf-section section-gray" id="tourism"><div class="site-container">
                    <div class="section-heading"><h2>{{ $section['title'] }}</h2><p>صفحه گردشگری با دسته‌بندی و مکان‌های مستقل از پنل تنظیم می‌شود.</p></div>
                    <div class="media-grid">@foreach($tourism as $place)<a class="media-card" href="{{ route('tourism.show', $place['slug']) }}"><img src="{{ asset($place['image']) }}" alt="{{ $place['title'] }}"><div class="media-card-overlay"></div><div class="media-card-footer"><span>{{ $place['category'] }}</span><h3>{{ $place['title'] }}</h3></div></a>@endforeach</div>
                </div></section>
            @break

            @case('messages')
                <section class="asnaf-section section-white" id="messages"><div class="site-container">
                    <div class="section-heading"><h2>{{ $section['title'] }}</h2><p>پیام تبریک یا اطلاع‌رسانی مدیران اتحادیه‌ها نیز وارد چرخه تایید می‌شود.</p></div>
                    <div class="asnaf-grid two-col">@foreach($managerMessages as $message)<article class="asnaf-card"><span class="admin-chip">{{ $message['status'] }}</span><h3>{{ $message['title'] }}</h3><p>{{ $message['text'] }}</p><small>{{ $message['guild'] }}</small></article>@endforeach</div>
                </div></section>
            @break
        @endswitch
    @endforeach
</main>
@endsection
