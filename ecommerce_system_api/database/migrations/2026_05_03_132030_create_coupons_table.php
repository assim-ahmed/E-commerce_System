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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            
            // معلومات الكوبون الأساسية
            $table->string('code')->unique();              // كود الكوبون (WELCOME10, SAVE50, ...)
            $table->enum('type', ['fixed', 'percentage']); // نوع الخصم: ثابت أو نسبة مئوية
            $table->decimal('value', 12, 2);               // قيمة الخصم (50 جنيه أو 10%)
            
            // شروط الاستخدام
            $table->decimal('minimum_order_amount', 12, 2)->nullable(); // الحد الأدنى للطلب (اختياري)
            
            // تواريخ الصلاحية
            $table->dateTime('start_date')->nullable();    // تاريخ البدء (اختياري)
            $table->dateTime('end_date')->nullable();      // تاريخ الانتهاء (اختياري)
            
            // استخدامات الكوبون
            $table->integer('usage_limit')->nullable();    // أقصى عدد مرات للاستخدام (اختياري)
            $table->integer('used_count')->default(0);     // عدد مرات الاستخدام الفعلية
            
            // حالة الكوبون
            $table->boolean('is_active')->default(true);   // مفعل/غير مفعل
            
            // حقل للملاحظات (اختياري)
            $table->text('description')->nullable();
            
            $table->timestamps();
            
            // Indexes لتحسين الأداء
            $table->index('code');
            $table->index('is_active');
            $table->index(['start_date', 'end_date']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};