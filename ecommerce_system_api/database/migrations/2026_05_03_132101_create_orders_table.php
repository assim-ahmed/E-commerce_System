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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('address_id')->constrained();
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'cancelled',
                'refunded'
            ])->default('pending');

            // الأعمدة المالية بالترتيب المنطقي
            $table->decimal('subtotal', 12, 2)->default(0);           // إجمالي قبل الخصم
            $table->decimal('discount_amount', 12, 2)->default(0);    // قيمة الخصم
            $table->decimal('total', 12, 2);                          // الإجمالي بعد الخصم

            // أعمدة الكوبون (Snapshot)
            $table->string('coupon_code')->nullable();
            $table->enum('coupon_type', ['fixed', 'percentage'])->nullable();
            $table->decimal('coupon_value', 12, 2)->nullable();

            $table->timestamps();

            // Indexes
            $table->index('order_number');
            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
