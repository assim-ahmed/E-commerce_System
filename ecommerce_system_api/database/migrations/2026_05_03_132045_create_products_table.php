<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('brand_id')->constrained();
            $table->decimal('base_price', 12, 2);  // السعر الأساسي قبل المتغيرات
            $table->decimal('compare_price', 12, 2)->nullable();  // سعر الخصم
            $table->integer('stock_quantity')->default(0);
            $table->integer('low_stock_threshold')->default(5);  // للتحذير
            $table->boolean('is_low_stock')->default(false);  // حقل مشتق لسرعة التحذير
            $table->string('sku')->unique();
            $table->json('images')->nullable();
            $table->json('specifications')->nullable();  // المواصفات الفنية
            $table->boolean('is_featured')->default(false);
            $table->integer('views_count')->default(0);
            $table->timestamps();

            // Indexes للأداء مع 100k+ منتج
            $table->index('slug');
            $table->index('category_id');
            $table->index('brand_id');
            $table->index('is_featured');
            $table->index('sku');
            $table->index('created_at');
            $table->index('base_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
