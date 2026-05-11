<?php

namespace App\Contracts\Services;

interface CartServiceInterface
{
    public function getCart($userId, $cookieId);
    public function addItem($userId, $cookieId, array $data);
    public function updateItemQuantity($userId, $cookieId, int $cartItemId, int $quantity);
    public function removeItem($userId, $cookieId, int $cartItemId);
    public function clearCart($userId, $cookieId);
    public function mergeGuestCartWithUserCart(string $cookieId, int $userId);
}