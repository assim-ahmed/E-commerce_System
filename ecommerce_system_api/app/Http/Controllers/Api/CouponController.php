<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\Services\CouponServiceInterface;
use App\Contracts\Services\CartServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    protected $couponService;
    protected $cartService;

    public function __construct(
        CouponServiceInterface $couponService,
        CartServiceInterface $cartService
    ) {
        $this->couponService = $couponService;
        $this->cartService = $cartService;
    }

    /**
     * Validate coupon (Public)
     * GET /api/coupons/validate/{code}
     */
    public function validateCoupon(string $code, Request $request)
    {
        $request->validate([
            'subtotal' => 'required|numeric|min:0'
        ]);

        $result = $this->couponService->validateCoupon($code, $request->subtotal);

        if (!$result['valid']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'code' => $result['coupon']->code,
                'type' => $result['coupon']->type,
                'value' => $result['coupon']->value,
                'discount_amount' => $result['discount_amount'],
                'total_after_discount' => $result['total_after_discount']
            ],
            'message' => 'الكوبون صالح'
        ]);
    }

    /**
     * Apply coupon to cart (Authenticated users)
     * POST /api/cart/apply-coupon
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50'
        ]);

        try {
            // الحصول على userId بطريقة آمنة
            $userId = Auth::check() ? Auth::id() : null;
            
            $result = $this->couponService->applyCouponToCart(
                $userId,
                $request->cookie('cart_cookie'),
                $request->coupon_code
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => $result['message']
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()['coupon_code'][0] ?? 'فشل تطبيق الكوبون',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Remove coupon from cart (Authenticated users)
     * DELETE /api/cart/coupon
     */
    public function removeCoupon(Request $request)
    {
        $userId = Auth::check() ? Auth::id() : null;
        
        $result = $this->couponService->removeCouponFromCart(
            $userId,
            $request->cookie('cart_cookie')
        );

        return response()->json([
            'success' => true,
            'data' => $result,
            'message' => $result['message']
        ]);
    }

    /**
     * Get all coupons (Admin only)
     * GET /api/admin/coupons
     */
    public function index()
    {
        $this->authorizeAdmin();

        $coupons = $this->couponService->getAllCoupons();

        return response()->json([
            'success' => true,
            'data' => $coupons,
            'message' => 'تم جلب جميع الكوبونات بنجاح'
        ]);
    }

    /**
     * Get single coupon (Admin only)
     * GET /api/admin/coupons/{id}
     */
    public function show(int $id)
    {
        $this->authorizeAdmin();

        try {
            $coupon = $this->couponService->getCouponById($id);
            
            return response()->json([
                'success' => true,
                'data' => $coupon,
                'message' => 'تم جلب بيانات الكوبون بنجاح'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()['id'][0] ?? 'الكوبون غير موجود'
            ], 404);
        }
    }

    /**
     * Create new coupon (Admin only)
     * POST /api/admin/coupons
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0|max:999999.99',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500'
        ]);

        try {
            $coupon = $this->couponService->createCoupon($validated);
            
            return response()->json([
                'success' => true,
                'data' => $coupon,
                'message' => 'تم إنشاء الكوبون بنجاح'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()['code'][0] ?? 'فشل إنشاء الكوبون',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Update coupon (Admin only)
     * PUT /api/admin/coupons/{id}
     */
    public function update(Request $request, int $id)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'code' => 'sometimes|string|max:50',
            'type' => 'sometimes|in:fixed,percentage',
            'value' => 'sometimes|numeric|min:0|max:999999.99',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500'
        ]);

        try {
            $coupon = $this->couponService->updateCoupon($id, $validated);
            
            return response()->json([
                'success' => true,
                'data' => $coupon,
                'message' => 'تم تحديث الكوبون بنجاح'
            ]);
        } catch (ValidationException $e) {
            $errorKey = array_key_first($e->errors());
            return response()->json([
                'success' => false,
                'message' => $e->errors()[$errorKey][0] ?? 'فشل تحديث الكوبون',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Delete coupon (Admin only)
     * DELETE /api/admin/coupons/{id}
     */
    public function destroy(int $id)
    {
        $this->authorizeAdmin();

        try {
            $this->couponService->deleteCoupon($id);
            
            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'تم حذف الكوبون بنجاح'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()['id'][0] ?? 'الكوبون غير موجود'
            ], 404);
        }
    }

    /**
     * Get active/valid coupons (Admin only)
     * GET /api/admin/coupons/active
     */
    public function getActiveCoupons()
    {
        $this->authorizeAdmin();

        $coupons = $this->couponService->getAllCoupons()->filter(function ($coupon) {
            return $coupon->isValid();
        })->values();

        return response()->json([
            'success' => true,
            'data' => $coupons,
            'message' => 'تم جلب الكوبونات النشطة بنجاح'
        ]);
    }

    /**
     * Authorize admin user
     */
    private function authorizeAdmin(): void
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            abort(403, 'غير مصرح بهذه العملية. هذه المنطقة مخصصة للإدارة فقط.');
        }
    }
}