<?php

use App\Models\Complaint;
use App\Models\User;
use App\Support\JalaliDate;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

function asnafJson(mixed $value, mixed $default = []): mixed
{
    if (is_array($value)) {
        return $value;
    }

    if ($value === null || $value === '') {
        return $default;
    }

    return json_decode((string) $value, true) ?: $default;
}

function asnafData(): array
{
    try {
        if (! Schema::hasTable('site_settings')) {
            return config('asnaf');
        }
    } catch (Throwable) {
        return config('asnaf');
    }

    $settings = DB::table('site_settings')->pluck('value', 'key')->map(fn ($value) => asnafJson($value))->all();
    $menus = [];
    $menuRows = DB::table('menus')->where('enabled', true)->orderBy('sort_order')->get()->groupBy('location');
    foreach ($menuRows as $location => $rows) {
        $parents = $rows->whereNull('parent_id');
        $menus[$location] = $parents->map(function ($parent) use ($rows): array {
            return [
                'title' => $parent->title,
                'url' => $parent->url,
                'icon' => $parent->icon,
                'children' => $rows->where('parent_id', $parent->id)->values()->map(fn ($child) => [
                    'title' => $child->title,
                    'url' => $child->url,
                    'icon' => $child->icon,
                ])->all(),
            ];
        })->values()->all();
    }

    $services = DB::table('service_pages')->where('status', 'published')->orderBy('sort_order')->get()->map(fn ($item) => (array) $item)->all();
    $news = DB::table('contents')->orderByDesc('important')->orderByDesc('published_at')->get()->map(fn ($item) => [
        'slug' => $item->slug,
        'type' => $item->type,
        'title' => $item->title,
        'important' => (bool) $item->important,
        'status' => $item->status,
        'published_at' => $item->published_at ? JalaliDate::format(new DateTimeImmutable($item->published_at)) : 'در انتظار انتشار',
        'summary' => $item->summary,
        'content' => $item->content,
        'gallery' => asnafJson($item->gallery),
        'approval' => $item->approved_by ?: ($item->status === 'published' ? 'منتشر شده' : 'در انتظار تایید'),
        'video_type' => $item->video_type,
        'video_url' => $item->video_url,
    ])->all();

    $guilds = DB::table('guilds')
        ->leftJoin('categories', 'categories.id', '=', 'guilds.category_id')
        ->select('guilds.*', 'categories.title as category')
        ->orderBy('guilds.id')
        ->get()
        ->map(fn ($guild) => [
            'slug' => $guild->slug,
            'title' => $guild->title,
            'category' => $guild->category,
            'chairman' => $guild->chairman,
            'phone' => $guild->phone,
            'complaints_enabled' => (bool) $guild->complaints_enabled,
            'features' => asnafJson($guild->features),
            'members_count' => $guild->members_count,
            'summary' => $guild->summary,
            'content' => $guild->content,
        ])->all();

    $commissions = DB::table('commissions')->orderBy('sort_order')->get()->map(function ($commission): array {
        return [
            'slug' => $commission->slug,
            'title' => $commission->title,
            'summary' => $commission->summary,
            'content' => $commission->content,
            'meetings' => DB::table('commission_meetings')->where('commission_id', $commission->id)->orderByDesc('held_at')->pluck('title')->all(),
        ];
    })->all();

    $tourism = DB::table('tourism_places')
        ->leftJoin('categories', 'categories.id', '=', 'tourism_places.category_id')
        ->select('tourism_places.*', 'categories.title as category')
        ->where('tourism_places.enabled', true)
        ->orderBy('tourism_places.id')
        ->get()
        ->map(fn ($place) => [
            'slug' => $place->slug,
            'title' => $place->title,
            'category' => $place->category,
            'summary' => $place->summary,
            'content' => $place->content,
            'image' => $place->image ?: 'theme/assets/img/asnaf-gorgan-default.jpg',
        ])->all();

    return [
        'site' => $settings['site'] ?? config('asnaf.site'),
        'top_links' => $settings['top_links'] ?? config('asnaf.top_links'),
        'menus' => array_replace_recursive(config('asnaf.menus'), $menus),
        'home_sections' => DB::table('home_sections')->orderBy('sort_order')->get()->map(fn ($section) => [
            'key' => $section->key,
            'title' => $section->title,
            'enabled' => (bool) $section->enabled,
            'order' => $section->sort_order,
            'settings' => asnafJson($section->settings),
        ])->all(),
        'services' => $services,
        'news' => $news,
        'guilds' => $guilds,
        'commissions' => $commissions,
        'tourism' => $tourism,
        'systems' => DB::table('systems')->where('enabled', true)->orderBy('id')->get()->map(fn ($item) => (array) $item)->all(),
        'ads' => DB::table('advertisements')->orderBy('id')->get()->map(fn ($item) => [
            'position' => $item->position,
            'title' => $item->title,
            'url' => $item->url,
            'image' => $item->image,
            'active' => (bool) $item->active,
        ])->all(),
        'roles' => DB::table('roles')->orderBy('id')->get()->map(fn ($role) => [
            'name' => $role->name,
            'permissions' => asnafJson($role->permissions),
        ])->all(),
        'workflow' => $settings['workflow'] ?? config('asnaf.workflow'),
        'manager_messages' => $settings['manager_messages'] ?? config('asnaf.manager_messages'),
    ];
}


