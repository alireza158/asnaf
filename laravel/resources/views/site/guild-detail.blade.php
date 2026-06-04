@extends('layouts.site')
@section('title', $guild['title'].' | '.$site['name'])

@section('content')
<div class="guild-hero">
    <img alt="{{ $guild['title'] }}" class="guild-hero-bg" src="{{ asset($site['hero_image']) }}">
    <div class="site-container guild-hero-content">
        <div class="guild-hero-logo">{{ mb_substr($guild['title'], 0, 1) }}</div>
        <div class="guild-hero-text">
            <nav class="breadcrumb-nav" style="margin-bottom:10px">
                <a href="{{ route('home') }}" style="color:rgba(255,255,255,.6)">خانه</a>
                <span class="breadcrumb-sep">/</span>
                <a href="{{ route('guilds.index') }}" style="color:rgba(255,255,255,.6)">اتحادیه‌ها</a>
                <span class="breadcrumb-sep">/</span>
                <span style="color:rgba(255,255,255,.8)">{{ $guild['title'] }}</span>
            </nav>
            <h1>{{ $guild['title'] }}</h1>
            <p>{{ $guild['summary'] }}</p>
            <div class="guild-hero-stats">
                <span>اعضا: <strong>{{ \App\Support\JalaliDate::faNumber($guild['members_count']) }}</strong></span>
                <span>رسته: <strong>{{ $guild['category'] }}</strong></span>
                <span>شکایت: <strong>{{ $guild['complaints_enabled'] ? 'فعال' : 'غیرفعال' }}</strong></span>
                <span>آخرین بروزرسانی: <strong>{{ $jalaliDate }}</strong></span>
            </div>
        </div>
    </div>
</div>

