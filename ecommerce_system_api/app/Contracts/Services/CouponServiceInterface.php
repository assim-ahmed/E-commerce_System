<?php

namespace App\Contracts\Services;

interface CouponServiceInterface
{
    /**
     * Validate coupon and calculate discount (without applying)
     */
    public function validateCoupon(string $code, float $subtotal): array;
    
    /**
     * Apply coupon to cart (updates cart table with coupon data)
     */
    public function applyCouponToCart($userId, $cookieId, string $code): array;
    
    /**
     * Remove coupon from cart
     */
    public function removeCouponFromCart($userId, $cookieId): array;
    
    /**
     * Get all coupons (for admin)
     */
    public function getAllCoupons();
    
    /**
     * Get coupon by id (for admin)
     */
    public function getCouponById(int $id);
    
    /**
     * Create new coupon (for admin)
     */
    public function createCoupon(array $data);
    
    /**
     * Update coupon (for admin)
     */
    public function updateCoupon(int $id, array $data);
    
    /**
     * Delete coupon (for admin)
     */
    public function deleteCoupon(int $id);
}