function adminModules(): array
{
    return [
        'site_settings' => ['group' => 'ساختار سایت', 'title' => 'تنظیمات سایت', 'description' => 'نام سایت، اطلاعات تماس، متن‌های ثابت هدر و فوتر را از اینجا مدیریت کنید.', 'table' => 'site_settings', 'columns' => ['id', 'key', 'value'], 'editable' => ['key', 'value']],
        'menus' => ['group' => 'ساختار سایت', 'title' => 'منوهای پویا', 'description' => 'منوی اصلی، دسترسی سریع و فوتر را مثل وردپرس با عنوان، لینک، والد و ترتیب مدیریت کنید.', 'table' => 'menus', 'columns' => ['id', 'location', 'title', 'url', 'parent_id', 'sort_order', 'enabled'], 'editable' => ['location', 'parent_id', 'title', 'url', 'icon', 'is_external', 'enabled', 'sort_order']],
        'home_sections' => ['group' => 'ساختار سایت', 'title' => 'صفحه اصلی', 'description' => 'بخش‌های صفحه اصلی را فعال/غیرفعال کنید و ترتیب نمایش آن‌ها را تغییر دهید.', 'table' => 'home_sections', 'columns' => ['id', 'key', 'title', 'enabled', 'sort_order'], 'editable' => ['key', 'title', 'enabled', 'sort_order', 'settings']],
        'advertisements' => ['group' => 'ساختار سایت', 'title' => 'تبلیغات', 'description' => 'بنرهای تبلیغاتی را برای جایگاه‌های قابل فهم مثل صفحه اصلی یا صفحه اتحادیه تنظیم کنید.', 'table' => 'advertisements', 'columns' => ['id', 'position', 'title', 'url', 'active'], 'editable' => ['position', 'title', 'url', 'image', 'active', 'starts_at', 'ends_at']],
        'contents' => ['group' => 'محتوا', 'title' => 'اخبار و اطلاعیه‌ها', 'description' => 'خبر، اطلاعیه، گالری تصویر، ویدیو و وضعیت تایید/انتشار را مدیریت کنید.', 'table' => 'contents', 'columns' => ['id', 'type', 'title', 'status', 'important', 'approved_by'], 'editable' => ['guild_id', 'category_id', 'slug', 'type', 'title', 'summary', 'content', 'status', 'important', 'approved_by', 'gallery', 'video_type', 'video_url']],
        'service_pages' => ['group' => 'محتوا', 'title' => 'خدمات الکترونیک', 'description' => 'کارت‌های خدمات بالای سایت و صفحات توضیحی هر خدمت را مدیریت کنید.', 'table' => 'service_pages', 'columns' => ['id', 'title', 'slug', 'status', 'sort_order'], 'editable' => ['slug', 'icon', 'title', 'summary', 'content', 'status', 'sort_order']],
        'categories' => ['group' => 'محتوا', 'title' => 'دسته‌بندی‌ها', 'description' => 'دسته‌های اخبار، اتحادیه‌ها، خدمات و گردشگری را تعریف کنید.', 'table' => 'categories', 'columns' => ['id', 'type', 'title', 'slug'], 'editable' => ['type', 'title', 'slug', 'description']],
        'systems' => ['group' => 'محتوا', 'title' => 'سامانه‌ها', 'description' => 'لینک سامانه‌های داخلی و خارجی را برای نمایش در سایت مدیریت کنید.', 'table' => 'systems', 'columns' => ['id', 'title', 'url', 'enabled'], 'editable' => ['slug', 'title', 'url', 'description', 'is_external', 'enabled']],
        'guilds' => ['group' => 'اتحادیه و خدمات', 'title' => 'اتحادیه‌ها', 'description' => 'صفحه اختصاصی هر اتحادیه، وضعیت شکایت، امکانات و اطلاعات تماس را مدیریت کنید.', 'table' => 'guilds', 'columns' => ['id', 'title', 'chairman', 'phone', 'complaints_enabled', 'members_count'], 'editable' => ['category_id', 'slug', 'title', 'chairman', 'phone', 'complaints_enabled', 'features', 'members_count', 'summary', 'content']],
        'guild_page_blocks' => ['group' => 'اتحادیه و خدمات', 'title' => 'بخش‌های صفحه اتحادیه', 'description' => 'هر قسمت صفحه جزئیات اتحادیه مثل قوانین، مقالات، نرخ‌نامه، صورتجلسه، آموزش، گالری و اطلاعات تماس را جداگانه و قابل فهم مدیریت کنید.', 'table' => 'guild_page_blocks', 'columns' => ['id', 'guild_title', 'block_type', 'title', 'enabled', 'sort_order'], 'editable' => ['guild_id', 'block_type', 'title', 'subtitle', 'items', 'enabled', 'sort_order']],
        'guild_members' => ['group' => 'اتحادیه و خدمات', 'title' => 'اعضای اتحادیه', 'description' => 'اعضای هر اتحادیه و وضعیت دریافت پیامک آن‌ها را ثبت کنید.', 'table' => 'guild_members', 'columns' => ['id', 'guild_id', 'name', 'mobile', 'business_name'], 'editable' => ['guild_id', 'name', 'mobile', 'business_name', 'license_number', 'sms_enabled']],
        'complaints' => ['group' => 'اتحادیه و خدمات', 'title' => 'شکایات', 'description' => 'شکایات ثبت‌شده، کد رهگیری و وضعیت رسیدگی را مدیریت کنید.', 'table' => 'complaints', 'columns' => ['id', 'tracking_code', 'guild_id', 'name', 'mobile', 'status'], 'editable' => ['guild_id', 'tracking_code', 'name', 'mobile', 'body', 'status']],
        'sms_messages' => ['group' => 'اتحادیه و خدمات', 'title' => 'پیامک‌ها', 'description' => 'پیامک‌های ارسالی به اعضای اتحادیه یا شخص خاص را آماده و پیگیری کنید.', 'table' => 'sms_messages', 'columns' => ['id', 'guild_id', 'recipient_type', 'recipient_mobile', 'status'], 'editable' => ['guild_id', 'sent_by', 'recipient_type', 'recipient_mobile', 'body', 'status']],
        'commissions' => ['group' => 'کمیسیون و گردشگری', 'title' => 'کمیسیون‌ها', 'description' => 'صفحات معرفی کمیسیون‌ها و شرح وظایف آن‌ها را مدیریت کنید.', 'table' => 'commissions', 'columns' => ['id', 'title', 'slug', 'sort_order'], 'editable' => ['slug', 'title', 'summary', 'content', 'sort_order']],
        'commission_meetings' => ['group' => 'کمیسیون و گردشگری', 'title' => 'جلسات کمیسیون‌ها', 'description' => 'جلسات، تاریخ و خلاصه مصوبات هر کمیسیون را ثبت کنید.', 'table' => 'commission_meetings', 'columns' => ['id', 'commission_id', 'title', 'held_at'], 'editable' => ['commission_id', 'title', 'held_at', 'summary']],
        'tourism_places' => ['group' => 'کمیسیون و گردشگری', 'title' => 'گردشگری', 'description' => 'مکان‌های گردشگری، تصویر، دسته‌بندی و توضیحات را مدیریت کنید.', 'table' => 'tourism_places', 'columns' => ['id', 'title', 'category_id', 'enabled'], 'editable' => ['category_id', 'slug', 'title', 'summary', 'content', 'image', 'enabled']],
        'roles' => ['group' => 'کاربران', 'title' => 'سطوح دسترسی', 'description' => 'نقش‌ها و مجوزهای قابل تخصیص به کاربران پنل را تعریف کنید.', 'table' => 'roles', 'columns' => ['id', 'name', 'permissions'], 'editable' => ['name', 'permissions']],
    ];
}

