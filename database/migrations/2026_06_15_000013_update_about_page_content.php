<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cms_pages')) {
            return;
        }

        $aboutContent = <<<'TEXT'
ClothStore — сучасний інтернет-магазин одягу для тих, хто цінує мінімалізм, комфорт і впевнений стиль кожного дня. Ми збираємо актуальні колекції, які легко поєднувати між собою.

Наша мета — зробити онлайн-шопінг простим: зручний каталог, детальні картки товарів, швидке оформлення замовлення та доставка по всій Україні.

Ми працюємо з перевіреними постачальниками, дбаємо про якість упаковки та підтримку клієнтів на кожному етапі покупки.
---VALUES---
Мінімалізм, зручність, стиль
Ми робимо акцент на якості сервісу, швидкій доставці та речах, які легко поєднувати між собою.
Сучасний дизайн, Зручна структура, Доставка по Україні, Орієнтація на клієнта
TEXT;

        DB::table('cms_pages')->where('slug', 'about')->update([
            'title' => 'Про ClothStore',
            'subtitle' => 'Сучасний одяг, зручний сервіс і доставка по всій Україні — все в одному місці.',
            'content' => $aboutContent,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        //
    }
};