<main>
    <div class="site-container guild-layout">
        <aside class="guild-side-nav">
            <h4>راهنمای سریع</h4>
            <ul>
                <li><a href="#guild-head">رییس اتحادیه</a></li>
                <li><a href="#guild-board">هیئت مدیره و اعضا</a></li>
                <li><a href="#guild-access">سطوح دسترسی</a></li>
                <li><a href="#guild-commissions">کمیسیون‌ها</a></li>
                <li><a href="#guild-rules">قوانین و دستورالعمل‌ها</a></li>
                <li><a href="#guild-slider">اسلایدر خبری</a></li>
                <li><a href="#guild-news">آخرین اخبار</a></li>
                <li><a href="#guild-articles">مقاله‌ها</a></li>
                <li><a href="#guild-prices">نرخ نامه</a></li>
                <li><a href="#guild-complaint">ثبت شکایت صنفی</a></li>
                <li><a href="#guild-minutes">صورتجلسه‌ها</a></li>
                <li><a href="#guild-edu">آموزش</a></li>
                <li><a href="#guild-announce">اطلاعیه‌ها</a></li>
                <li><a href="#guild-gallery">گالری تصاویر و ویدیو</a></li>
                <li><a href="#guild-search">جستجو</a></li>
                <li><a href="#guild-contact">تماس با ما</a></li>
            </ul>
        </aside>

        <div>
            <section class="guild-section guild-section-alt" id="guild-head" style="padding-top:0">
                <h3 class="guild-section-title">رییس {{ $guild['title'] }}</h3>
                <div class="guild-head-card">
                    <div class="guild-head-avatar">{{ mb_substr($guild['chairman'] ?: 'رئیس', 0, 1) }}</div>
                    <div class="guild-head-info">
                        <strong>{{ $guild['chairman'] ?: 'رئیس اتحادیه' }}</strong>
                        <span>مسئول پیگیری امور صنفی، نظارت و هماهنگی با اتاق اصناف</span>
                        <p>{{ $guild['content'] ?? $guild['summary'] }}</p>
                        <div class="guild-head-contact">
                            <a href="tel:{{ $guild['phone'] }}">تماس با اتحادیه</a>
                            <a href="#guild-board">مشاهده ساختار اتحادیه</a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="guild-section guild-section-alt" id="guild-board">
                <h3 class="guild-section-title">هیئت مدیره و اعضای نمونه</h3>
                <div class="guild-members-grid">
                    @foreach($members as $member)
                        <div class="guild-member-card @if($loop->first) is-head @endif">
                            <div class="member-avatar">{{ mb_substr($member['name'], 0, 1) }}</div>
                            <strong>{{ $member['name'] }}</strong>
                            <small>{{ $member['business_name'] ?? $member['role'] ?? 'عضو اتحادیه' }}</small>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="guild-section guild-section-alt" id="guild-access">
                <h3 class="guild-section-title">{{ $guildBlocks['access']['title'] ?? 'سطوح دسترسی و امکانات اتحادیه' }}</h3>
                @if(! empty($guildBlocks['access']['subtitle']))<p class="guild-search-desc">{{ $guildBlocks['access']['subtitle'] }}</p>@endif
                <div class="guild-3col">
                    @foreach($roles as $role)
                        <div class="guild-info-card">
                            <h4>{{ $role['name'] }}</h4>
                            <ul>@foreach($role['permissions'] as $permission)<li>• {{ $permission }}</li>@endforeach</ul>
                        </div>
                    @endforeach
                    @foreach($guildBlocks['access']['items'] ?? [] as $accessItem)
                        <div class="guild-info-card">
                            <h4>{{ $accessItem['title'] ?? 'دسترسی اتحادیه' }}</h4>
                            <p>{{ $accessItem['summary'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="feature-list" style="margin-top:18px">@foreach($guild['features'] as $feature)<span>{{ $feature }}</span>@endforeach</div>
            </section>

            <section class="guild-section guild-section-alt" id="guild-commissions">
                <h3 class="guild-section-title">کمیسیون‌های اتحادیه</h3>
                <div class="guild-commission-list">
                    @foreach($commissions as $commission)
                        <div class="guild-commission-item"><div class="com-num">{{ \App\Support\JalaliDate::faNumber($loop->iteration) }}</div><div><strong>{{ $commission['title'] }}</strong><small>{{ $commission['summary'] }}</small></div></div>
                    @endforeach
                </div>
            </section>

            <section class="guild-section guild-section-alt" id="guild-rules">
                <h3 class="guild-section-title">{{ $guildBlocks['rules']['title'] ?? 'قوانین و دستورالعمل‌ها' }}</h3>
                @if(! empty($guildBlocks['rules']['subtitle']))<p class="guild-search-desc">{{ $guildBlocks['rules']['subtitle'] }}</p>@endif
                <div class="guild-2col">
                    @foreach(array_chunk($rules, max(1, (int) ceil(count($rules) / 2))) as $ruleChunk)
                        <div class="guild-rules-list">
                            @foreach($ruleChunk as $rule)
                                <div class="guild-rule-item"><div class="rule-icon">{{ $rule['icon'] ?? '📌' }}</div><div><strong>{{ $rule['title'] ?? 'عنوان قانون' }}</strong><small>{{ $rule['summary'] ?? '' }}</small></div></div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="guild-section" id="guild-slider">
                <h3 class="guild-section-title">اسلایدر خبری اتحادیه</h3>
                <div class="guild-news-slider swiper"><div class="swiper-wrapper">
                    @foreach($news as $item)
                        <article class="swiper-slide"><img src="{{ asset($item['gallery'][0] ?? $site['hero_image']) }}" alt="{{ $item['title'] }}"><div class="slide-overlay"></div><div class="slide-text"><h3>{{ $item['title'] }}</h3><span>{{ $item['published_at'] }}</span></div></article>
                    @endforeach
                </div><div class="slider-arrows"><button class="guild-slider-prev" type="button">‹</button><button class="guild-slider-next" type="button">›</button></div><div class="swiper-pagination"></div></div>
            </section>

            <section class="guild-section" id="guild-news">
                <h3 class="guild-section-title">آخرین اخبار {{ $guild['title'] }}</h3>
                <div class="guild-article-list">
                    @foreach($news as $item)
                        <a class="guild-article-item" href="{{ route('news.show', $item['slug']) }}"><img alt="{{ $item['title'] }}" src="{{ asset($item['gallery'][0] ?? $site['hero_image']) }}"><div><h4>{{ $item['title'] }}</h4><p>{{ $item['summary'] }}</p><span class="item-date">{{ $item['published_at'] }}</span></div></a>
                    @endforeach
                </div>
            </section>

            <section class="guild-section" id="guild-articles">
                <h3 class="guild-section-title">{{ $guildBlocks['articles']['title'] ?? 'مقاله‌ها' }}</h3>
                @if(! empty($guildBlocks['articles']['subtitle']))<p class="guild-search-desc">{{ $guildBlocks['articles']['subtitle'] }}</p>@endif
                <div class="guild-3col">
                    @foreach($articles as $article)
                        <div class="archive-card"><a href="#"><img alt="{{ $article['title'] ?? 'مقاله اتحادیه' }}" class="archive-card-img" src="{{ asset($article['image'] ?? $site['hero_image']) }}"><div class="archive-card-body"><h2>{{ $article['title'] ?? 'مقاله اتحادیه' }}</h2><p>{{ $article['summary'] ?? '' }}</p><span class="card-date">{{ $jalaliDate }}</span></div></a></div>
                    @endforeach
                </div>
            </section>

            <section class="guild-section guild-section-alt" id="guild-prices">
                <h3 class="guild-section-title">{{ $guildBlocks['prices']['title'] ?? 'نرخ نامه و تعرفه خدمات' }}</h3>
                @if(! empty($guildBlocks['prices']['subtitle']))<p class="guild-search-desc">{{ $guildBlocks['prices']['subtitle'] }}</p>@endif
                <div class="price-table-wrap"><table class="price-table"><thead><tr><th>عنوان</th><th>مبلغ/وضعیت</th><th>نوع</th><th>تاریخ بروزرسانی</th></tr></thead><tbody>@foreach($prices as $price)<tr><td>{{ $price['title'] ?? 'ردیف نرخ‌نامه' }}</td><td>{{ $price['amount'] ?? 'قابل تنظیم' }}</td><td>{{ $price['type'] ?? 'عمومی' }}</td><td>{{ $jalaliDate }}</td></tr>@endforeach</tbody></table></div>
            </section>

            <section class="guild-section guild-section-alt" id="guild-complaint">
                <h3 class="guild-section-title">ثبت شکایت صنفی</h3>
                <div class="guild-2col"><div class="guild-info-card"><h4>نحوه ثبت شکایت</h4><p>ثبت شکایت برای این اتحادیه {{ $guild['complaints_enabled'] ? 'فعال است و پس از ثبت، کد رهگیری دریافت می‌کنید.' : 'به انتخاب اتحادیه فعلاً غیرفعال است.' }}</p><ul><li>ثبت آنلاین از طریق فرم شکایت</li><li>پیگیری با کد رهگیری</li><li>ارجاع به کارشناس اتحادیه</li></ul></div><div class="guild-complaint-cta"><strong>{{ $guild['complaints_enabled'] ? 'ثبت شکایت آنلاین' : 'ثبت شکایت غیرفعال' }}</strong>@if($guild['complaints_enabled'])<a class="tab-pill active" href="{{ route('complaints.create') }}">ثبت شکایت جدید</a><a class="tab-pill" href="{{ route('complaints.create') }}">پیگیری شکایت قبلی</a>@else<span class="tab-pill">این اتحادیه نمایش شکایت را غیرفعال کرده است</span>@endif</div></div>
            </section>

            <section class="guild-section guild-section-alt" id="guild-minutes">
                <h3 class="guild-section-title">{{ $guildBlocks['minutes']['title'] ?? 'صورتجلسه‌های اجرایی' }}</h3>
                @if(! empty($guildBlocks['minutes']['subtitle']))<p class="guild-search-desc">{{ $guildBlocks['minutes']['subtitle'] }}</p>@endif
                <div class="guild-minutes-list">@foreach($minutes as $minute)<div class="guild-minute-item"><div class="minute-info"><strong>{{ $minute['title'] ?? 'صورتجلسه اتحادیه' }}</strong><span>{{ $minute['date'] ?? $jalaliDate }}</span></div><a class="minute-dl" href="{{ $minute['file'] ?? '#' }}">دانلود PDF</a></div>@endforeach</div>
            </section>

            <section class="guild-section guild-section-alt" id="guild-edu">
                <h3 class="guild-section-title">{{ $guildBlocks['education']['title'] ?? 'آموزش' }}</h3>
                @if(! empty($guildBlocks['education']['subtitle']))<p class="guild-search-desc">{{ $guildBlocks['education']['subtitle'] }}</p>@endif
                <div class="guild-4col">@foreach($educations as $edu)<div class="guild-edu-item"><div class="edu-icon">{{ $edu['icon'] ?? '📚' }}</div><strong>{{ $edu['title'] ?? 'آموزش اتحادیه' }}</strong><span>{{ $edu['summary'] ?? '' }}</span></div>@endforeach</div>
            </section>

            <section class="guild-section guild-section-alt" id="guild-announce"><h3 class="guild-section-title">اطلاعیه و بخشنامه‌ها</h3><div class="guild-announce-list">@foreach($announcements as $item)<div class="guild-announce-item"><div class="announce-badge"></div><strong>{{ $item['title'] }}</strong><span>{{ $item['published_at'] ?? $jalaliDate }}</span></div>@endforeach</div></section>

            <section class="guild-section" id="guild-gallery">
                <h3 class="guild-section-title">{{ $guildBlocks['gallery']['title'] ?? 'گالری تصاویر و ویدیو' }}</h3>
                @if(! empty($guildBlocks['gallery']['subtitle']))<p class="guild-search-desc">{{ $guildBlocks['gallery']['subtitle'] }}</p>@endif
                <div class="guild-gallery-tabs" data-tab-group="guild-gallery"><button class="tab-pill active" data-tab-target="gallery-image" type="button">تصاویر</button><button class="tab-pill" data-tab-target="gallery-video" type="button">ویدیوها</button></div>
                <div class="tab-panels" data-tab-panels="guild-gallery"><div class="tab-panel active" data-tab-panel="gallery-image"><div class="guild-gallery-grid">@foreach($gallery as $image)<div class="guild-gallery-item"><img alt="گالری" src="{{ asset($image) }}"></div>@endforeach</div></div><div class="tab-panel" data-tab-panel="gallery-video"><div class="guild-gallery-grid">@foreach($gallery as $image)<div class="guild-gallery-item video"><img alt="گالری ویدیو" src="{{ asset($image) }}"></div>@endforeach</div></div></div>
                <div class="guild-gallery-more"><a href="{{ route('gallery.index') }}">مشاهده همه تصاویر و ویدیوها</a></div>
            </section>

            <section class="guild-section" id="guild-search"><h3 class="guild-section-title">جستجو در {{ $guild['title'] }}</h3><p class="guild-search-desc">عبارت مورد نظر خود را در میان اخبار، اعضا، قوانین و اطلاعات اتحادیه جستجو کنید</p><form class="guild-search-box" action="{{ route('search') }}"><input name="q" placeholder="جستجو در اخبار، اعضا، قوانین و..." type="search"><button type="submit">جستجو</button></form></section>

            <section class="guild-section" id="guild-contact">
                <h3 class="guild-section-title">{{ $guildBlocks['contact']['title'] ?? ('تماس با '.$guild['title']) }}</h3>
                @if(! empty($guildBlocks['contact']['subtitle']))<p class="guild-search-desc">{{ $guildBlocks['contact']['subtitle'] }}</p>@endif
                <div class="guild-contact-grid">
                    @forelse($guildBlocks['contact']['items'] ?? [] as $contactItem)
                        <div class="guild-contact-card"><div class="contact-icon">{{ $contactItem['icon'] ?? '☎️' }}</div><div><strong>{{ $contactItem['label'] ?? 'اطلاعات تماس' }}</strong><span>{{ $contactItem['value'] ?? '' }}</span></div></div>
                    @empty
                        <div class="guild-contact-card"><div class="contact-icon">📍</div><div><strong>آدرس</strong><span>{{ $site['address'] }}</span></div></div>
                        <div class="guild-contact-card"><div class="contact-icon">📞</div><div><strong>تلفن</strong><span>{{ $guild['phone'] ?: $site['phone'] }}</span></div></div>
                        <div class="guild-contact-card"><div class="contact-icon">✉️</div><div><strong>ایمیل</strong><span>{{ $site['email'] }}</span></div></div>
                        <div class="guild-contact-card"><div class="contact-icon">👤</div><div><strong>رئیس اتحادیه</strong><span>{{ $guild['chairman'] }}</span></div></div>
                    @endforelse
                </div>
                <div class="guild-social"><a href="#" aria-label="اینستاگرام">📷</a><a href="#" aria-label="تلگرام">✈️</a><a href="#" aria-label="واتساپ">💬</a><a href="#" aria-label="ایتا">📱</a></div>
            </section>
        </div>
    </div>
</main>
@endsection