function adminModuleGroups(): array
{
    return collect(adminModules())
        ->groupBy('group', true)
        ->map(fn ($items) => $items->all())
        ->all();
}


function adminModuleGuide(?string $module = null): array
{
    $guides = [
        'site_settings' => ['intro' => 'اطلاعات پایه سایت مثل نام، شماره تماس، آدرس و لینک‌های بالای سایت را اینجا اصلاح کنید.', 'steps' => ['رکورد مورد نظر را باز کنید.', 'فقط متن مقدار را تغییر دهید و کلید را دست نزنید.', 'ذخیره کنید و سایت را بررسی کنید.']],
        'menus' => ['intro' => 'برای ساخت منو مثل وردپرس فقط عنوان، لینک و محل نمایش را انتخاب کنید.', 'steps' => ['محل نمایش را انتخاب کنید: منوی اصلی، دسترسی سریع یا فوتر.', 'عنوانی بنویسید که کاربر در سایت می‌بیند.', 'برای لینک داخلی مثل /news و برای لینک بیرونی آدرس کامل وارد کنید.']],
        'home_sections' => ['intro' => 'بخش‌های صفحه اصلی را بدون کدنویسی روشن/خاموش یا جابه‌جا کنید.', 'steps' => ['عنوان بخش را واضح بنویسید.', 'اگر نمی‌خواهید نمایش داده شود، گزینه نمایش را خاموش کنید.', 'ترتیب نمایش را با عددهای ۱، ۲، ۳ تنظیم کنید.']],
        'advertisements' => ['intro' => 'بنرهای تبلیغاتی را با جایگاه مشخص و لینک مقصد آماده کنید.', 'steps' => ['جایگاه نمایش تبلیغ را انتخاب کنید.', 'عنوان، تصویر و لینک مقصد را وارد کنید.', 'فعال بودن تبلیغ را روی بله بگذارید.']],
        'contents' => ['intro' => 'خبر و اطلاعیه را آماده کنید؛ تا زمانی که منتشر نشده باشد می‌تواند پیش‌نویس بماند.', 'steps' => ['نوع محتوا را خبر یا اطلاعیه انتخاب کنید.', 'عنوان، خلاصه و متن کامل را بنویسید.', 'وضعیت را پس از تایید مدیرکل روی منتشر شده بگذارید.']],
        'service_pages' => ['intro' => 'کارت‌های خدمات بالای سایت و صفحه توضیح هر خدمت از این بخش مدیریت می‌شود.', 'steps' => ['عنوان و خلاصه کوتاه خدمت را بنویسید.', 'آدرس یکتا را انگلیسی و کوتاه وارد کنید.', 'متن کامل راهنما را در محتوا بنویسید.']],
        'categories' => ['intro' => 'دسته‌بندی‌ها برای مرتب‌کردن خبر، اتحادیه و گردشگری استفاده می‌شود.', 'steps' => ['نوع دسته را انتخاب کنید.', 'عنوان قابل فهم وارد کنید.', 'توضیح کوتاه برای استفاده داخلی بنویسید.']],
        'systems' => ['intro' => 'لیست سامانه‌ها و لینک‌های کاربردی مردم را اینجا وارد کنید.', 'steps' => ['عنوان سامانه را بنویسید.', 'لینک مقصد را وارد کنید.', 'اگر سامانه بیرون از سایت است، لینک خارجی را فعال کنید.']],
        'guilds' => ['intro' => 'اطلاعات اصلی هر اتحادیه، رئیس، تلفن، توضیحات و فعال بودن شکایت از اینجا مدیریت می‌شود.', 'steps' => ['نام اتحادیه و رئیس را دقیق وارد کنید.', 'اگر شکایت باید نمایش داده شود، ثبت شکایت را فعال کنید.', 'خلاصه و توضیح صفحه اتحادیه را ساده و اداری بنویسید.']],
        'guild_page_blocks' => ['intro' => 'هر بخش صفحه جزئیات اتحادیه جداگانه اینجا مدیریت می‌شود؛ کاربر فقط اتحادیه، نوع بخش، عنوان و آیتم‌ها را تکمیل می‌کند.', 'steps' => ['اول اتحادیه را از لیست انتخاب کنید.', 'نوع بخش را مثل قوانین، نرخ‌نامه، آموزش یا گالری انتخاب کنید.', 'آیتم‌ها را طبق نمونه راهنما وارد و ترتیب نمایش را مشخص کنید.']],
        'guild_members' => ['intro' => 'اعضای هر اتحادیه برای نمایش داخلی و ارسال پیامک ثبت می‌شوند.', 'steps' => ['اتحادیه عضو را انتخاب کنید.', 'نام، موبایل و نام واحد صنفی را وارد کنید.', 'اگر عضو پیامک دریافت می‌کند، دریافت پیامک را فعال کنید.']],
        'complaints' => ['intro' => 'شکایت‌ها با کد رهگیری پیگیری می‌شوند و وضعیت رسیدگی از اینجا تغییر می‌کند.', 'steps' => ['شکایت را باز کنید و متن را بخوانید.', 'وضعیت را به در حال بررسی یا بسته شده تغییر دهید.', 'در صورت نیاز با شاکی تماس بگیرید.']],
        'sms_messages' => ['intro' => 'پیامک به اعضای اتحادیه یا یک شماره خاص از این بخش آماده می‌شود.', 'steps' => ['اتحادیه را انتخاب کنید.', 'گیرنده را همه اعضا یا یک شماره خاص بگذارید.', 'متن پیامک را کوتاه و رسمی بنویسید.']],
        'commissions' => ['intro' => 'صفحات معرفی کمیسیون‌ها و شرح وظایف آن‌ها را مدیریت کنید.', 'steps' => ['عنوان و خلاصه کمیسیون را وارد کنید.', 'شرح وظایف را در محتوا بنویسید.', 'ترتیب نمایش را تنظیم کنید.']],
        'commission_meetings' => ['intro' => 'جلسات هر کمیسیون و خلاصه مصوبات را ثبت کنید.', 'steps' => ['کمیسیون را انتخاب کنید.', 'عنوان جلسه و تاریخ را وارد کنید.', 'خلاصه مصوبات را بنویسید.']],
        'tourism_places' => ['intro' => 'مکان‌های گردشگری، تصویر و توضیحات هر مکان از این بخش قابل تنظیم است.', 'steps' => ['دسته‌بندی مکان را انتخاب کنید.', 'عنوان، خلاصه و تصویر را وارد کنید.', 'اگر آماده نمایش است، فعال را روی بله بگذارید.']],
        'roles' => ['intro' => 'سطوح دسترسی کاربران پنل را ساده و قابل فهم تعریف کنید.', 'steps' => ['نام نقش مثل مدیرکل یا کارشناس اتحادیه بنویسید.', 'دسترسی‌ها را کوتاه و روشن وارد کنید.', 'بعداً این نقش به کاربران نسبت داده می‌شود.']],
    ];

    return $guides[$module] ?? ['intro' => 'اطلاعات این بخش را مرحله‌به‌مرحله تکمیل کنید.', 'steps' => ['رکورد را ایجاد یا ویرایش کنید.', 'فیلدهای فارسی و راهنما را بخوانید.', 'ذخیره کنید و نتیجه را در سایت بررسی کنید.']];
}

