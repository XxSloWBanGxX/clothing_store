<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('site_settings')) {
            Schema::create('site_settings', function (Blueprint $table) {
                $table->string('key', 100)->primary();
                $table->text('value')->nullable();
            });
        }

        if (! Schema::hasTable('cms_pages')) {
            Schema::create('cms_pages', function (Blueprint $table) {
                $table->id();
                $table->string('slug', 80)->unique();
                $table->string('title', 200);
                $table->string('subtitle', 500)->nullable();
                $table->longText('content')->nullable();
                $table->boolean('is_published')->default(true);
                $table->timestamp('updated_at')->nullable();
            });
        }

        if (! Schema::hasTable('promocodes')) {
            Schema::create('promocodes', function (Blueprint $table) {
                $table->id();
                $table->string('code', 32)->unique();
                $table->string('title', 120);
                $table->unsignedTinyInteger('discount_percent')->default(10);
                $table->decimal('min_order_amount', 10, 2)->nullable();
                $table->unsignedInteger('max_uses')->nullable();
                $table->unsignedInteger('uses_count')->default(0);
                $table->timestamp('expires_at')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (! Schema::hasTable('newsletter_subscribers')) {
            Schema::create('newsletter_subscribers', function (Blueprint $table) {
                $table->id();
                $table->string('email', 150)->unique();
                $table->timestamp('subscribed_at')->useCurrent();
                $table->timestamp('unsubscribed_at')->nullable();
            });
        }

        if (Schema::hasTable('reviews') && ! Schema::hasColumn('reviews', 'is_approved')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->boolean('is_approved')->default(true)->after('comment');
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (! Schema::hasColumn('orders', 'discount_amount')) {
                    $table->decimal('discount_amount', 10, 2)->default(0)->after('total_amount');
                }
                if (! Schema::hasColumn('orders', 'promocode')) {
                    $table->string('promocode', 32)->nullable()->after('discount_amount');
                }
            });
        }

        $this->seedDefaults();
    }

    private function seedDefaults(): void
    {
        $settings = [
            'brand_name' => 'CLOTHSTORE',
            'brand_lead' => 'CLOTH',
            'brand_accent' => 'STORE',
            'contact_email' => 'info@clothstore.local',
            'contact_phone' => '+380 99 000 00 00',
            'contact_location' => 'Україна',
            'instagram_url' => 'https://www.instagram.com/tori_cloth.store?utm_source=qr',
            'instagram_handle' => '@tori_cloth.store',
            'footer_description' => 'Сучасний одяг у мінімалістичному стилі. Зручний онлайн-магазин з доставкою по всій Україні.',
            'footer_strip_text' => 'Нова колекція вже в каталозі',
            'footer_strip_link_text' => 'Дивитись новинки →',
            'footer_strip_link_url' => '/new',
            'delivery_carriers' => 'Nova Poshta, Ukrposhta, Meest',
            'trust_payment_text' => 'Онлайн або при отриманні',
            'shipping_info' => 'Нова Пошта, Укрпошта, Meest — відправка протягом 1–2 робочих днів.',
            'returns_info' => '14 днів на повернення без зайвих питань.',
            'hero_badge' => 'NEW COLLECTION',
            'hero_title' => "Стиль, який\nговорить за тебе",
            'hero_text' => 'Сучасний одяг для тих, хто цінує мінімалізм, комфорт та впевнений вигляд кожного дня.',
            'hero_btn1_text' => 'Перейти в каталог',
            'hero_btn1_url' => '/catalog',
            'hero_btn2_text' => 'Дивитися новинки',
            'hero_btn2_url' => '/new',
            'hero_stat1_value' => '500+',
            'hero_stat1_label' => 'Товарів',
            'hero_stat2_value' => '24/7',
            'hero_stat2_label' => 'Онлайн замовлення',
            'hero_stat3_value' => '100%',
            'hero_stat3_label' => 'Сучасний стиль',
            'feature1_title' => 'Швидке оформлення',
            'feature1_text' => 'Простий і зручний процес покупки без зайвих кроків.',
            'feature2_title' => 'Актуальні колекції',
            'feature2_text' => 'Стильні моделі одягу в сучасному мінімалістичному стилі.',
            'feature3_title' => 'Зручний інтерфейс',
            'feature3_text' => 'Приємна навігація, сучасний дизайн і база для розвитку магазину.',
            'banner_label' => 'COLLECTION',
            'banner_title' => 'Онови свій гардероб вже сьогодні',
            'banner_text' => 'Підбери речі, які підкреслять твій стиль та зроблять магазин виглядом реально сучасним уже з головної сторінки.',
            'banner_btn_text' => 'До покупок',
            'banner_btn_url' => '/catalog',
            'new_products_days' => '30',
            'reviews_moderation' => '0',
        ];

        foreach ($settings as $key => $value) {
            if (! DB::table('site_settings')->where('key', $key)->exists()) {
                DB::table('site_settings')->insert(['key' => $key, 'value' => $value]);
            }
        }

        $pages = [
            [
                'slug' => 'about',
                'title' => 'Про ClothStore',
                'subtitle' => 'ClothStore — це сучасний інтернет-магазин одягу, створений для зручного, стильного та швидкого онлайн-шопінгу.',
                'content' => "Ми поєднуємо мінімалістичний дизайн, просту навігацію та сучасний підхід до представлення товарів.\n\nОсновна ідея ClothStore — створити платформу, де користувач може легко знайти потрібний товар, переглянути фото, вибрати розмір і колір, додати товар у кошик або обране та отримати комфортний досвід покупки.\n\nМи працюємо з перевіреними постачальниками, дбаємо про якість упаковки та швидку відправку замовлень по всій Україні.",
            ],
            [
                'slug' => 'privacy',
                'title' => 'Політика конфіденційності',
                'subtitle' => 'Як ми збираємо, використовуємо та захищаємо ваші персональні дані.',
                'content' => "1. Загальні положення\nМи поважаємо вашу конфіденційність і обробляємо персональні дані відповідно до чинного законодавства України.\n\n2. Які дані збираємо\nІмʼя, email, телефон, адреса доставки — лише для оформлення замовлень та звʼязку з вами.\n\n3. Використання даних\nДані використовуються для обробки замовлень, доставки, підтримки клієнтів та інформування про статус замовлення.\n\n4. Захист даних\nМи застосовуємо технічні та організаційні заходи для захисту вашої інформації.\n\n5. Контакти\nЗ питань конфіденційності звертайтесь на email магазину, вказаний у футері сайту.",
            ],
            [
                'slug' => 'cooperation',
                'title' => 'Співробітництво',
                'subtitle' => 'Запрошуємо бренди, постачальників та партнерів до спільної роботи.',
                'content' => "Ми відкриті до співпраці з виробниками одягу, аксесуарів та fashion-брендами.\n\nЩо пропонуємо:\n• Розміщення колекцій у нашому каталозі\n• Спільні промо-акції та розсилки\n• Прозорі умови співпраці\n\nНадішліть пропозицію на email магазину з темою «Співробітництво» — ми відповімо протягом 2–3 робочих днів.",
            ],
        ];

        foreach ($pages as $page) {
            if (! DB::table('cms_pages')->where('slug', $page['slug'])->exists()) {
                DB::table('cms_pages')->insert(array_merge($page, [
                    'is_published' => true,
                    'updated_at' => now(),
                ]));
            }
        }

        if (Schema::hasTable('promocodes') && ! DB::table('promocodes')->where('code', 'WELCOME10')->exists()) {
            DB::table('promocodes')->insert([
                'code' => 'WELCOME10',
                'title' => 'Знижка для нових клієнтів',
                'discount_percent' => 10,
                'min_order_amount' => null,
                'max_uses' => null,
                'uses_count' => 0,
                'expires_at' => null,
                'is_active' => true,
                'created_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('promocodes');
        Schema::dropIfExists('cms_pages');
        Schema::dropIfExists('site_settings');

        if (Schema::hasTable('reviews') && Schema::hasColumn('reviews', 'is_approved')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropColumn('is_approved');
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                foreach (['promocode', 'discount_amount'] as $column) {
                    if (Schema::hasColumn('orders', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
