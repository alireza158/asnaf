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
        'site_settings' => ['title' => 'تنظیمات سایت', 'table' => 'site_settings', 'columns' => ['id', 'key', 'value'], 'editable' => ['key', 'value']],
        'menus' => ['title' => 'منوهای پویا', 'table' => 'menus', 'columns' => ['id', 'location', 'title', 'url', 'parent_id', 'sort_order', 'enabled'], 'editable' => ['location', 'parent_id', 'title', 'url', 'icon', 'is_external', 'enabled', 'sort_order']],
        'home_sections' => ['title' => 'سکشن‌های صفحه اصلی', 'table' => 'home_sections', 'columns' => ['id', 'key', 'title', 'enabled', 'sort_order'], 'editable' => ['key', 'title', 'enabled', 'sort_order', 'settings']],
        'service_pages' => ['title' => 'خدمات الکترونیک', 'table' => 'service_pages', 'columns' => ['id', 'title', 'slug', 'status', 'sort_order'], 'editable' => ['slug', 'icon', 'title', 'summary', 'content', 'status', 'sort_order']],
        'contents' => ['title' => 'اخبار، اطلاعیه‌ها و محتوا', 'table' => 'contents', 'columns' => ['id', 'type', 'title', 'status', 'important', 'approved_by'], 'editable' => ['guild_id', 'category_id', 'slug', 'type', 'title', 'summary', 'content', 'status', 'important', 'approved_by', 'gallery', 'video_type', 'video_url']],
        'categories' => ['title' => 'دسته‌بندی‌ها', 'table' => 'categories', 'columns' => ['id', 'type', 'title', 'slug'], 'editable' => ['type', 'title', 'slug', 'description']],
        'guilds' => ['title' => 'اتحادیه‌ها', 'table' => 'guilds', 'columns' => ['id', 'title', 'chairman', 'phone', 'complaints_enabled', 'members_count'], 'editable' => ['category_id', 'slug', 'title', 'chairman', 'phone', 'complaints_enabled', 'features', 'members_count', 'summary', 'content']],
        'guild_members' => ['title' => 'اعضای اتحادیه‌ها', 'table' => 'guild_members', 'columns' => ['id', 'guild_id', 'name', 'mobile', 'business_name'], 'editable' => ['guild_id', 'name', 'mobile', 'business_name', 'license_number', 'sms_enabled']],
        'commissions' => ['title' => 'کمیسیون‌ها', 'table' => 'commissions', 'columns' => ['id', 'title', 'slug', 'sort_order'], 'editable' => ['slug', 'title', 'summary', 'content', 'sort_order']],
        'commission_meetings' => ['title' => 'جلسات کمیسیون‌ها', 'table' => 'commission_meetings', 'columns' => ['id', 'commission_id', 'title', 'held_at'], 'editable' => ['commission_id', 'title', 'held_at', 'summary']],
        'tourism_places' => ['title' => 'گردشگری', 'table' => 'tourism_places', 'columns' => ['id', 'title', 'category_id', 'enabled'], 'editable' => ['category_id', 'slug', 'title', 'summary', 'content', 'image', 'enabled']],
        'systems' => ['title' => 'سامانه‌ها', 'table' => 'systems', 'columns' => ['id', 'title', 'url', 'enabled'], 'editable' => ['slug', 'title', 'url', 'description', 'is_external', 'enabled']],
        'advertisements' => ['title' => 'تبلیغات', 'table' => 'advertisements', 'columns' => ['id', 'position', 'title', 'url', 'active'], 'editable' => ['position', 'title', 'url', 'image', 'active', 'starts_at', 'ends_at']],
        'roles' => ['title' => 'سطوح دسترسی', 'table' => 'roles', 'columns' => ['id', 'name', 'permissions'], 'editable' => ['name', 'permissions']],
        'complaints' => ['title' => 'شکایات', 'table' => 'complaints', 'columns' => ['id', 'tracking_code', 'guild_id', 'name', 'mobile', 'status'], 'editable' => ['guild_id', 'tracking_code', 'name', 'mobile', 'body', 'status']],
        'sms_messages' => ['title' => 'پیامک‌ها', 'table' => 'sms_messages', 'columns' => ['id', 'guild_id', 'recipient_type', 'recipient_mobile', 'status'], 'editable' => ['guild_id', 'sent_by', 'recipient_type', 'recipient_mobile', 'body', 'status']],
    ];
}

function adminLabels(): array
{
    return [
        'id' => 'شناسه', 'title' => 'عنوان', 'name' => 'نام', 'status' => 'وضعیت', 'url' => 'لینک', 'slug' => 'نامک',
        'location' => 'جایگاه', 'parent_id' => 'والد', 'sort_order' => 'ترتیب', 'enabled' => 'فعال', 'key' => 'کلید',
        'type' => 'نوع', 'summary' => 'خلاصه', 'content' => 'محتوا', 'permissions' => 'دسترسی‌ها', 'value' => 'مقدار',
        'phone' => 'تلفن', 'mobile' => 'موبایل', 'body' => 'متن', 'important' => 'مهم', 'active' => 'فعال',
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
Route::get('/guilds/{slug}', fn (string $slug) => view('site.detail', asnafViewData(['item' => findBySlug(asnafData()['guilds'], $slug)])))->name('guilds.show');

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
        $rows = Schema::hasTable($meta['table']) ? DB::table($meta['table'])->latest('id')->limit(100)->get()->map(fn ($row) => (array) $row)->all() : [];

        return view('admin.module', asnafViewData([
            'module' => $module,
            'title' => $meta['title'],
            'rows' => $rows,
            'columns' => $meta['columns'],
            'editable' => $meta['editable'],
            'labels' => adminLabels(),
        ]));
    })->name('module');

    Route::get('/module/{module}/{id}/edit', function (string $module, int $id) {
        $modules = adminModules();
        abort_unless(isset($modules[$module]), 404);
        $meta = $modules[$module];
        abort_unless(Schema::hasTable($meta['table']), 404);
        $row = (array) DB::table($meta['table'])->where('id', $id)->first() ?: abort(404);

        return view('admin.module-edit', asnafViewData([
            'module' => $module,
            'title' => 'ویرایش '.$meta['title'],
            'row' => $row,
            'editable' => $meta['editable'],
            'labels' => adminLabels(),
        ]));
    })->name('module.edit');

    Route::put('/module/{module}/{id}', function (string $module, int $id) {
        $modules = adminModules();
        abort_unless(isset($modules[$module]), 404);
        $meta = $modules[$module];
        abort_unless(Schema::hasTable($meta['table']), 404);
        $payload = Arr::only(request()->except(['_token', '_method']), $meta['editable']);
        foreach ($payload as $key => $value) {
            if (in_array($key, ['enabled', 'active', 'important', 'complaints_enabled', 'sms_enabled', 'is_external'], true)) {
                $payload[$key] = (bool) $value;
            }
        }
        $payload['updated_at'] = now();
        DB::table($meta['table'])->where('id', $id)->update($payload);

        return redirect()->route('admin.module', $module)->with('status', 'تغییرات با موفقیت ذخیره شد.');
    })->name('module.update');
});
