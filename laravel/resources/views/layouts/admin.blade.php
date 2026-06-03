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
            <a href="{{ route('admin.module', 'roles') }}">سطوح دسترسی</a>
            <a href="{{ route('admin.module', 'menus') }}">منوهای پویا</a>
            <a href="{{ route('admin.module', 'contents') }}">اخبار و محتوا</a>
            <a href="{{ route('admin.module', 'guilds') }}">اتحادیه‌ها و اعضا</a>
            <a href="{{ route('admin.module', 'sms_messages') }}">پیامک اطلاع‌رسانی</a>
            <a href="{{ route('admin.module', 'home_sections') }}">صفحه اصلی</a>
            <a href="{{ route('admin.module', 'advertisements') }}">تبلیغات</a>
        </nav>
    </aside>
    <main class="admin-main">@yield('content')</main>
</body>
</html>
