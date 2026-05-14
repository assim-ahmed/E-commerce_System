<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// استيراد الموديلات المرتبطة
use App\Models\User;
use App\Models\CartItem;
use App\Models\Coupon;

class Cart extends Model
{
    /**
     * الأعمدة التي يمكن تعبئتها بشكل جماعي (Mass Assignment)
     */
    protected $fillable = [
        'user_id',
        'cookie_id',
        'coupon_code',        // كود الكوبون المطبق
        'coupon_type',        // نوع الخصم (fixed / percentage)
        'coupon_value',       // قيمة الخصم الأصلية
        'discount_amount',    // قيمة الخصم الفعلية بالجنيه
        'subtotal',           // إجمالي المنتجات قبل الخصم
        'total',              // الإجمالي بعد الخصم
    ];

    /**
     * الأعمدة التي يجب تحويلها إلى أنواع بيانات أخرى
     */
    protected $casts = [
        'user_id' => 'integer',
        'coupon_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // ========== Relationships ==========

    /**
     * علاقة المستخدم صاحب السلة (للمستخدمين المسجلين)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * علاقة عناصر السلة (المنتجات المضافة)
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * علاقة الكوبون المطبق (اختيارية)
     */
    public function appliedCoupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_code', 'code');
    }

    // ========== Accessors (Mutators) ==========

