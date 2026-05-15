<?php

namespace App\Services;

use App\Contracts\Repositories\CouponRepositoryInterface;
use App\Contracts\Repositories\CartRepositoryInterface;
use App\Contracts\Services\CouponServiceInterface;
use Illuminate\Validation\ValidationException;

class CouponService implements CouponServiceInterface
{
    protected $couponRepository;
    protected $cartRepository;

    public function __construct(
        CouponRepositoryInterface $couponRepository,
        CartRepositoryInterface $cartRepository
    ) {
        $this->couponRepository = $couponRepository;
        $this->cartRepository = $cartRepository;
    }

    /**
     * Resolve cart (same pattern as CartService)
     */
    private function resolveCart($userId, $cookieId)
    {
        if ($userId) {
            return $this->cartRepository->findOrCreateCartByUser($userId);
        }

        if ($cookieId) {
            return $this->cartRepository->findOrCreateCartByCookie($cookieId);
        }

        throw new \Exception('No user or cookie identifier provided');
    }

    /**
     * Validate coupon and calculate discount
     */
    public function validateCoupon(string $code, float $subtotal): array
    {
        $coupon = $this->couponRepository->findByCode($code);

        if (!$coupon) {
            return [
                'valid' => false,
                'message' => 'الكوبون غير موجود'
            ];
        }

        if (!$coupon->isValidForSubtotal($subtotal)) {
            return [
                'valid' => false,
                'message' => 'الكوبون غير صالح لهذا الطلب'
            ];
        }

        $discount = $coupon->calculateDiscount($subtotal);

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount_amount' => $discount,
            'total_after_discount' => $subtotal - $discount
        ];
    }

    /**
     * Apply coupon to cart
     */
    public function applyCouponToCart($userId, $cookieId, string $code): array
    {
        // Get cart
        $cart = $this->resolveCart($userId, $cookieId);
        
        // Calculate current subtotal from cart items
        $cartWithItems = $this->cartRepository->getCartWithItems($cart->id);
        $subtotal = $cartWithItems->items->sum(function ($item) {
            return $item->price_at_time * $item->quantity;
        });

        // Validate coupon
        $validation = $this->validateCoupon($code, $subtotal);
        
        if (!$validation['valid']) {
            throw ValidationException::withMessages([
                'coupon_code' => [$validation['message']]
            ]);
        }

        $coupon = $validation['coupon'];

        // Update cart with coupon data
        $cart->coupon_code = $coupon->code;
        $cart->coupon_type = $coupon->type;
        $cart->coupon_value = $coupon->value;
        $cart->discount_amount = $validation['discount_amount'];
        $cart->total = $validation['total_after_discount'];
        $cart->save();

        // Return updated cart data (same format as CartService@getCart)
        return [
            'cart' => $cart->load('items.product', 'items.variant'),
            'subtotal' => $subtotal,
            'discount_amount' => $cart->discount_amount,
            'total' => $cart->total,
            'coupon_code' => $cart->coupon_code,
            'coupon_type' => $cart->coupon_type,
            'coupon_value' => $cart->coupon_value,
            'message' => 'تم تطبيق الكوبون بنجاح'
        ];
    }

    /**
     * Remove coupon from cart
     */
    public function removeCouponFromCart($userId, $cookieId): array
    {
        // Get cart
        $cart = $this->resolveCart($userId, $cookieId);
        
        // Calculate current subtotal
        $cartWithItems = $this->cartRepository->getCartWithItems($cart->id);
        $subtotal = $cartWithItems->items->sum(function ($item) {
            return $item->price_at_time * $item->quantity;
        });

        // Remove coupon data
        $cart->coupon_code = null;
        $cart->coupon_type = null;
        $cart->coupon_value = null;
        $cart->discount_amount = 0;
        $cart->total = $subtotal;
        $cart->save();

        return [
            'cart' => $cart->load('items.product', 'items.variant'),
            'subtotal' => $subtotal,
            'discount_amount' => 0,
            'total' => $subtotal,
            'coupon_code' => null,
            'message' => 'تم إزالة الكوبون بنجاح'
        ];
    }

    /**
     * Get all coupons (admin)
     */
    public function getAllCoupons()
    {
        return $this->couponRepository->all();
    }

    /**
     * Get coupon by id (admin)
     */
    public function getCouponById(int $id)
    {
        $coupon = $this->couponRepository->find($id);
        
        if (!$coupon) {
            throw ValidationException::withMessages([
                'id' => ['الكوبون غير موجود']
            ]);
        }
        
        return $coupon;
    }

    /**
     * Create new coupon (admin)
     */
    public function createCoupon(array $data)
    {
        // Check if code already exists
        $existing = $this->couponRepository->findByCode($data['code']);
        
        if ($existing) {
            throw ValidationException::withMessages([
                'code' => ['كود الكوبون موجود بالفعل']
            ]);
        }
        
        return $this->couponRepository->create($data);
    }

    /**
     * Update coupon (admin)
     */
    public function updateCoupon(int $id, array $data)
    {
        $coupon = $this->couponRepository->find($id);
        
        if (!$coupon) {
            throw ValidationException::withMessages([
                'id' => ['الكوبون غير موجود']
            ]);
        }
        
        // Check if code already exists (excluding current coupon)
        if (isset($data['code'])) {
            $existing = $this->couponRepository->findByCode($data['code']);
            if ($existing && $existing->id !== $id) {
                throw ValidationException::withMessages([
                    'code' => ['كود الكوبون موجود بالفعل']
                ]);
            }
        }
        
        return $this->couponRepository->update($id, $data);
    }

    /**
     * Delete coupon (admin)
     */
    public function deleteCoupon(int $id)
    {
        $coupon = $this->couponRepository->find($id);
        
        if (!$coupon) {
            throw ValidationException::withMessages([
                'id' => ['الكوبون غير موجود']
            ]);
        }
        
        return $this->couponRepository->delete($id);
    }
}