function adminQuickStart(): array
{
    return [
        ['title' => 'اول اطلاعات سایت را تنظیم کنید', 'text' => 'نام، تماس، لینک‌های بالا و متن‌های ثابت را اصلاح کنید.', 'module' => 'site_settings'],
        ['title' => 'منوها را بچینید', 'text' => 'منوی اصلی، دسترسی سریع و فوتر را با لینک‌های آماده بسازید.', 'module' => 'menus'],
        ['title' => 'اتحادیه‌ها را کامل کنید', 'text' => 'اطلاعات اتحادیه، اعضا و بخش‌های صفحه جزئیات را تکمیل کنید.', 'module' => 'guilds'],
        ['title' => 'خبر و اطلاعیه منتشر کنید', 'text' => 'محتوا را ثبت کنید و بعد از تایید مدیرکل منتشر کنید.', 'module' => 'contents'],
    ];
}

function adminSafeHasTable(string $table): bool
{
    try {
        return Schema::hasTable($table);
    } catch (Throwable) {
        return false;
    }
}

function adminRows(string $table): array
{
    if (! adminSafeHasTable($table)) {
        return [];
    }

    if ($table === 'guild_page_blocks' && adminSafeHasTable('guilds')) {
        return DB::table('guild_page_blocks')
            ->leftJoin('guilds', 'guilds.id', '=', 'guild_page_blocks.guild_id')
            ->select('guild_page_blocks.*', 'guilds.title as guild_title')
            ->orderBy('guilds.title')
            ->orderBy('guild_page_blocks.sort_order')
            ->limit(100)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    return DB::table($table)->latest('id')->limit(100)->get()->map(fn ($row) => (array) $row)->all();
}

function adminNormalizePayload(array $payload): array
{
    foreach ($payload as $key => $value) {
        if (in_array($key, ['enabled', 'active', 'important', 'complaints_enabled', 'sms_enabled', 'is_external'], true)) {
            $payload[$key] = (bool) $value;
        }
        if (in_array($key, ['value', 'permissions', 'features', 'gallery', 'settings', 'items'], true) && is_string($value)) {
            $decoded = json_decode($value, true);
            $payload[$key] = json_last_error() === JSON_ERROR_NONE ? json_encode($decoded, JSON_UNESCAPED_UNICODE) : json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        if ($value === '') {
            $payload[$key] = null;
        }
    }

    return $payload;
}


function adminFieldMeta(): array
{
    return [
        'position' => ['label' => 'جایگاه نمایش تبلیغ', 'help' => 'مشخص می‌کند بنر در کدام بخش سایت نمایش داده شود.', 'options' => ['hero-left' => 'صفحه اصلی - کنار اسلایدر', 'guild-page' => 'صفحه اتحادیه‌ها', 'footer' => 'فوتر سایت', 'sidebar' => 'ستون کناری صفحات']],
        'location' => ['label' => 'محل نمایش منو', 'help' => 'primary یعنی منوی بالای سایت، quick یعنی دسترسی سریع، footer یعنی فوتر.', 'options' => ['primary' => 'منوی اصلی بالای سایت', 'quick' => 'دسترسی سریع صفحه اصلی', 'footer' => 'منوی فوتر']],
        'parent_id' => ['label' => 'شناسه منوی والد', 'help' => 'اگر این آیتم زیرمنو است، شناسه منوی اصلی را وارد کنید؛ برای منوی اصلی خالی بگذارید.'],
        'sort_order' => ['label' => 'ترتیب نمایش', 'help' => 'عدد کوچک‌تر زودتر نمایش داده می‌شود.'],
        'url' => ['label' => 'لینک مقصد', 'help' => 'برای لینک داخلی مثل /news و برای لینک خارجی آدرس کامل مثل https://example.com وارد کنید.'],
        'is_external' => ['label' => 'لینک خارجی است؟', 'help' => 'اگر لینک به سایتی خارج از همین سایت می‌رود، بله را انتخاب کنید.'],
        'enabled' => ['label' => 'نمایش داده شود؟'],
        'active' => ['label' => 'فعال است؟'],
        'important' => ['label' => 'خبر مهم است؟'],
        'complaints_enabled' => ['label' => 'ثبت شکایت برای این اتحادیه فعال باشد؟'],
        'sms_enabled' => ['label' => 'دریافت پیامک فعال باشد؟'],
        'status' => ['label' => 'وضعیت', 'options' => ['draft' => 'پیش‌نویس', 'pending' => 'در انتظار تایید', 'published' => 'منتشر شده', 'new' => 'جدید', 'reviewing' => 'در حال بررسی', 'closed' => 'بسته شده']],
        'type' => ['label' => 'نوع محتوا/دسته', 'options' => ['خبر' => 'خبر', 'اطلاعیه' => 'اطلاعیه', 'guild' => 'اتحادیه', 'tourism' => 'گردشگری', 'content' => 'محتوا']],
        'video_type' => ['label' => 'نوع ویدیو', 'options' => ['upload' => 'آپلود مستقیم', 'aparat' => 'لینک آپارات', 'external' => 'لینک خارجی']],
        'recipient_type' => ['label' => 'گیرنده پیامک', 'options' => ['guild_members' => 'همه اعضای اتحادیه', 'single' => 'یک شماره خاص']],
        'block_type' => ['label' => 'نوع بخش صفحه اتحادیه', 'help' => 'انتخاب کنید این رکورد کدام قسمت صفحه جزئیات اتحادیه را کنترل می‌کند.', 'options' => ['head' => 'معرفی و رئیس اتحادیه', 'board' => 'هیئت مدیره و اعضا', 'access' => 'سطوح دسترسی و امکانات', 'commissions' => 'کمیسیون‌های اتحادیه', 'rules' => 'قوانین و دستورالعمل‌ها', 'news_slider' => 'اسلایدر خبری اتحادیه', 'latest_news' => 'آخرین اخبار اتحادیه', 'articles' => 'مقالات و محتوای آموزشی', 'prices' => 'نرخ‌نامه و تعرفه‌ها', 'complaint' => 'ثبت شکایت صنفی', 'minutes' => 'صورتجلسه‌ها', 'education' => 'آموزش اعضا', 'announcements' => 'اطلاعیه و بخشنامه‌ها', 'gallery' => 'گالری تصاویر', 'search' => 'جستجو در اتحادیه', 'contact' => 'اطلاعات تماس و پاسخگویی']],
        'items' => ['label' => 'آیتم‌های این بخش', 'help' => 'آیتم‌ها را به‌صورت JSON وارد کنید؛ مثال: [{"title":"عنوان","summary":"توضیح","icon":"📋"}]. برای گالری، image و title وارد کنید.'],
        'subtitle' => ['label' => 'توضیح کوتاه بخش', 'help' => 'این متن زیر عنوان بخش در صفحه اتحادیه نمایش داده می‌شود.'],
        'key' => ['label' => 'کلید تنظیمات', 'help' => 'کلید فنی تنظیمات؛ فقط در صورت نیاز تغییر دهید.'],
        'value' => ['label' => 'مقدار تنظیمات', 'help' => 'می‌تواند متن یا JSON باشد. برای کاربران عادی بهتر است فقط متن‌های موجود را اصلاح کنید.'],
        'slug' => ['label' => 'آدرس یکتا', 'help' => 'فقط حروف انگلیسی، عدد و خط تیره؛ مثال: market-news'],
        'gallery' => ['label' => 'گالری تصاویر', 'help' => 'آدرس تصاویر را به‌صورت JSON یا متن وارد کنید.'],
        'features' => ['label' => 'امکانات', 'help' => 'لیست امکانات را با JSON یا متن ساده وارد کنید.'],
        'permissions' => ['label' => 'دسترسی‌ها', 'help' => 'دسترسی‌ها را به‌صورت لیست JSON یا متن وارد کنید.'],
    ];
}

function adminFieldConfig(string $field): array
{
    return adminFieldMeta()[$field] ?? [];
}

function adminLabels(): array
{
    return [
        'id' => 'شناسه', 'title' => 'عنوان', 'name' => 'نام', 'status' => 'وضعیت', 'url' => 'لینک', 'slug' => 'نامک',
        'location' => 'جایگاه', 'parent_id' => 'والد', 'sort_order' => 'ترتیب', 'enabled' => 'فعال', 'key' => 'کلید',
        'type' => 'نوع', 'summary' => 'خلاصه', 'content' => 'محتوا', 'permissions' => 'دسترسی‌ها', 'value' => 'مقدار',
        'phone' => 'تلفن', 'mobile' => 'موبایل', 'body' => 'متن', 'important' => 'مهم', 'active' => 'فعال', 'position' => 'جایگاه نمایش', 'location' => 'محل منو', 'parent_id' => 'والد', 'is_external' => 'لینک خارجی', 'category_id' => 'دسته‌بندی', 'guild_id' => 'اتحادیه', 'chairman' => 'رئیس اتحادیه', 'members_count' => 'تعداد اعضا', 'tracking_code' => 'کد رهگیری', 'recipient_type' => 'گیرنده', 'recipient_mobile' => 'موبایل گیرنده', 'approved_by' => 'تاییدکننده', 'video_type' => 'نوع ویدیو', 'video_url' => 'لینک ویدیو', 'block_type' => 'نوع بخش', 'guild_title' => 'نام اتحادیه', 'subtitle' => 'توضیح کوتاه', 'items' => 'آیتم‌ها',
    ];
}

function asnafViewData(array $extra = []): array
{
    $data = asnafData();

    return array_merge($data, [
        'jalaliDate' => JalaliDate::today(),
        'topLinks' => $data['top_links'] ?? [],
        'homeSections' => collect($data['home_sections'])->sortBy('order')->values()->all(),
        'importantNews' => collect($data['news'])->where('important', true)->where('status', 'published')->values(),
        'managerMessages' => $data['manager_messages'],
    ], $extra);
}

function findBySlug(array $items, string $slug): array
{
    return Arr::first($items, fn (array $item) => ($item['slug'] ?? null) === $slug) ?? abort(404);
}

Route::get('/', fn () => view('site.home', asnafViewData()))->name('home');

Route::get('/services', fn () => view('site.list', asnafViewData([
    'title' => 'خدمات الکترونیک صنفی',
    'description' => 'خدمات بالای سایت، صفحات توضیحی و لینک‌های داخلی/خارجی از پنل مدیریت قابل تنظیم هستند.',
    'items' => asnafData()['services'],
])))->name('services.index');
Route::get('/services/{slug}', fn (string $slug) => view('site.detail', asnafViewData(['item' => findBySlug(asnafData()['services'], $slug)])))->name('services.show');

Route::get('/news', fn () => view('site.list', asnafViewData([
    'title' => 'اخبار و اطلاعیه‌ها',
    'description' => 'خبر، اطلاعیه، گالری تصاویر و ویدیو پس از تایید مدیرکل منتشر می‌شود.',
    'items' => asnafData()['news'],
])))->name('news.index');
Route::get('/news/{slug}', fn (string $slug) => view('site.detail', asnafViewData(['item' => findBySlug(asnafData()['news'], $slug)])))->name('news.show');
Route::get('/announcements', fn () => view('site.list', asnafViewData(['title' => 'اطلاعیه‌ها', 'items' => collect(asnafData()['news'])->where('type', 'اطلاعیه')->values()->all()])))->name('announcements.index');

Route::get('/guilds', fn () => view('site.list', asnafViewData([
    'title' => 'اتحادیه‌های صنفی',
    'description' => 'هر اتحادیه صفحه مستقل، امکانات اختصاصی، عضو، پیامک و تنظیم نمایش شکایت دارد.',
    'items' => asnafData()['guilds'],
])))->name('guilds.index');
Route::get('/guilds/{slug}', function (string $slug) {
    $data = asnafData();
    $guild = findBySlug($data['guilds'], $slug);
    $guildId = adminSafeHasTable('guilds') ? DB::table('guilds')->where('slug', $slug)->value('id') : null;
    $members = ($guildId && adminSafeHasTable('guild_members'))
        ? DB::table('guild_members')->where('guild_id', $guildId)->get()->map(fn ($member) => (array) $member)->all()
        : [];
    if (! $members) {
        $members = [
            ['name' => $guild['chairman'] ?: 'رئیس اتحادیه', 'business_name' => 'رئیس اتحادیه'],
            ['name' => 'کارشناس اتحادیه', 'business_name' => 'کارشناس رسیدگی'],
            ['name' => 'بازرس اتحادیه', 'business_name' => 'بازرسی و نظارت'],
            ['name' => 'مسئول آموزش', 'business_name' => 'آموزش اعضا'],
        ];
    }
    $news = collect($data['news'])->take(4)->values()->all();
    $announcements = collect($data['news'])->where('type', 'اطلاعیه')->values()->all();
    if (! $announcements) {
        $announcements = $news;
    }

    $fallbackBlocks = [
        'head' => ['title' => 'رییس '.$guild['title'], 'subtitle' => $guild['content'] ?? $guild['summary'], 'enabled' => true, 'items' => []],
        'board' => ['title' => 'هیئت مدیره و اعضای نمونه', 'subtitle' => 'اعضا و مسئولان قابل مدیریت اتحادیه', 'enabled' => true, 'items' => []],
        'access' => ['title' => 'سطوح دسترسی و امکانات اتحادیه', 'subtitle' => 'نقش‌ها و امکانات قابل نمایش برای این اتحادیه', 'enabled' => true, 'items' => []],
        'commissions' => ['title' => 'کمیسیون‌های اتحادیه', 'subtitle' => 'کمیسیون‌ها و کارگروه‌های مرتبط با اتحادیه', 'enabled' => true, 'items' => []],
        'rules' => ['title' => 'قوانین و دستورالعمل‌ها', 'enabled' => true, 'items' => [
            ['icon' => '📋', 'title' => 'دستورالعمل فعالیت صنفی', 'summary' => 'ضوابط فعالیت، تمدید پروانه و مدارک مورد نیاز اعضای اتحادیه.'],
            ['icon' => '⚖️', 'title' => 'رسیدگی به شکایات', 'summary' => 'فرآیند ثبت، ارجاع، بررسی و پاسخ‌دهی به شکایات مردمی.'],
            ['icon' => '🧾', 'title' => 'صدور فاکتور و شفافیت', 'summary' => 'الزامات ثبت اطلاعات فروش، خدمات و اطلاع‌رسانی به مصرف‌کننده.'],
            ['icon' => '🛡️', 'title' => 'بازرسی و نظارت', 'summary' => 'برنامه‌های نظارتی اتحادیه و تعامل با اتاق اصناف.'],
        ]],
        'articles' => ['enabled' => true, 'title' => 'مقالات و محتوای آموزشی', 'items' => [
            ['title' => 'راهنمای استفاده از خدمات '.$guild['title'], 'summary' => 'نکات کاربردی برای اعضا و مراجعه‌کنندگان این اتحادیه.'],
            ['title' => 'حقوق مصرف‌کننده در '.$guild['category'], 'summary' => 'آشنایی با حقوق شهروندان و وظایف واحدهای صنفی.'],
            ['title' => 'آموزش قوانین نظام صنفی', 'summary' => 'مرور الزامات قانونی و اداری برای فعالان صنفی.'],
        ]],
        'prices' => ['enabled' => true, 'title' => 'نرخ‌نامه و تعرفه‌ها', 'items' => [
            ['title' => 'تعرفه خدمات پایه', 'amount' => 'طبق نرخ مصوب', 'type' => 'مصوب اتحادیه'],
            ['title' => 'هزینه کارشناسی پرونده', 'amount' => 'قابل تنظیم در پنل', 'type' => 'خدمات اداری'],
            ['title' => 'هزینه آموزش اعضا', 'amount' => 'قابل تنظیم در پنل', 'type' => 'آموزشی'],
        ]],
        'minutes' => ['enabled' => true, 'title' => 'صورتجلسه‌ها و مصوبات', 'items' => [
            ['title' => 'صورتجلسه هیئت مدیره '.$guild['title']],
            ['title' => 'صورتجلسه کمیسیون رسیدگی و نظارت'],
            ['title' => 'صورتجلسه برنامه‌ریزی آموزش اعضا'],
        ]],
        'education' => ['enabled' => true, 'title' => 'آموزش اعضا', 'items' => [
            ['icon' => '📚', 'title' => 'قوانین نظام صنفی', 'summary' => 'آموزش مقررات و تکالیف قانونی'],
            ['icon' => '🔍', 'title' => 'بازرسی و استاندارد', 'summary' => 'آشنایی با شاخص‌های نظارت'],
            ['icon' => '💰', 'title' => 'مالیات و حسابداری', 'summary' => 'اصول پرونده مالیاتی اعضا'],
            ['icon' => '🛡️', 'title' => 'حقوق مصرف‌کننده', 'summary' => 'صیانت از حقوق شهروندان'],
        ]],
        'news_slider' => ['title' => 'اسلایدر خبری اتحادیه', 'enabled' => true, 'items' => []],
        'latest_news' => ['title' => 'آخرین اخبار '.$guild['title'], 'enabled' => true, 'items' => []],
        'complaint' => ['title' => 'ثبت شکایت صنفی', 'subtitle' => 'ثبت و پیگیری شکایت با کد رهگیری', 'enabled' => true, 'items' => []],
        'announcements' => ['title' => 'اطلاعیه و بخشنامه‌ها', 'enabled' => true, 'items' => []],
        'gallery' => ['title' => 'گالری تصاویر و ویدیو', 'enabled' => true, 'items' => []],
        'search' => ['title' => 'جستجو در '.$guild['title'], 'subtitle' => 'عبارت مورد نظر خود را در میان اخبار، اعضا، قوانین و اطلاعات اتحادیه جستجو کنید', 'enabled' => true, 'items' => []],
        'contact' => ['title' => 'تماس با '.$guild['title'], 'enabled' => true, 'items' => []],
    ];

    $guildBlocks = $fallbackBlocks;
    if ($guildId && adminSafeHasTable('guild_page_blocks')) {
        $dbBlocks = DB::table('guild_page_blocks')
            ->where('guild_id', $guildId)
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(fn ($block) => [$block->block_type => [
                'title' => $block->title,
                'subtitle' => $block->subtitle,
                'enabled' => (bool) $block->enabled,
                'items' => asnafJson($block->items),
            ]])
            ->all();
        $guildBlocks = array_replace($guildBlocks, $dbBlocks);
    }

    $gallery = $guildBlocks['gallery']['items'] ?? collect($news)->flatMap(fn ($item) => $item['gallery'] ?? [])->filter()->take(4)->values()->all();
    $gallery = collect($gallery)
        ->map(fn ($item) => is_array($item) ? ($item['image'] ?? $item['url'] ?? $data['site']['hero_image']) : $item)
        ->filter()
        ->values()
        ->all();
    if (! $gallery) {
        $gallery = array_fill(0, 4, $data['site']['hero_image']);
    }

    return view('site.guild-detail', asnafViewData([
        'guild' => $guild,
        'members' => $members,
        'commissions' => $data['commissions'],
        'news' => $news,
        'announcements' => $announcements,
        'gallery' => $gallery,
        'guildBlocks' => $guildBlocks,
        'rules' => $guildBlocks['rules']['items'] ?? [],
        'articles' => $guildBlocks['articles']['items'] ?? [],
        'prices' => $guildBlocks['prices']['items'] ?? [],
        'minutes' => $guildBlocks['minutes']['items'] ?? [],
        'educations' => $guildBlocks['education']['items'] ?? [],
    ]));
})->name('guilds.show');

Route::get('/tourism', fn () => view('site.list', asnafViewData([
    'title' => 'گردشگری و بازار گرگان',
    'description' => 'مکان‌های گردشگری با دسته‌بندی اختصاصی و صفحات قابل ویرایش نمایش داده می‌شوند.',
    'items' => asnafData()['tourism'],
])))->name('tourism.index');
Route::get('/tourism/{slug}', fn (string $slug) => view('site.detail', asnafViewData(['item' => findBySlug(asnafData()['tourism'], $slug)])))->name('tourism.show');

Route::get('/commissions', fn () => view('site.list', asnafViewData(['title' => 'کمیسیون‌های اتاق اصناف', 'items' => asnafData()['commissions']])))->name('commissions.index');
Route::get('/systems', fn () => view('site.list', asnafViewData(['title' => 'لیست سامانه‌ها', 'items' => asnafData()['systems']])))->name('systems.index');
Route::get('/gallery', fn () => view('site.list', asnafViewData(['title' => 'گالری تصاویر', 'items' => asnafData()['news']])))->name('gallery.index');
Route::get('/videos', fn () => view('site.list', asnafViewData(['title' => 'ویدیوها', 'description' => 'ویدیوها می‌توانند آپلود مستقیم یا لینک آپارات باشند.', 'items' => asnafData()['news']])))->name('videos.index');

Route::get('/categories', fn () => view('site.list', asnafViewData([
    'title' => 'دسته‌بندی‌ها و رسته‌های صنفی',
    'description' => 'دسته‌بندی‌ها برای اتصال اخبار، اتحادیه‌ها، گردشگری و خدمات قابل توسعه هستند.',
    'items' => collect(asnafData()['guilds'])->map(fn (array $guild) => [
        'title' => $guild['category'],
        'category' => 'دسته‌بندی صنفی',
        'summary' => 'نمونه رسته مرتبط با '.$guild['title'],
    ])->unique('title')->values()->all(),
])))->name('categories.index');

Route::get('/page/{slug}', fn (string $slug) => view('site.detail', asnafViewData(['item' => [
    'title' => match ($slug) {
        'about' => 'معرفی اتاق اصناف گرگان',
        'board' => 'هیئت رئیسه و ساختار اداری',
        default => 'صفحه داخلی قابل ویرایش',
    },
    'category' => 'صفحه ثابت',
    'summary' => 'این صفحه از پنل مدیریت، منوساز و ادیتور محتوایی قابل تکمیل است.',
    'content' => 'مدیر می‌تواند عنوان، آدرس، متن، تصویر شاخص، فایل‌ها و وضعیت تایید مدیرکل را برای این صفحه تنظیم کند.',
]])))->name('pages.show');

Route::get('/contact', fn () => view('site.list', asnafViewData(['title' => 'تماس و ارتباط با ما', 'items' => [[
    'title' => asnafData()['site']['name'],
    'category' => 'تماس با ما',
    'summary' => asnafData()['site']['address'].' - '.asnafData()['site']['phone'].' - '.asnafData()['site']['email'],
]]])))->name('contact');

Route::get('/complaints/create', fn () => view('site.complaint', asnafViewData()))->name('complaints.create');
Route::post('/complaints', function () {
    $trackingCode = 'ASN-'.JalaliDate::faNumber(now()->format('Ymd-His')).'-'.JalaliDate::faNumber(random_int(100, 999));

    if (Schema::hasTable('complaints')) {
        $guildId = DB::table('guilds')->where('title', request('guild'))->value('id');
        Complaint::create([
            'guild_id' => $guildId,
            'tracking_code' => $trackingCode,
            'name' => (string) request('name'),
            'mobile' => (string) request('mobile'),
            'body' => (string) request('body'),
            'status' => 'new',
        ]);
    }

    return view('site.complaint', asnafViewData(['trackingCode' => $trackingCode]));
})->name('complaints.store');

Route::get('/search', function () {
    $query = trim((string) request('q'));
    $data = asnafData();
    $pool = collect([...$data['services'], ...$data['news'], ...$data['guilds'], ...$data['tourism'], ...$data['systems'], ...$data['commissions']]);
    $items = $query === '' ? [] : $pool->filter(fn (array $item) => Str::contains(($item['title'] ?? '').' '.($item['summary'] ?? '').' '.($item['description'] ?? ''), $query, true))->values()->all();

    return view('site.list', asnafViewData(['title' => 'نتایج جستجو برای «'.$query.'»', 'items' => $items]));
})->name('search');


Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => view('auth.login', asnafViewData()))->name('login');
    Route::post('/login', function () {
        $credentials = request()->validate(['email' => ['required', 'email'], 'password' => ['required']]);
        if (Auth::attempt($credentials, (bool) request('remember'))) {
            request()->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['email' => 'اطلاعات ورود صحیح نیست.'])->onlyInput('email');
    })->name('login.store');

    Route::get('/register', fn () => view('auth.register', asnafViewData()))->name('register');
    Route::post('/register', function () {
        $data = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);
        $user = User::create(['name' => $data['name'], 'email' => $data['email'], 'password' => Hash::make($data['password'])]);
        Auth::login($user);

        return redirect()->route('admin.dashboard');
    })->name('register.store');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', fn () => view('admin.dashboard', asnafViewData(['modules' => adminModules()])))->name('dashboard');

    Route::get('/module/{module}', function (string $module) {
        $modules = adminModules();
        abort_unless(isset($modules[$module]), 404);
        $meta = $modules[$module];
        $rows = adminRows($meta['table']);

        return view('admin.module', asnafViewData([
            'module' => $module,
            'title' => $meta['title'],
            'rows' => $rows,
            'columns' => $meta['columns'],
            'editable' => $meta['editable'],
            'labels' => adminLabels(),
            'meta' => $meta,
        ]));
    })->name('module');

    Route::get('/module/{module}/create', function (string $module) {
        $modules = adminModules();
        abort_unless(isset($modules[$module]), 404);
        $meta = $modules[$module];

        return view('admin.module-edit', asnafViewData([
            'module' => $module,
            'title' => 'افزودن '.$meta['title'],
            'row' => ['id' => null],
            'editable' => $meta['editable'],
            'labels' => adminLabels(),
            'meta' => $meta,
            'isCreate' => true,
        ]));
    })->name('module.create');

    Route::post('/module/{module}', function (string $module) {
        $modules = adminModules();
        abort_unless(isset($modules[$module]), 404);
        $meta = $modules[$module];
        abort_unless(adminSafeHasTable($meta['table']), 404);
        $payload = adminNormalizePayload(Arr::only(request()->except(['_token', '_method']), $meta['editable']));
        $payload['created_at'] = now();
        $payload['updated_at'] = now();
        DB::table($meta['table'])->insert($payload);

        return redirect()->route('admin.module', $module)->with('status', 'رکورد جدید با موفقیت ثبت شد.');
    })->name('module.store');

    Route::get('/module/{module}/{id}/edit', function (string $module, int $id) {
        $modules = adminModules();
        abort_unless(isset($modules[$module]), 404);
        $meta = $modules[$module];
        abort_unless(adminSafeHasTable($meta['table']), 404);
        $row = (array) DB::table($meta['table'])->where('id', $id)->first() ?: abort(404);

        return view('admin.module-edit', asnafViewData([
            'module' => $module,
            'title' => 'ویرایش '.$meta['title'],
            'row' => $row,
            'editable' => $meta['editable'],
            'labels' => adminLabels(),
            'meta' => $meta,
        ]));
    })->name('module.edit');

    Route::put('/module/{module}/{id}', function (string $module, int $id) {
        $modules = adminModules();
        abort_unless(isset($modules[$module]), 404);
        $meta = $modules[$module];
        abort_unless(adminSafeHasTable($meta['table']), 404);
        $payload = adminNormalizePayload(Arr::only(request()->except(['_token', '_method']), $meta['editable']));
        $payload['updated_at'] = now();
        DB::table($meta['table'])->where('id', $id)->update($payload);

        return redirect()->route('admin.module', $module)->with('status', 'تغییرات با موفقیت ذخیره شد.');
    })->name('module.update');

    Route::delete('/module/{module}/{id}', function (string $module, int $id) {
        $modules = adminModules();
        abort_unless(isset($modules[$module]), 404);
        $meta = $modules[$module];
        abort_unless(adminSafeHasTable($meta['table']), 404);
        DB::table($meta['table'])->where('id', $id)->delete();

        return redirect()->route('admin.module', $module)->with('status', 'رکورد انتخاب‌شده حذف شد.');
    })->name('module.destroy');
});
