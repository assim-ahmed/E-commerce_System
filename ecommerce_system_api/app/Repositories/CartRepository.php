<?php

namespace App\Repositories;

use App\Contracts\Repositories\CartRepositoryInterface;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class CartRepository implements CartRepositoryInterface
{
    public function findOrCreateCartByUser($userId)
    {
        return Cart::firstOrCreate(
            ['user_id' => $userId],
            ['cookie_id' => null]
        );
    }

    public function findOrCreateCartByCookie(string $cookieId)
    {
        return Cart::firstOrCreate(
            ['cookie_id' => $cookieId],
            ['user_id' => null]
        );
    }

    public function getCartWithItems(int $cartId)
    {
        return Cart::with([
            'items.product',
            'items.variant'
        ])->findOrFail($cartId);
    }

    public function addItemToCart(int $cartId, int $productId, ?int $variantId, int $quantity, float $priceAtTime)
    {
        return CartItem::create([
            'cart_id' => $cartId,
            'product_id' => $productId,
            'product_variant_id' => $variantId,
            'quantity' => $quantity,
            'price_at_time' => $priceAtTime,
        ]);
    }

    public function updateCartItemQuantity(int $cartItemId, int $quantity)
    {
        $item = CartItem::findOrFail($cartItemId);
        $item->quantity = $quantity;
        $item->save();
        return $item;
    }

    public function removeCartItem(int $cartItemId)
    {
        return CartItem::destroy($cartItemId);
    }

    public function clearCart(int $cartId)
    {
        return CartItem::where('cart_id', $cartId)->delete();
    }

    public function findCartItemByProductAndVariant(int $cartId, int $productId, ?int $variantId)
    {
        return CartItem::where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->first();
    }

    public function mergeCarts(string $cookieId, int $userId)
    {
        DB::transaction(function () use ($cookieId, $userId) {
            $guestCart = Cart::where('cookie_id', $cookieId)->first();
            $userCart = Cart::where('user_id', $userId)->first();

            if (!$guestCart) return;

            if (!$userCart) {
                // تحويل سلة الزائر إلى سلة مستخدم
                $guestCart->user_id = $userId;
                $guestCart->cookie_id = null;
                $guestCart->save();
                return;
            }

            // دمج العناصر
            foreach ($guestCart->items as $guestItem) {
                $existingItem = CartItem::where('cart_id', $userCart->id)
                    ->where('product_id', $guestItem->product_id)
                    ->where('product_variant_id', $guestItem->product_variant_id)
                    ->first();

                if ($existingItem) {
                    $existingItem->quantity += $guestItem->quantity;
                    $existingItem->save();
                } else {
                    $guestItem->cart_id = $userCart->id;
                    $guestItem->save();
                }
            }

            $guestCart->delete();
        });
    }

    public function getCurrentPrice(int $productId, ?int $variantId)
    {
        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            if ($variant) {
                $product = $variant->product;
                return $product->base_price + $variant->price_adjustment;
            }
        }

        $product = Product::find($productId);
        return $product?->base_price;
    }
}