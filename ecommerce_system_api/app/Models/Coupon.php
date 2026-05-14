<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    /**
     * الأعمدة التي يمكن تعبئتها بشكل جماعي (Mass Assignment)
     */
    protected $fillable = [
        'code',                    // كود الكوبون
        'type',                    // fixed / percentage
        'value',                   // قيمة الخصم
        'minimum_order_amount',    // الحد الأدنى للطلب
        'start_date',              // تاريخ البدء
        'end_date',                // تاريخ الانتهاء
        'usage_limit',             // الحد الأقصى لعدد الاستخدامات
        'used_count',              // عدد مرات الاستخدام الفعلية
        'is_active',               // مفعل / غير مفعل
        'description',             // وصف الكوبون
    ];

    /**
     * الأعمدة التي يجب تحويلها إلى أنواع بيانات أخرى
     */
    protected $casts = [
        'value' => 'decimal:2',                    // قيمة الخصم
        'minimum_order_amount' => 'decimal:2',     // الحد الأدنى للطلب
        'start_date' => 'datetime',                // تاريخ البدء
        'end_date' => 'datetime',                  // تاريخ الانتهاء
        'usage_limit' => 'integer',                // حد الاستخدام
        'used_count' => 'integer',                 // عدد الاستخدامات
        'is_active' => 'boolean',                  // الحالة
    ];

    /**
     * العلاقات
     */
    
    /**
     * علاقة مع جدول orders (استخدام الكوبون في الطلبات)
     * ملاحظة: العلاقة باستخدام coupon_code لأن الكوبون قد يحذف والطلبات تحتفظ بالكود
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'coupon_code', 'code');
    }

    /**
     * النطاقات (Scopes) للاستعلامات
     */
    
    /**
     * نطاق: الكوبونات الصالحة حالياً (لم تنتهي صلاحيتها)
     */
    public function scopeValid($query)
    {
        $now = Carbon::now();
        
        return $query->where('is_active', true)
                     ->where(function ($q) use ($now) {
                         $q->where('start_date', '<=', $now)
                           ->orWhereNull('start_date');
                     })
                     ->where(function ($q) use ($now) {
                         $q->where('end_date', '>=', $now)
                           ->orWhereNull('end_date');
                     });
    }

    /**
     * نطاق: الكوبونات غير المنتهية (لم تصل لـ end_date)
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('end_date', '>=', Carbon::now())
              ->orWhereNull('end_date');
        });
    }

    /**
     * نطاق: الكوبونات المفعلة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * نطاق: الكوبونات المعطلة فقط
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * نطاق: الكوبونات من النوع الثابت (fixed)
     */
    public function scopeFixed($query)
    {
        return $query->where('type', 'fixed');
    }

    /**
     * نطاق: الكوبونات من النسبة المئوية (percentage)
     */
    public function scopePercentage($query)
    {
        return $query->where('type', 'percentage');
    }

    /**
     * نطاق: الكوبونات التي لم تصل إلى الحد الأقصى للاستخدام
     */
    public function scopeHasRemainingUses($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('usage_limit')
              ->orWhereRaw('used_count < usage_limit');
        });
    }

    /**
     * الدوال (Methods)
     */
    
    /**
     * التحقق من صحة الكوبون (هل يمكن استخدامه حالياً)
     */
    public function isValid(): bool
    {
        $now = Carbon::now();
        
        // التحقق من الحالة
        if (!$this->is_active) {
            return false;
        }
        
        // التحقق من تاريخ البدء
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }
        
        // التحقق من تاريخ الانتهاء
        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }
        
        // التحقق من حد الاستخدام
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }
        
        return true;
    }

    /**
     * التحقق مما إذا كان الكوبون صالحاً لمبلغ معين (الحد الأدنى)
     */
    public function isValidForSubtotal(float $subtotal): bool
    {
        if (!$this->isValid()) {
            return false;
        }
        
        if ($this->minimum_order_amount && $subtotal < $this->minimum_order_amount) {
            return false;
        }
        
        return true;
    }

    /**
     * حساب قيمة الخصم بناءً على إجمالي السلة
     */
    public function calculateDiscount(float $subtotal): float
    {
        // إذا كان الكوبون غير صالح، خصم = 0
        if (!$this->isValidForSubtotal($subtotal)) {
            return 0.0;
        }
        
        if ($this->type === 'fixed') {
            // الخصم الثابت لا يتجاوز إجمالي السلة
            return min($this->value, $subtotal);
        }
        
        // نوع percentage
        $discount = ($subtotal * $this->value) / 100;
        
        // الخصم لا يتجاوز إجمالي السلة
        return min($discount, $subtotal);
    }

    /**
     * زيادة عدد استخدامات الكوبون
     */
    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    /**
     * التحقق مما إذا كان الكوبون منتهي الصلاحية
     */
    public function isExpired(): bool
    {
        if (!$this->end_date) {
            return false;
        }
        
        return Carbon::now()->gt($this->end_date);
    }

    /**
     * التحقق مما إذا كان الكوبون لم يبدأ بعد
     */
    public function isNotStartedYet(): bool
    {
        if (!$this->start_date) {
            return false;
        }
        
        return Carbon::now()->lt($this->start_date);
    }

    /**
     * الحصول على نسبة الخصم كنص (للعرض)
     */
    public function getDiscountTextAttribute(): string
    {
        if ($this->type === 'percentage') {
            return $this->value . '%';
        }
        
        return $this->value . ' ' . config('app.currency', 'EGP');
    }

    /**
     * الحصول على حالة الكوبون كنص (للعرض)
     */
    public function getStatusTextAttribute(): string
    {
        if (!$this->is_active) {
            return 'معطل';
        }
        
        if ($this->isExpired()) {
            return 'منتهي الصلاحية';
        }
        
        if ($this->isNotStartedYet()) {
            return 'لم يبدأ بعد';
        }
        
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return 'استنفذ الاستخدام';
        }
        
        return 'صالح للاستخدام';
    }

    /**
     * الحصول على عدد الاستخدامات المتبقية
     */
    public function getRemainingUsesAttribute(): ?int
    {
        if (!$this->usage_limit) {
            return null;
        }
        
        return max(0, $this->usage_limit - $this->used_count);
    }
}