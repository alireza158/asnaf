@extends('layouts.admin')
@section('content')
<header class="admin-header">
    <div><span>{{ $jalaliDate }}</span><h1>داشبورد مدیریت حرفه‌ای اتاق اصناف</h1><p>طراحی رسمی، سریع و روان برای مدیریت محتوای سایت، اتحادیه‌ها، دسترسی‌ها و چرخه تایید مدیرکل.</p></div>
    <a class="admin-primary" href="/">مشاهده سایت</a>
</header>

<section class="admin-stats">
    <article><strong>{{ \App\Support\JalaliDate::faNumber(count($news)) }}</strong><span>محتوا و خبر</span></article>
    <article><strong>{{ \App\Support\JalaliDate::faNumber(count($guilds)) }}</strong><span>اتحادیه فعال</span></article>
    <article><strong>{{ \App\Support\JalaliDate::faNumber(count($roles)) }}</strong><span>نقش قابل تعریف</span></article>
    <article><strong>{{ \App\Support\JalaliDate::faNumber(count($homeSections)) }}</strong><span>سکشن صفحه اصلی</span></article>
</section>

<section class="admin-grid" id="roles">
    <article class="admin-panel wide"><h2>سطوح دسترسی قابل تعریف</h2><p>هر نقش می‌تواند به کاربران نسبت داده شود و دسترسی به منوهای پنل، تایید محتوا، پیامک و اتحادیه‌ها را کنترل کند.</p><div class="role-list">@foreach($roles as $role)<div><h3>{{ $role['name'] }}</h3>@foreach($role['permissions'] as $permission)<span>{{ $permission }}</span>@endforeach</div>@endforeach</div></article>
    <article class="admin-panel"><h2>چرخه انتشار</h2>@foreach($workflow as $step)<div class="workflow-step"><strong>{{ $step['step'] }}</strong><p>{{ $step['description'] }}</p></div>@endforeach</article>
</section>

<section class="admin-grid" id="menus">
    <article class="admin-panel wide"><h2>منوساز مشابه وردپرس</h2><p>عنوان، لینک داخلی/خارجی، والد، ترتیب، وضعیت نمایش و محل نمایش منو از این بخش تنظیم می‌شود.</p><div class="sortable-demo">@foreach($menus['primary'] as $item)<div draggable="true"><span>↕</span><strong>{{ $item['title'] }}</strong><small>{{ $item['url'] }}</small></div>@endforeach</div></article>
    <article class="admin-panel"><h2>هدر و فوتر</h2><form class="admin-form"><label>شماره تماس<input value="{{ $site['phone'] }}"></label><label>متن بالای سایت<textarea>{{ $site['tagline'] }}</textarea></label><button type="button">ذخیره پیش‌نویس</button></form></article>
</section>

<section class="admin-grid" id="home-builder">
    <article class="admin-panel wide"><h2>صفحه‌ساز صفحه اصلی</h2><p>مدیر می‌تواند سکشن‌ها را فعال/غیرفعال کند، جابه‌جا کند و محتوای هر بخش را تغییر دهد.</p><div class="section-builder">@foreach($homeSections as $section)<div><span>☰</span><strong>{{ $section['title'] }}</strong><em>{{ $section['enabled'] ? 'فعال' : 'غیرفعال' }}</em></div>@endforeach</div></article>
    <article class="admin-panel" id="ads"><h2>مدیریت تبلیغات</h2>@foreach($ads as $ad)<div class="workflow-step"><strong>{{ $ad['title'] }}</strong><p>{{ $ad['position'] }} · {{ $ad['active'] ? 'فعال' : 'غیرفعال' }}</p></div>@endforeach</article>
</section>

<section class="admin-grid" id="content">
    <article class="admin-panel wide"><h2>مدیریت خبر، اطلاعیه، گالری و ویدیو</h2><div class="content-table">@foreach($news as $item)<div><strong>{{ $item['title'] }}</strong><span>{{ $item['type'] }}</span><span>{{ $item['approval'] }}</span><span>{{ count($item['gallery']) }} تصویر</span></div>@endforeach</div><div class="editor-shell"><div class="editor-toolbar"><button>B</button><button>لینک</button><button>تصویر</button><button>ویدیو آپارات</button><button>آپلود مستقیم</button></div><div contenteditable="true" class="editor-area">متن خبر، اطلاعیه یا صفحه داخلی را اینجا وارد کنید...</div></div></article>
    <article class="admin-panel"><h2>لیست سامانه‌ها</h2>@foreach($systems as $system)<a class="system-row" href="{{ $system['url'] }}">{{ $system['title'] }}</a>@endforeach</article>
</section>

<section class="admin-grid" id="guilds">
    <article class="admin-panel wide"><h2>اتحادیه‌ها، اعضا و شکایت اختیاری</h2><div class="content-table">@foreach($guilds as $guild)<div><strong>{{ $guild['title'] }}</strong><span>{{ $guild['category'] }}</span><span>{{ \App\Support\JalaliDate::faNumber($guild['members_count']) }} عضو</span><span>{{ $guild['complaints_enabled'] ? 'شکایت فعال' : 'شکایت غیرفعال' }}</span></div>@endforeach</div></article>
    <article class="admin-panel" id="sms"><h2>پیامک اطلاع‌رسانی اعضا</h2><form class="admin-form"><label>انتخاب اتحادیه<select>@foreach($guilds as $guild)<option>{{ $guild['title'] }}</option>@endforeach</select></label><label>گیرنده<select><option>همه اعضای اتحادیه</option><option>شخص خاص</option></select></label><label>متن پیامک<textarea>متن پیامک اطلاع‌رسانی...</textarea></label><button type="button">ارسال آزمایشی</button></form></article>
</section>
@endsection