    /**
     * حساب إجمالي السلة من الـ items (بدون استخدام القيمة المخزنة)
     */
    public function getCalculatedSubtotalAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->price_at_time * $item->quantity;
        });
    }

    /**
     * الحصول على إجمالي السلة (يستخدم القيمة المخزنة إن وجدت)
     */
    public function getTotalAttribute()
    {
        // إذا كانت القيمة total مخزنة ومحدثة، استخدمها
        if ($this->total > 0) {
            return $this->total;
        }
        
        // وإلا احسبها من الـ items
        return $this->items->sum(function ($item) {
            return $item->price_at_time * $item->quantity;
        });
    }

    /**
     * عدد المنتجات في السلة (مجموع الكميات)
     */
    public function getItemsCountAttribute()
    {
        return $this->items->sum('quantity');
    }

    /**
     * الحصول على نص الخصم للعرض
     */
    public function getDiscountTextAttribute(): string
    {
        if ($this->coupon_code) {
            if ($this->coupon_type === 'percentage') {
                return $this->coupon_value . '%';
            }
            return $this->coupon_value . ' ' . config('app.currency', 'EGP');
        }
        
        return 'لا يوجد خصم';
    }

    /**
     * التحقق مما إذا كانت السلة فيها خصم مطبق
     */
    public function getHasCouponAttribute(): bool
    {
        return !is_null($this->coupon_code);
    }

    /**
     * الحصول على قيمة التوفير (الخصم)
     */
    public function getSavingsAttribute(): float
    {
        return $this->discount_amount;
    }

    // ========== Scopes ==========

    /**
     * نطاق: السلات التي تحتوي على منتجات
     */
    public function scopeNonEmpty($query)
    {
        return $query->has('items');
    }

    /**
     * نطاق: السلات الفارغة
     */
    public function scopeEmpty($query)
    {
        return $query->doesntHave('items');
    }

    /**
     * نطاق: السلات التي عليها خصم
     */
    public function scopeWithCoupon($query)
    {
        return $query->whereNotNull('coupon_code');
    }

    /**
     * نطاق: سلات المستخدمين المسجلين
     */
    public function scopeForUsers($query)
    {
        return $query->whereNotNull('user_id');
    }

    /**
     * نطاق: سلات الزوار (guest)
     */
    public function scopeForGuests($query)
    {
        return $query->whereNotNull('cookie_id')->whereNull('user_id');
    }

    // ========== Methods ==========

    /**
     * حساب وتحديث الـ subtotal من الـ items
     */
    public function calculateSubtotal(): float
    {
        $this->subtotal = $this->items->sum(function ($item) {
            return $item->price_at_time * $item->quantity;
        });
        
        $this->save();
        
        return $this->subtotal;
    }

    /**
     * تطبيق كوبون على السلة
     */
    public function applyCoupon(Coupon $coupon): bool
    {
        // التأكد من صحة الكوبون للسلة الحالية
        if (!$coupon->isValidForSubtotal($this->subtotal)) {
            return false;
        }
        
        // حساب الخصم
        $discountAmount = $coupon->calculateDiscount($this->subtotal);
        
        // تطبيق بيانات الكوبون
        $this->coupon_code = $coupon->code;
        $this->coupon_type = $coupon->type;
        $this->coupon_value = $coupon->value;
        $this->discount_amount = $discountAmount;
        $this->total = $this->subtotal - $discountAmount;
        
        return $this->save();
    }

    /**
     * إزالة الكوبون من السلة
     */
    public function removeCoupon(): bool
    {
        $this->coupon_code = null;
        $this->coupon_type = null;
        $this->coupon_value = null;
        $this->discount_amount = 0;
        $this->total = $this->subtotal;
        
        return $this->save();
    }

    /**
     * التحقق من صحة الكوبون المطبق حالياً
     */
    public function isAppliedCouponValid(): bool
    {
        if (!$this->coupon_code) {
            return true; // لا يوجد كوبون، يعتبر صحيح
        }
        
        $coupon = Coupon::where('code', $this->coupon_code)->first();
        
        if (!$coupon) {
            return false;
        }
        
        return $coupon->isValidForSubtotal($this->subtotal);
    }

    /**
     * إعادة حساب السلة بالكامل (subtotal + discount + total)
     */
    public function recalculate(): void
    {
        // حساب الـ subtotal من الـ items
        $this->subtotal = $this->items->sum(function ($item) {
            return $item->price_at_time * $item->quantity;
        });
        
        // إذا كان هناك كوبون مطبق، أعد حسابه
        if ($this->coupon_code) {
            $coupon = Coupon::where('code', $this->coupon_code)->first();
            
            if ($coupon && $coupon->isValidForSubtotal($this->subtotal)) {
                $this->discount_amount = $coupon->calculateDiscount($this->subtotal);
                $this->total = $this->subtotal - $this->discount_amount;
            } else {
                // الكوبون غير صالح، قم بإزالته
                $this->coupon_code = null;
                $this->coupon_type = null;
                $this->coupon_value = null;
                $this->discount_amount = 0;
                $this->total = $this->subtotal;
            }
        } else {
            $this->discount_amount = 0;
            $this->total = $this->subtotal;
        }
        
        $this->save();
    }

    /**
     * تفريغ السلة (حذف جميع العناصر)
     */
    public function clear(): bool
    {
        // حذف جميع عناصر السلة
        $this->items()->delete();
        
        // إعادة تعيين بيانات الخصم
        $this->coupon_code = null;
        $this->coupon_type = null;
        $this->coupon_value = null;
        $this->discount_amount = 0;
        $this->subtotal = 0;
        $this->total = 0;
        
        return $this->save();
    }

    /**
     * التحقق مما إذا كانت السلة فارغة
     */
    public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }

    /**
     * التحقق مما إذا كانت السلة تحتوي على منتجات
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * دمج سلة زائر مع سلة مستخدم (عند تسجيل الدخول)
     */
    public function mergeWithUserCart(Cart $userCart): void
    {
        // نقل العناصر من سلة الزائر إلى سلة المستخدم
        foreach ($this->items as $item) {
            // التحقق إذا كان المنتج موجود بالفعل في سلة المستخدم
            $existingItem = $userCart->items()
                ->where('product_id', $item->product_id)
                ->where('product_variant_id', $item->product_variant_id)
                ->first();
            
            if ($existingItem) {
                // تحديث الكمية
                $existingItem->quantity += $item->quantity;
                $existingItem->save();
            } else {
                // نقل العنصر
                $item->cart_id = $userCart->id;
                $item->save();
            }
        }
        
        // حذف سلة الزائر بعد النقل
        $this->delete();
        
        // إعادة حساب سلة المستخدم
        $userCart->recalculate();
    }

    /**
     * الحصول على معرف السلة المناسب (للاستخدام في الـ API)
     */
    public function getIdentifierAttribute(): string
    {
        if ($this->user_id) {
            return 'user_' . $this->user_id;
        }
        
        return 'guest_' . $this->cookie_id;
    }

    // ========== Boot ==========

    /**
     * Boot the model
     */
    protected static function booted()
    {
        // قبل حفظ السلة، تأكد من تحديث الـ total
        static::saving(function ($cart) {
            if ($cart->subtotal > 0 && $cart->total == 0) {
                $cart->total = $cart->subtotal - $cart->discount_amount;
            }
        });
    }
}