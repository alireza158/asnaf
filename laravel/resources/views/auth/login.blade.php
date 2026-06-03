@extends('layouts.site')
@section('title', 'ورود به پنل مدیریت | '.$site['name'])
@section('content')
<header class="page-header-alt"><div class="site-container"><h1>ورود به پنل مدیریت</h1><p>برای مدیریت محتوا، منوها، اتحادیه‌ها و تنظیمات سایت وارد شوید.</p></div></header>
<main class="site-container asnaf-page">
    <form class="admin-form public-form auth-card" method="post" action="{{ route('login.store') }}">@csrf
        @if($errors->any())<div class="tracking-box auth-error">{{ $errors->first() }}</div>@endif
        <label>ایمیل<input type="email" name="email" value="{{ old('email') }}" required autofocus></label>
        <label>رمز عبور<input type="password" name="password" required></label>
        <label class="auth-check"><input type="checkbox" name="remember" value="1"> مرا به خاطر بسپار</label>
        <button class="admin-primary">ورود</button>
        <p>حساب ندارید؟ <a class="howto-link" href="{{ route('register') }}">ثبت‌نام کنید ←</a></p>
    </form>
</main>
@endsection
