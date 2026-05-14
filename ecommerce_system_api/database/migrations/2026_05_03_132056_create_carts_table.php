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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            
            // دعم المستخدمين المسجلين والزوار
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('cookie_id')->nullable()->unique();
            
            // أعمدة الخصم والكوبونات
            $table->string('coupon_code')->nullable();
            $table->enum('coupon_type', ['fixed', 'percentage'])->nullable();
            $table->decimal('coupon_value', 12, 2)->nullable();
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            
            $table->timestamps();
            
            // Indexes لتحسين الأداء
            $table->index('user_id');
            $table->index('cookie_id');
            $table->index('coupon_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};