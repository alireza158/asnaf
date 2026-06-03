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
        <nav>
            <a href="{{ route('admin.dashboard') }}">داشبورد</a>
            <a href="#roles">سطوح دسترسی</a>
            <a href="#menus">منوهای پویا</a>
            <a href="#content">اخبار و محتوا</a>
            <a href="#guilds">اتحادیه‌ها و اعضا</a>
            <a href="#sms">پیامک اطلاع‌رسانی</a>
            <a href="#home-builder">صفحه اصلی</a>
            <a href="#ads">تبلیغات</a>
        </nav>
    </aside>
    <main class="admin-main">@yield('content')</main>
</body>
</html>
