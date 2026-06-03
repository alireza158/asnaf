<?php

use App\Support\JalaliDate;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

function asnafData(): array
{
    return config('asnaf');
}

function asnafViewData(array $extra = []): array
{
    $data = asnafData();

    return array_merge($data, [
        'jalaliDate' => JalaliDate::today(),
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
Route::post('/complaints', fn () => view('site.complaint', asnafViewData([
    'trackingCode' => 'ASN-'.JalaliDate::faNumber(now()->format('Ymd-His')).'-'.JalaliDate::faNumber(random_int(100, 999)),
])))->name('complaints.store');

Route::get('/search', function () {
    $query = trim((string) request('q'));
    $data = asnafData();
    $pool = collect([...$data['services'], ...$data['news'], ...$data['guilds'], ...$data['tourism'], ...$data['systems'], ...$data['commissions']]);
    $items = $query === '' ? [] : $pool->filter(fn (array $item) => Str::contains(($item['title'] ?? '').' '.($item['summary'] ?? '').' '.($item['description'] ?? ''), $query, true))->values()->all();

    return view('site.list', asnafViewData(['title' => 'نتایج جستجو برای «'.$query.'»', 'items' => $items]));
})->name('search');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => view('admin.dashboard', asnafViewData()))->name('dashboard');
});
