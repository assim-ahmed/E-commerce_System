<?php

namespace App\Contracts\Repositories;

interface CartRepositoryInterface
{
    public function findOrCreateCartByUser($userId);
    public function findOrCreateCartByCookie(string $cookieId);
    public function getCartWithItems(int $cartId);
    public function addItemToCart(int $cartId, int $productId, ?int $variantId, int $quantity, float $priceAtTime);
    public function updateCartItemQuantity(int $cartItemId, int $quantity);
    public function removeCartItem(int $cartItemId);
    public function clearCart(int $cartId);
    public function findCartItemByProductAndVariant(int $cartId, int $productId, ?int $variantId);
    public function mergeCarts(string $cookieId, int $userId);
    public function getCurrentPrice(int $productId, ?int $variantId);
}