<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $data = config('asnaf');
        $now = now();

        DB::transaction(function () use ($data, $now): void {
            Schema::disableForeignKeyConstraints();
            foreach (['sms_messages', 'complaints', 'guild_page_blocks', 'role_user', 'roles', 'advertisements', 'systems', 'tourism_places', 'commission_meetings', 'commissions', 'contents', 'guild_members', 'guilds', 'service_pages', 'categories', 'home_sections', 'menus', 'site_settings'] as $table) {
                DB::table($table)->delete();
            }
            Schema::enableForeignKeyConstraints();

            foreach (['site', 'top_links', 'workflow', 'manager_messages'] as $key) {
                DB::table('site_settings')->insert([
                    'key' => $key,
                    'value' => json_encode($data[$key], JSON_UNESCAPED_UNICODE),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach ($data['menus'] as $location => $items) {
                foreach ($items as $order => $item) {
                    $parentId = DB::table('menus')->insertGetId([
                        'location' => $location,
                        'parent_id' => null,
                        'title' => $item['title'],
                        'url' => $item['url'],
                        'icon' => $item['icon'] ?? null,
                        'is_external' => str_starts_with($item['url'], 'http'),
                        'enabled' => true,
                        'sort_order' => $order + 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    foreach (($item['children'] ?? []) as $childOrder => $child) {
                        DB::table('menus')->insert([
                            'location' => $location,
                            'parent_id' => $parentId,
                            'title' => $child['title'],
                            'url' => $child['url'],
                            'icon' => $child['icon'] ?? null,
                            'is_external' => str_starts_with($child['url'], 'http'),
                            'enabled' => true,
                            'sort_order' => $childOrder + 1,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
            }

            foreach ($data['home_sections'] as $section) {
                DB::table('home_sections')->insert([
                    'key' => $section['key'],
                    'title' => $section['title'],
                    'enabled' => $section['enabled'],
                    'sort_order' => $section['order'],
                    'settings' => json_encode([], JSON_UNESCAPED_UNICODE),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $categoryIds = [];
            foreach (['توزیعی', 'خدماتی', 'تولیدی و فنی', 'بازار و خرید', 'طبیعت', 'فرهنگی', 'اخبار', 'اطلاعیه‌ها'] as $index => $title) {
                $categoryIds[$title] = DB::table('categories')->insertGetId([
                    'type' => $index < 3 ? 'guild' : ($index < 6 ? 'tourism' : 'content'),
                    'title' => $title,
                    'slug' => 'category-'.($index + 1),
                    'description' => 'دسته‌بندی قابل مدیریت برای '.$title,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach ($data['services'] as $order => $service) {
                DB::table('service_pages')->insert([
                    'slug' => $service['slug'],
                    'icon' => $service['icon'],
                    'title' => $service['title'],
                    'summary' => $service['summary'],
                    'content' => $service['content'],
                    'status' => 'published',
                    'sort_order' => $order + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $guildIds = [];
            foreach ($data['guilds'] as $guild) {
                $guildIds[$guild['slug']] = DB::table('guilds')->insertGetId([
                    'category_id' => $categoryIds[$guild['category']] ?? null,
                    'slug' => $guild['slug'],
                    'title' => $guild['title'],
                    'chairman' => $guild['chairman'],
                    'phone' => $guild['phone'],
                    'complaints_enabled' => $guild['complaints_enabled'],
                    'features' => json_encode($guild['features'], JSON_UNESCAPED_UNICODE),
                    'members_count' => $guild['members_count'],
                    'summary' => $guild['summary'],
                    'content' => 'صفحه اختصاصی '.$guild['title'].' شامل اخبار، اطلاعیه‌ها، اعضا، پیامک و امکانات فعال اتحادیه است.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach ($guildIds as $slug => $guildId) {
                for ($i = 1; $i <= 3; $i++) {
                    DB::table('guild_members')->insert([
                        'guild_id' => $guildId,
                        'name' => 'عضو نمونه '.$i,
                        'mobile' => '0911000000'.$i,
                        'business_name' => 'واحد صنفی نمونه '.$i,
                        'license_number' => 'LIC-'.$guildId.'-'.$i,
                        'sms_enabled' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }


            foreach ($guildIds as $slug => $guildId) {
                $guildTitle = DB::table('guilds')->where('id', $guildId)->value('title');
                $blocks = [
                    ['access', 'سطوح دسترسی و امکانات اتحادیه', 'نقش‌ها و دسترسی‌های مرتبط با پنل اتحادیه', [['title' => 'کارشناس اتحادیه', 'summary' => 'مشاهده اعضا، پاسخ شکایت، ارسال پیامک'], ['title' => 'خبرنگار', 'summary' => 'ثبت خبر، گالری و ویدیو برای تایید مدیرکل']]],
                    ['rules', 'قوانین و دستورالعمل‌ها', 'ضوابط قابل نمایش در صفحه اتحادیه', [['icon' => '📋', 'title' => 'دستورالعمل فعالیت صنفی', 'summary' => 'ضوابط فعالیت، تمدید پروانه و مدارک مورد نیاز'], ['icon' => '⚖️', 'title' => 'رسیدگی به شکایات', 'summary' => 'فرآیند ثبت، ارجاع و پاسخ‌دهی به شکایت']]],
                    ['articles', 'مقاله‌ها', 'محتوای آموزشی و راهنمای اعضا', [['title' => 'راهنمای استفاده از خدمات '.$guildTitle, 'summary' => 'نکات کاربردی برای اعضا و مراجعه‌کنندگان'], ['title' => 'حقوق مصرف‌کننده', 'summary' => 'آشنایی با وظایف واحدهای صنفی']]],
                    ['prices', 'نرخ نامه و تعرفه خدمات', 'تعرفه‌های قابل تنظیم اتحادیه', [['title' => 'تعرفه خدمات پایه', 'amount' => 'طبق نرخ مصوب', 'type' => 'مصوب اتحادیه'], ['title' => 'هزینه کارشناسی پرونده', 'amount' => 'قابل تنظیم در پنل', 'type' => 'خدمات اداری']]],
                    ['minutes', 'صورتجلسه‌های اجرایی', 'فایل‌ها و صورتجلسه‌های اتحادیه', [['title' => 'صورتجلسه هیئت مدیره '.$guildTitle], ['title' => 'صورتجلسه کمیسیون رسیدگی و نظارت']]],
                    ['education', 'آموزش', 'دوره‌ها و محتوای آموزشی', [['icon' => '📚', 'title' => 'قوانین نظام صنفی', 'summary' => 'آموزش مقررات و تکالیف قانونی'], ['icon' => '🛡️', 'title' => 'حقوق مصرف‌کننده', 'summary' => 'صیانت از حقوق شهروندان']]],
                    ['gallery', 'گالری تصاویر و ویدیو', 'تصاویر و ویدیوهای مرتبط با اتحادیه', [['image' => 'theme/assets/img/asnaf-gorgan-default.jpg'], ['image' => 'theme/assets/img/asnaf-gorgan-default.jpg']]],
                    ['contact', 'تماس با اتحادیه', 'اطلاعات تماس اختصاصی اتحادیه', [['label' => 'تلفن', 'value' => DB::table('guilds')->where('id', $guildId)->value('phone')], ['label' => 'آدرس', 'value' => $data['site']['address']]]],
                ];
                foreach ($blocks as $order => [$type, $title, $subtitle, $items]) {
                    DB::table('guild_page_blocks')->insert([
                        'guild_id' => $guildId,
                        'block_type' => $type,
                        'title' => $title,
                        'subtitle' => $subtitle,
                        'items' => json_encode($items, JSON_UNESCAPED_UNICODE),
                        'enabled' => true,
                        'sort_order' => $order + 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }

            foreach ($data['news'] as $news) {
                DB::table('contents')->insert([
                    'guild_id' => null,
                    'category_id' => $categoryIds[$news['type'] === 'خبر' ? 'اخبار' : 'اطلاعیه‌ها'] ?? null,
                    'slug' => $news['slug'],
                    'type' => $news['type'],
                    'title' => $news['title'],
                    'summary' => $news['summary'],
                    'content' => $news['summary'].' متن کامل این محتوا از پنل مدیریت و با تایید مدیرکل تکمیل و منتشر می‌شود.',
                    'status' => $news['status'],
                    'important' => $news['important'],
                    'published_at' => $news['status'] === 'published' ? $now : null,
                    'approved_by' => $news['approval'],
                    'gallery' => json_encode($news['gallery'], JSON_UNESCAPED_UNICODE),
                    'video_type' => 'aparat',
                    'video_url' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach ($data['commissions'] as $order => $commission) {
                $commissionId = DB::table('commissions')->insertGetId([
                    'slug' => $commission['slug'],
                    'title' => $commission['title'],
                    'summary' => $commission['summary'],
                    'content' => 'شرح وظایف، اعضا و مصوبات '.$commission['title'].' از این صفحه مدیریت می‌شود.',
                    'sort_order' => $order + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                foreach ($commission['meetings'] as $meeting) {
                    DB::table('commission_meetings')->insert([
                        'commission_id' => $commissionId,
                        'title' => $meeting,
                        'held_at' => $now->toDateString(),
                        'summary' => 'جلسه قابل نمایش در صفحه کمیسیون و قابل ویرایش در پنل.',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }

            foreach ($data['tourism'] as $place) {
                DB::table('tourism_places')->insert([
                    'category_id' => $categoryIds[$place['category']] ?? null,
                    'slug' => $place['slug'],
                    'title' => $place['title'],
                    'summary' => $place['summary'],
                    'content' => 'اطلاعات کامل مکان گردشگری، مسیر دسترسی، تصاویر و کسب‌وکارهای مرتبط از پنل مدیریت ثبت می‌شود.',
                    'image' => $place['image'],
                    'enabled' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach ($data['systems'] as $system) {
                DB::table('systems')->insert([
                    'slug' => $system['slug'],
                    'title' => $system['title'],
                    'url' => $system['url'],
                    'description' => $system['description'],
                    'is_external' => str_starts_with($system['url'], 'http'),
                    'enabled' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach ($data['ads'] as $ad) {
                DB::table('advertisements')->insert([
                    'position' => $ad['position'],
                    'title' => $ad['title'],
                    'url' => $ad['url'],
                    'image' => 'theme/assets/img/asnaf-gorgan-default.jpg',
                    'active' => $ad['active'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach ($data['roles'] as $role) {
                DB::table('roles')->insert([
                    'name' => $role['name'],
                    'permissions' => json_encode($role['permissions'], JSON_UNESCAPED_UNICODE),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $admin = User::updateOrCreate(
                ['email' => 'admin@asnaf.test'],
                ['name' => 'مدیرکل اتاق اصناف', 'password' => Hash::make('password')]
            );
            $adminRoleId = DB::table('roles')->where('name', 'مدیرکل')->value('id');
            DB::table('role_user')->insertOrIgnore(['role_id' => $adminRoleId, 'user_id' => $admin->id]);
        });
    }
}
