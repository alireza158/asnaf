@extends('layouts.site')
@section('title', 'ثبت شکایت صنفی | '.$site['name'])
@section('content')
<main class="site-container asnaf-page">
    <div class="section-heading"><h1>ثبت شکایت از اتحادیه یا واحد صنفی</h1><p>پس از ثبت، کد رهگیری برای پیگیری‌های بعدی نمایش داده می‌شود.</p></div>
    @isset($trackingCode)<div class="tracking-box">کد رهگیری شما: <strong>{{ $trackingCode }}</strong></div>@endisset
    <form class="admin-form public-form" method="post" action="{{ route('complaints.store') }}">@csrf
        <label>اتحادیه مرتبط<select name="guild">@foreach($guilds as $guild)@if($guild['complaints_enabled'])<option>{{ $guild['title'] }}</option>@endif @endforeach</select></label>
        <label>نام و نام خانوادگی<input name="name" required></label>
        <label>شماره موبایل<input name="mobile" required></label>
        <label>شرح شکایت<textarea name="body" rows="6" required></textarea></label>
        <button class="admin-primary">ثبت و دریافت کد رهگیری</button>
    </form>
</main>
@endsection
