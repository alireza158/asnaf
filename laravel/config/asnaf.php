<?php

return [
    'site' => [
        'name' => 'اتاق اصناف شهرستان گرگان',
        'tagline' => 'پشتیبان کسب‌وکارهای صنفی، ناظر خدمات اتحادیه‌ها و مرجع اطلاع‌رسانی رسمی بازار گرگان',
        'phone' => '۰۱۷۳۲۱۵۲۹۱۲',
        'phone_alt' => '۰۱۷-۳۲۱۵۴۷۶۷',
        'email' => 'info@asnaf-gorgan.ir',
        'address' => 'گرگان، خیابان مطهری جنوبی، روبروی پمپ بنزین، ساختمان اتاق اصناف',
        'hero_image' => 'theme/assets/img/asnaf-gorgan-default.jpg',
        'logo' => 'theme/assets/img/asnaf-wordmark.svg',
        'footer_logo' => 'theme/assets/img/asnaf-footer-mark.svg',
    ],

    'top_links' => [
        ['title' => 'سامانه خدمات صنفی', 'url' => '/systems', 'style' => 'pill'],
        ['title' => 'تماس با اتاق', 'url' => 'tel:01732152912', 'subtitle' => '۰۱۷۳۲۱۵۲۹۱۲', 'style' => 'contact'],
    ],

    'menus' => [
        'primary' => [
            ['title' => 'صفحه اصلی', 'url' => '/', 'children' => []],
            ['title' => 'درباره اتاق', 'url' => '/page/about', 'children' => [
                ['title' => 'معرفی اتاق اصناف گرگان', 'url' => '/page/about'],
                ['title' => 'هیئت رئیسه و ساختار اداری', 'url' => '/page/board'],
                ['title' => 'آدرس و راهنمای مراجعه', 'url' => '/contact'],
            ]],
            ['title' => 'خدمات صنفی', 'url' => '/services', 'children' => [
                ['title' => 'صدور پروانه کسب', 'url' => '/services/business-license'],
                ['title' => 'تمدید و انتقال پروانه', 'url' => '/services/license-renewal'],
                ['title' => 'فرم‌ها و درخواست‌ها', 'url' => '/services/forms'],
            ]],
            ['title' => 'اتحادیه‌ها', 'url' => '/guilds', 'children' => [
                ['title' => 'فهرست اتحادیه‌های صنفی', 'url' => '/guilds'],
                ['title' => 'رسته‌های شغلی', 'url' => '/categories'],
                ['title' => 'ثبت شکایت صنفی', 'url' => '/complaints/create'],
            ]],
            ['title' => 'اخبار و اطلاعیه‌ها', 'url' => '/news', 'children' => [
                ['title' => 'اخبار مهم', 'url' => '/news?important=1'],
                ['title' => 'اطلاعیه‌ها', 'url' => '/announcements'],
                ['title' => 'گالری تصاویر', 'url' => '/gallery'],
                ['title' => 'ویدیوها', 'url' => '/videos'],
            ]],
            ['title' => 'سامانه‌ها', 'url' => '/systems', 'children' => [
                ['title' => 'سامانه نوین اصناف', 'url' => '/systems/novin-asnaf'],
                ['title' => 'سامانه آموزش اصناف', 'url' => '/systems/education'],
                ['title' => 'راهنمای خدمات الکترونیک', 'url' => '/services'],
            ]],
            ['title' => 'گردشگری', 'url' => '/tourism', 'children' => []],
            ['title' => 'تماس با ما', 'url' => '/contact', 'children' => []],
        ],
        'quick' => [
            ['title' => 'ثبت و پیگیری شکایت', 'icon' => '⚖️', 'url' => '/complaints/create'],
            ['title' => 'اتحادیه‌های صنفی', 'icon' => '🏛️', 'url' => '/guilds'],
            ['title' => 'سامانه‌ها', 'icon' => '💻', 'url' => '/systems'],
            ['title' => 'کمیسیون‌ها', 'icon' => '📌', 'url' => '/commissions'],
            ['title' => 'گردشگری بازار', 'icon' => '🧭', 'url' => '/tourism'],
        ],
        'footer' => [
            ['title' => 'آرشیو اخبار', 'url' => '/news'],
            ['title' => 'سامانه خدمات صنفی', 'url' => '/systems'],
            ['title' => 'گالری تصاویر', 'url' => '/gallery'],
            ['title' => 'تماس با ما', 'url' => '/contact'],
        ],
    ],

    'home_sections' => [
        ['key' => 'services', 'title' => 'خدمات الکترونیک صنفی', 'enabled' => true, 'order' => 1],
        ['key' => 'important_news', 'title' => 'اخبار مهم', 'enabled' => true, 'order' => 2],
        ['key' => 'guilds', 'title' => 'اتحادیه‌ها', 'enabled' => true, 'order' => 3],
        ['key' => 'commissions', 'title' => 'کمیسیون‌های اتاق اصناف', 'enabled' => true, 'order' => 4],
        ['key' => 'tourism', 'title' => 'گردشگری و بازار', 'enabled' => true, 'order' => 5],
        ['key' => 'messages', 'title' => 'پیام مدیران اصناف', 'enabled' => true, 'order' => 6],
    ],

    'services' => [
        ['slug' => 'business-license', 'icon' => '📋', 'title' => 'نحوه صدور پروانه کسب', 'summary' => 'راهنمای گام‌به‌گام دریافت پروانه کسب جدید و تشکیل پرونده صنفی برای متقاضیان.', 'content' => 'در این صفحه مدیر محتوا می‌تواند با ادیتور، مراحل، مدارک، زمان‌بندی و فایل‌های مرتبط صدور پروانه را وارد کند.'],
        ['slug' => 'license-renewal', 'icon' => '🔄', 'title' => 'تمدید و انتقال پروانه', 'summary' => 'مراحل تمدید، انتقال و اصلاح پروانه کسب در اتحادیه مربوطه.', 'content' => 'این خدمت قابلیت اتصال به فرم‌های داخلی یا لینک سامانه بیرونی را دارد.'],
        ['slug' => 'complaint', 'icon' => '⚖️', 'title' => 'ثبت شکایت صنفی', 'summary' => 'ثبت شکایت مردمی با دریافت کد رهگیری و ارجاع به اتحادیه مرتبط.', 'content' => 'پس از ثبت، شکایت در کارتابل اتحادیه قابل مشاهده است و وضعیت آن به کاربر اعلام می‌شود.'],
        ['slug' => 'forms', 'icon' => '📁', 'title' => 'فرم‌ها و بخشنامه‌ها', 'summary' => 'دریافت فرم‌های اداری، بخشنامه‌ها و دستورالعمل‌های صنفی.', 'content' => 'فایل‌ها، دسته‌بندی‌ها و وضعیت انتشار از پنل مدیریت کنترل می‌شوند.'],
        ['slug' => 'novin-asnaf', 'icon' => '💻', 'title' => 'سامانه نوین اصناف', 'summary' => 'ورود سریع به سامانه‌های الکترونیکی و راهنمای استفاده از آن‌ها.', 'content' => 'هر لینک سامانه می‌تواند داخلی یا خارجی باشد و در منوی پویا نیز قرار گیرد.'],
    ],

    'news' => [
        ['slug' => 'market-monitoring', 'type' => 'خبر', 'title' => 'اجرای طرح نظارت ویژه بازار با همکاری اتحادیه‌های صنفی', 'important' => true, 'status' => 'published', 'published_at' => '۱۴۰۵/۰۳/۱۰', 'summary' => 'بازرسی‌های مشترک برای صیانت از حقوق شهروندان و فعالان صنفی آغاز شد.', 'gallery' => ['theme/assets/img/asnaf-gorgan-default.jpg'], 'approval' => 'تایید مدیرکل'],
        ['slug' => 'education-course', 'type' => 'خبر', 'title' => 'برگزاری دوره آموزشی قوانین صنفی برای متقاضیان پروانه کسب', 'important' => true, 'status' => 'published', 'published_at' => '۱۴۰۵/۰۳/۰۸', 'summary' => 'ثبت‌نام دوره آموزشی از طریق واحد آموزش اتاق اصناف انجام می‌شود.', 'gallery' => ['theme/assets/img/asnaf-gorgan-default.jpg'], 'approval' => 'تایید مدیرکل'],
        ['slug' => 'notice-tax', 'type' => 'اطلاعیه', 'title' => 'اطلاعیه مهم درباره تکمیل پرونده مالیاتی اعضای صنفی', 'important' => false, 'status' => 'pending', 'published_at' => 'در انتظار انتشار', 'summary' => 'این اطلاعیه پس از بررسی مدیرکل در سایت منتشر خواهد شد.', 'gallery' => [], 'approval' => 'در انتظار تایید'],
    ],

    'guilds' => [
        ['slug' => 'food', 'title' => 'اتحادیه صنف مواد غذایی', 'category' => 'توزیعی', 'chairman' => 'رئیس اتحادیه مواد غذایی', 'phone' => '۰۱۷-۳۲۱۱۰۰۰۰', 'complaints_enabled' => true, 'features' => ['اعضا', 'اخبار اتحادیه', 'پیامک اعضا', 'شکایات'], 'members_count' => 328, 'summary' => 'مدیریت امور واحدهای صنفی خواربار، سوپرمارکت و عرضه‌کنندگان مواد غذایی.'],
        ['slug' => 'tourism-services', 'title' => 'اتحادیه خدمات گردشگری و پذیرایی', 'category' => 'خدماتی', 'chairman' => 'رئیس اتحادیه گردشگری', 'phone' => '۰۱۷-۳۲۱۲۰۰۰۰', 'complaints_enabled' => true, 'features' => ['جاذبه‌ها', 'اعضا', 'تبلیغات', 'شکایات'], 'members_count' => 84, 'summary' => 'پوشش واحدهای پذیرایی، خدمات گردشگری و کسب‌وکارهای مرتبط با بازار گردشگری.'],
        ['slug' => 'technical', 'title' => 'اتحادیه صنوف فنی', 'category' => 'تولیدی و فنی', 'chairman' => 'رئیس اتحادیه صنوف فنی', 'phone' => '۰۱۷-۳۲۱۳۰۰۰۰', 'complaints_enabled' => false, 'features' => ['اعضا', 'اطلاعیه‌ها', 'پیامک اعضا'], 'members_count' => 196, 'summary' => 'رسیدگی به امور صنوف فنی، تعمیرگاهی و خدمات تخصصی.'],
    ],

    'commissions' => [
        ['slug' => 'inspection', 'title' => 'کمیسیون بازرسی و نظارت', 'summary' => 'برنامه‌ریزی گشت‌های مشترک، رسیدگی به گزارش‌ها و پایش بازار.', 'meetings' => ['جلسه بررسی طرح نوروزی بازار', 'نشست هماهنگی شکایات مردمی']],
        ['slug' => 'education', 'title' => 'کمیسیون آموزش', 'summary' => 'طراحی دوره‌های آموزشی برای اعضا، متقاضیان و مدیران اتحادیه‌ها.', 'meetings' => ['کارگاه قوانین نظام صنفی', 'جلسه برنامه‌ریزی آموزش مجازی']],
        ['slug' => 'tourism', 'title' => 'کمیسیون گردشگری بازار', 'summary' => 'معرفی ظرفیت‌های بازار، صنایع دستی و مسیرهای گردشگری اصناف.', 'meetings' => ['بررسی مسیر گردشگری بازار نعلبندان']],
    ],

    'tourism' => [
        ['slug' => 'bazaar', 'title' => 'بازار تاریخی نعلبندان', 'category' => 'بازار و خرید', 'summary' => 'مسیر پیشنهادی بازدید از بازار سنتی، صنایع دستی و سوغات گرگان.', 'image' => 'theme/assets/img/asnaf-gorgan-default.jpg'],
        ['slug' => 'naharkhoran', 'title' => 'ناهارخوران گرگان', 'category' => 'طبیعت', 'summary' => 'معرفی مسیرهای دسترسی، امکانات اطراف و کسب‌وکارهای خدماتی مرتبط.', 'image' => 'theme/assets/img/asnaf-gorgan-default.jpg'],
        ['slug' => 'handicrafts', 'title' => 'نمایشگاه صنایع دستی', 'category' => 'فرهنگی', 'summary' => 'صفحه قابل تنظیم برای معرفی رویدادها، غرفه‌ها و زمان بازدید.', 'image' => 'theme/assets/img/asnaf-gorgan-default.jpg'],
    ],

    'systems' => [
        ['slug' => 'novin-asnaf', 'title' => 'سامانه نوین اصناف', 'url' => 'https://iranianasnaf.ir', 'description' => 'درگاه ملی خدمات مرتبط با پروانه کسب و پرونده صنفی.'],
        ['slug' => 'education', 'title' => 'سامانه آموزش اصناف', 'url' => '/services/business-license', 'description' => 'ثبت‌نام و مشاهده دوره‌های آموزشی مورد نیاز اصناف.'],
        ['slug' => 'inspection', 'title' => 'سامانه بازرسی و شکایات', 'url' => '/complaints/create', 'description' => 'ثبت شکایت، دریافت کد رهگیری و پیگیری وضعیت درخواست.'],
    ],

    'ads' => [
        ['position' => 'hero-left', 'title' => 'جایگاه تبلیغاتی صفحه اصلی', 'url' => '/admin/advertisements', 'active' => true],
        ['position' => 'guild-page', 'title' => 'تبلیغات اختصاصی اتحادیه‌ها', 'url' => '/admin/advertisements', 'active' => true],
    ],

    'roles' => [
        ['name' => 'مدیرکل', 'permissions' => ['تایید نهایی محتوا', 'مدیریت کاربران', 'مدیریت منوها', 'مدیریت صفحه اصلی']],
        ['name' => 'کارشناس اتحادیه', 'permissions' => ['مشاهده اعضای اتحادیه خود', 'ارسال پیامک به اعضا', 'ثبت پیش‌نویس خبر', 'پاسخ به شکایت اتحادیه']],
        ['name' => 'خبرنگار', 'permissions' => ['ثبت خبر', 'افزودن گالری تصویر', 'افزودن ویدیو مستقیم یا آپارات']],
        ['name' => 'مدیر تبلیغات', 'permissions' => ['مدیریت بنرها', 'زمان‌بندی نمایش تبلیغات']],
    ],

    'workflow' => [
        ['step' => 'پیش‌نویس', 'description' => 'کارشناس یا خبرنگار محتوا را ثبت می‌کند.'],
        ['step' => 'بازبینی', 'description' => 'سردبیر یا مسئول واحد محتوا متن، تصویر، گالری و ویدیو را بررسی می‌کند.'],
        ['step' => 'تایید مدیرکل', 'description' => 'انتشار هر خبر، اطلاعیه، صفحه و پیام تبریک پس از تایید مدیرکل انجام می‌شود.'],
        ['step' => 'انتشار', 'description' => 'محتوا در سایت، صفحه اتحادیه یا منوی انتخاب‌شده نمایش داده می‌شود.'],
    ],

    'manager_messages' => [
        ['guild' => 'اتحادیه صنف مواد غذایی', 'title' => 'پیام تبریک روز اصناف', 'text' => 'با گرامیداشت تلاش فعالان صنفی، روز اصناف را به خانواده بزرگ بازار گرگان تبریک می‌گوییم.', 'status' => 'منتشر شده'],
        ['guild' => 'اتحادیه خدمات گردشگری و پذیرایی', 'title' => 'دعوت به همکاری در معرفی ظرفیت گردشگری', 'text' => 'از اعضای محترم دعوت می‌شود پیشنهادهای خود برای معرفی بهتر بازار گرگان را ارسال کنند.', 'status' => 'در انتظار تایید'],
    ],
];
