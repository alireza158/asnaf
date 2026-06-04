@extends('layouts.site')
@section('title', 'ثبت‌نام کاربر | '.$site['name'])
@section('content')
<header class="page-header-alt"><div class="site-container"><h1>ثبت‌نام کاربر پنل</h1><p>پس از ثبت‌نام، مدیرکل می‌تواند نقش و سطح دسترسی مناسب را برای کاربر تعریف کند.</p></div></header>
<main class="site-container asnaf-page">
    <form class="admin-form public-form auth-card" method="post" action="{{ route('register.store') }}">@csrf
        @if($errors->any())<div class="tracking-box auth-error">{{ $errors->first() }}</div>@endif
        <label>نام و نام خانوادگی<input name="name" value="{{ old('name') }}" required></label>
        <label>ایمیل<input type="email" name="email" value="{{ old('email') }}" required></label>
        <label>رمز عبور<input type="password" name="password" required></label>
        <label>تکرار رمز عبور<input type="password" name="password_confirmation" required></label>
        <button class="admin-primary">ثبت‌نام</button>
        <p>قبلاً حساب ساخته‌اید؟ <a class="howto-link" href="{{ route('login') }}">وارد شوید ←</a></p>
    </form>
</main>
@endsection
