<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

// استيراد الموديلات المرتبطة
use App\Models\User;
use App\Models\Address;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\Coupon;

class Order extends Model
{
    // ========== Status Constants ==========
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    // ========== Fillable ==========
    protected $fillable = [
        'order_number',
        'user_id',
        'address_id',
        'status',
        'subtotal',           // إجمالي المنتجات قبل الخصم
        'discount_amount',    // قيمة الخصم بالجنيه
        'total',              // الإجمالي بعد الخصم
        'coupon_code',        // كود الكوبون المستخدم
        'coupon_type',        // نوع الخصم (fixed / percentage) - Snapshot
        'coupon_value',       // قيمة الخصم الأصلية - Snapshot
    ];

    // ========== Casts ==========
    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'coupon_value' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========== Relationships ==========

    /**
     * علاقة المستخدم صاحب الطلب
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * علاقة عنوان التوصيل
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * علاقة عناصر الطلب (المنتجات)
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * علاقة التقييمات
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * علاقة الكوبون (ملاحظة: باستخدام الكود لأن الكوبون قد يحذف لاحقاً)
     * هذه علاقة اختيارية، قد لا يجد الكوبون إذا تم حذفه
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_code', 'code');
    }

    // ========== Accessors (Mutators) ==========

    /**
     * الحصول على إجمالي الطلب من الـ items (ديناميكي)
     * ملاحظة: يفضل استخدام subtotal المخزن بدلاً من هذا
     */
    public function getCalculatedSubtotalAttribute()
    {
        return $this->items->sum('total');
    }

    /**
     * الحصول على عدد المنتجات في الطلب
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
     * الحصول على نص حالة الطلب بالعربية
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_PROCESSING => 'قيد المعالجة',
            self::STATUS_COMPLETED => 'مكتمل',
            self::STATUS_CANCELLED => 'ملغي',
            self::STATUS_REFUNDED => 'تم الاسترجاع',
            default => $this->status,
        };
    }

    /**
     * الحصول على لون الحالة (للعرض في الواجهة)
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_PROCESSING => 'info',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'danger',
            self::STATUS_REFUNDED => 'secondary',
            default => 'light',
        };
    }

    /**
     * التحقق مما إذا كان الطلب مطبق عليه كوبون
     */
    public function getHasCouponAttribute(): bool
    {
        return !is_null($this->coupon_code);
    }

    // ========== Scopes ==========

    /**
     * نطاق: الطلبات قيد الانتظار
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * نطاق: الطلبات قيد المعالجة
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    /**
     * نطاق: الطلبات المكتملة
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * نطاق: الطلبات الملغية
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * نطاق: الطلبات المسترجعة
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', self::STATUS_REFUNDED);
    }

    /**
     * نطاق: الطلبات غير الملغية (مستثنى الملغية والمسترجعة)
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_CANCELLED, self::STATUS_REFUNDED]);
    }

    /**
     * نطاق: طلبات مستخدم محدد
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * نطاق: الطلبات التي استخدمت كوبون
     */
    public function scopeWithCoupon($query)
    {
        return $query->whereNotNull('coupon_code');
    }

    /**
     * نطاق: الطلبات بدون كوبون
     */
    public function scopeWithoutCoupon($query)
    {
        return $query->whereNull('coupon_code');
    }

    /**
     * نطاق: الطلبات في فترة زمنية محددة
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * نطاق: الطلبات التي يتجاوز إجماليها مبلغ معين
     */
    public function scopeTotalGreaterThan($query, $amount)
    {
        return $query->where('total', '>', $amount);
    }

    // ========== Methods ==========

    /**
     * تحديث حالة الطلب
     */
    public function updateStatus(string $newStatus): bool
    {
        // التحقق من صحة الحالة
        if (!in_array($newStatus, [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
            self::STATUS_REFUNDED
        ])) {
            return false;
        }
        
        // التحقق من إمكانية الانتقال إلى الحالة الجديدة
        if (!$this->canTransitionTo($newStatus)) {
            return false;
        }
        
        $this->status = $newStatus;
        return $this->save();
    }

    /**
     * التحقق من إمكانية تغيير حالة الطلب
     */
    public function canTransitionTo(string $newStatus): bool
    {
        // الحالات النهائية (لا يمكن التغيير منها)
        if (in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED, self::STATUS_REFUNDED])) {
            return false;
        }
        
        // pending -> أي حالة
        if ($this->status === self::STATUS_PENDING) {
            return in_array($newStatus, [self::STATUS_PROCESSING, self::STATUS_CANCELLED]);
        }
        
        // processing -> completed, cancelled
        if ($this->status === self::STATUS_PROCESSING) {
            return in_array($newStatus, [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
        }
        
        return false;
    }

    /**
     * التحقق مما إذا كان الطلب يمكن تقييمه
     */
    public function canBeReviewed(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * التحقق مما إذا كان الطلب ملغى
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * التحقق مما إذا كان الطلب مكتمل
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * التحقق مما إذا كان الطلب قيد المعالجة
     */
    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    /**
     * التحقق مما إذا كان الطلب قيد الانتظار
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * حساب إجمالي الطلب (باستخدام القيم المخزنة)
     */
    public function calculateTotal(): float
    {
        return $this->subtotal - $this->discount_amount;
    }

    /**
     * إعادة حساب الطلب بالكامل من الـ items
     */
    public function recalculate(): void
    {
        $subtotal = $this->items->sum(function ($item) {
            return $item->price_snapshot * $item->quantity;
        });
        
        $this->subtotal = $subtotal;
        $this->total = $subtotal - $this->discount_amount;
        $this->save();
    }

    // ========== Boot ==========

    /**
     * Boot the model
     */
    protected static function booted()
    {
        // إنشاء رقم طلب تلقائياً قبل الحفظ
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    /**
     * توليد رقم طلب فريد
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD-';
        $year = date('Y');
        $month = date('m');
        
        // الحصول على آخر رقم تسلسلي للشهر الحالي
        $lastOrder = static::whereYear('created_at', $year)
                           ->whereMonth('created_at', $month)
                           ->orderBy('id', 'desc')
                           ->first();
        
        if ($lastOrder && preg_match('/ORD-' . $year . $month . '-(\d+)/', $lastOrder->order_number, $matches)) {
            $sequence = (int) $matches[1] + 1;
        } else {
            $sequence = 1;
        }
        
        return $prefix . $year . $month . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}