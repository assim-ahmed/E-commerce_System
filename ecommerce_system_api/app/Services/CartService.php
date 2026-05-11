<?php

namespace App\Services;

use App\Contracts\Repositories\CartRepositoryInterface;
use App\Contracts\Services\CartServiceInterface;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartService implements CartServiceInterface
{
    protected $cartRepository;

    public function __construct(CartRepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

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

    public function getCart($userId, $cookieId)
    {
        $cart = $this->resolveCart($userId, $cookieId);
        $cartWithItems = $this->cartRepository->getCartWithItems($cart->id);

        // تحديث السعر الحالي + التحقق من تغير السعر
        $priceChanged = false;
        foreach ($cartWithItems->items as $item) {
            $currentPrice = $this->cartRepository->getCurrentPrice(
                $item->product_id,
                $item->product_variant_id
            );

            if ($currentPrice != $item->price_at_time) {
                $priceChanged = true;
                $item->current_price = $currentPrice;
            } else {
                $item->current_price = $item->price_at_time;
            }

            $item->line_total = $item->quantity * $item->current_price;
        }

        $subtotal = $cartWithItems->items->sum('line_total');
        $total = $subtotal;

        return [
            'cart' => $cartWithItems,
            'subtotal' => $subtotal,
            'total' => $total,
            'price_changed' => $priceChanged,
            'items_count' => $cartWithItems->items->count()
        ];
    }

    public function addItem($userId, $cookieId, array $data)
    {
        $cart = $this->resolveCart($userId, $cookieId);

        $product = Product::findOrFail($data['product_id']);

        // التحقق من المخزون
        if ($product->stock_quantity < $data['quantity']) {
            throw ValidationException::withMessages([
                'quantity' => ['Insufficient stock available']
            ]);
        }

        // منع إضافة نفس المنتج مرتين
        $existingItem = $this->cartRepository->findCartItemByProductAndVariant(
            $cart->id,
            $data['product_id'],
            $data['product_variant_id'] ?? null
        );

        if ($existingItem) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'product_id' => ['Product already exists in cart. Update quantity instead.']
            ]);
        }

        $currentPrice = $this->cartRepository->getCurrentPrice(
            $data['product_id'],
            $data['product_variant_id'] ?? null
        );

        return $this->cartRepository->addItemToCart(
            $cart->id,
            $data['product_id'],
            $data['product_variant_id'] ?? null,
            $data['quantity'],
            $currentPrice
        );
    }

    public function updateItemQuantity($userId, $cookieId, int $cartItemId, int $quantity)
    {
        $cart = $this->resolveCart($userId, $cookieId);

        $item = $cart->items()->where('id', $cartItemId)->firstOrFail();

        // التأكد من المخزون
        $currentStock = $item->product->stock_quantity;
        if ($currentStock < $quantity) {
            throw ValidationException::withMessages([
                'quantity' => ['Requested quantity exceeds available stock']
            ]);
        }

        return $this->cartRepository->updateCartItemQuantity($cartItemId, $quantity);
    }

    public function removeItem($userId, $cookieId, int $cartItemId)
    {
        $cart = $this->resolveCart($userId, $cookieId);
        $cart->items()->where('id', $cartItemId)->firstOrFail();
        return $this->cartRepository->removeCartItem($cartItemId);
    }

    public function clearCart($userId, $cookieId)
    {
        $cart = $this->resolveCart($userId, $cookieId);
        return $this->cartRepository->clearCart($cart->id);
    }

    public function mergeGuestCartWithUserCart(string $cookieId, int $userId)
    {
        $this->cartRepository->mergeCarts($cookieId, $userId);
    }
}
