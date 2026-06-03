<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'پنل مدیریت اتاق اصناف')</title>
    <link href="{{ asset('theme/assets/css/styles.css') }}" rel="stylesheet">
</head>
<body class="admin-body">
    <aside class="admin-sidebar">
        <div class="admin-brand">پنل مدیریت<br><span>اتاق اصناف گرگان</span></div>
        <nav class="admin-nav">
            <a href="{{ route('admin.dashboard') }}">داشبورد</a>
            <div class="admin-nav-group"><span>ساختار سایت</span>
                <a href="{{ route('admin.module', 'site_settings') }}">تنظیمات سایت</a>
                <a href="{{ route('admin.module', 'menus') }}">منوهای پویا</a>
                <a href="{{ route('admin.module', 'home_sections') }}">صفحه اصلی</a>
                <a href="{{ route('admin.module', 'advertisements') }}">تبلیغات</a>
            </div>
            <div class="admin-nav-group"><span>محتوا</span>
                <a href="{{ route('admin.module', 'contents') }}">اخبار و اطلاعیه‌ها</a>
                <a href="{{ route('admin.module', 'service_pages') }}">خدمات الکترونیک</a>
                <a href="{{ route('admin.module', 'categories') }}">دسته‌بندی‌ها</a>
                <a href="{{ route('admin.module', 'systems') }}">سامانه‌ها</a>
            </div>
            <div class="admin-nav-group"><span>اتحادیه و خدمات</span>
                <a href="{{ route('admin.module', 'guilds') }}">اتحادیه‌ها</a>
                <a href="{{ route('admin.module', 'guild_members') }}">اعضای اتحادیه</a>
                <a href="{{ route('admin.module', 'complaints') }}">شکایات</a>
                <a href="{{ route('admin.module', 'sms_messages') }}">پیامک‌ها</a>
            </div>
            <div class="admin-nav-group"><span>کمیسیون و گردشگری</span>
                <a href="{{ route('admin.module', 'commissions') }}">کمیسیون‌ها</a>
                <a href="{{ route('admin.module', 'commission_meetings') }}">جلسات کمیسیون‌ها</a>
                <a href="{{ route('admin.module', 'tourism_places') }}">گردشگری</a>
            </div>
            <div class="admin-nav-group"><span>کاربران</span>
                <a href="{{ route('admin.module', 'roles') }}">سطوح دسترسی</a>
            </div>
            <a href="{{ route('home') }}">مشاهده سایت</a>
            <form method="post" action="{{ route('logout') }}">@csrf<button class="admin-logout" type="submit">خروج</button></form>
        </nav>
    </aside>
    <main class="admin-main">@yield('content')</main>
</body>
</html>
