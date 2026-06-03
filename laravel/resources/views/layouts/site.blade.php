<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $site['name'])</title>
    <meta name="description" content="{{ $site['tagline'] }}">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" rel="stylesheet">
    <link href="{{ asset('theme/assets/css/styles.css') }}" rel="stylesheet">
</head>
<body>
<header class="site-header">
    <div class="header-top site-container">
        <div class="brand-note">
            <img class="flag-img" src="{{ asset('theme/assets/img/flag-iran.png') }}" alt="پرچم ایران">
            <div>
                <span>{{ $jalaliDate }}</span>
                <strong>{{ $site['tagline'] }}</strong>
            </div>
        </div>
        <div class="header-left-actions" aria-label="راه‌های دسترسی سریع هدر">
            @foreach($topLinks as $link)
                @if($link['style'] === 'contact')
                    <a class="header-contact-card" href="{{ $link['url'] }}"><span>{{ $link['title'] }}</span><strong>{{ $link['subtitle'] }}</strong></a>
                @else
                    <a class="header-service-pill" href="{{ $link['url'] }}">{{ $link['title'] }}</a>
                @endif
            @endforeach
        </div>
    </div>
    <div aria-hidden="true" class="black-rail site-container"></div>
    <nav aria-label="منوی اصلی" class="navbar navbar-expand-lg main-navbar site-container">
        <button class="navbar-toggler" type="button" data-bs-target="#mainNav" data-bs-toggle="collapse" aria-controls="mainNav" aria-expanded="false" aria-label="باز کردن منو"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 nav-dots top-nav-menu">
                @foreach($menus['primary'] as $item)
                    @if(count($item['children']))
                        <li class="nav-item top-nav-item has-top-submenu">
                            <button class="nav-link top-nav-link" type="button" aria-expanded="false">{{ $item['title'] }}<span class="top-submenu-caret"></span></button>
                            <ul class="top-submenu">
                                @foreach($item['children'] as $child)
                                    <li><a href="{{ $child['url'] }}">{{ $child['title'] }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link @if(request()->path() === ltrim($item['url'], '/')) active @endif" href="{{ $item['url'] }}">{{ $item['title'] }}</a></li>
                    @endif
                @endforeach
            </ul>
        </div>
        <button aria-controls="headerSearchPanel" aria-expanded="false" aria-label="جستجو در سایت" class="search-trigger" type="button"><span class="visually-hidden">جستجو</span></button>
    </nav>
    <div class="header-search-panel site-container" hidden id="headerSearchPanel">
        <form action="{{ route('search') }}" class="header-search-form" role="search">
            <label class="header-search-label" for="siteSearchInput">جستجو در کل وب‌سایت</label>
            <div class="header-search-field">
                <input id="siteSearchInput" name="q" placeholder="اتحادیه، پروانه کسب، شکایت، آموزش..." type="search">
                <button type="submit">جستجو</button>
            </div>
            <div aria-live="polite" class="header-search-results"></div>
        </form>
    </div>
</header>

@yield('content')

<footer class="site-footer">
    <div class="site-container">
        <div class="footer-main">
            <div class="footer-col footer-brand-col">
                <img alt="{{ $site['name'] }}" src="{{ asset($site['footer_logo']) }}">
                <p>{{ $site['name'] }} به عنوان نماینده جامعه صنفی شهرستان، پشتیبان کسب‌وکارهای صنفی، ناظر بر فعالیت اتحادیه‌ها و تسهیل‌گر تعامل با دستگاه‌های اجرایی است.</p>
            </div>
            <div class="footer-col"><h4>دسترسی سریع</h4><ul>@foreach($menus['footer'] as $link)<li><a href="{{ $link['url'] }}">{{ $link['title'] }}</a></li>@endforeach</ul></div>
            <div class="footer-col"><h4>اطلاعات تماس</h4>
                <div class="footer-contact-item"><span class="fc-icon">📍</span><span>{{ $site['address'] }}</span></div>
                <div class="footer-contact-item"><span class="fc-icon">📞</span><span>{{ $site['phone'] }}<br>{{ $site['phone_alt'] }}</span></div>
                <div class="footer-contact-item"><span class="fc-icon">✉️</span><span>{{ $site['email'] }}</span></div>
            </div>
        </div>
        <div class="footer-divider"></div>
        <div class="footer-orgs">@foreach($systems as $system)<a href="{{ $system['url'] }}">{{ $system['title'] }}</a>@endforeach</div>
        <div class="footer-divider"></div>
        <div class="footer-bottom"><p>تمام حقوق برای {{ $site['name'] }} محفوظ است.</p><a href="{{ route('admin.dashboard') }}">ورود به پنل مدیریت</a></div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="{{ asset('theme/assets/js/main.js') }}"></script>
</body>
</html>
