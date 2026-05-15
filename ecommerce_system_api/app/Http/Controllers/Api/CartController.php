<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\Services\CartServiceInterface;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Http\Resources\Cart\CartResource;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartServiceInterface $cartService)
    {
        $this->cartService = $cartService;
    }

   public function index(Request $request)
{
    $result = $this->cartService->getCart(
        auth('sanctum')->id(),
        $request->cookie('cart_cookie')
    );

    return response()->json([
        'success' => true,
        'data' => [
            'cart_id' => $result['cart_id'],
            'items' => $result['items'],
            'subtotal' => $result['subtotal'],
            'discount_amount' => $result['discount_amount'] ?? 0,
            'total' => $result['total'],
            'coupon_code' => $result['coupon_code'] ?? null,
            'coupon_type' => $result['coupon_type'] ?? null,
            'coupon_value' => $result['coupon_value'] ?? null,
            'items_count' => $result['items_count'],
            'price_changed' => $result['price_changed']
        ],
        'message' => 'Cart retrieved successfully',
        'price_changed' => $result['price_changed']
    ]);
}

    public function addItem(AddToCartRequest $request)
    {
        try {
            $userId = auth('sanctum')->id();
            $cookieId = $request->cookie('cart_cookie');

            if (!$userId && !$cookieId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please call GET /api/cart first'
                ], 400);
            }

            $item = $this->cartService->addItem($userId, $cookieId, $request->validated());

            return response()->json([
                'success' => true,
                'data' => $item,
                'message' => 'Item added to cart successfully'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
                'errors' => []
            ], 500);
        }
    }

    public function updateItem(UpdateCartItemRequest $request, $id)
    {
        try {
            $userId = auth('sanctum')->id();
            $cookieId = $request->cookie('cart_cookie');

            $item = $this->cartService->updateItemQuantity($userId, $cookieId, $id, $request->quantity);

            return response()->json([
                'success' => true,
                'data' => $item,
                'message' => 'Cart item updated successfully'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
                'errors' => []
            ], 500);
        }
    }

    public function removeItem(Request $request, $cartItemId)
    {
        $userId = auth('sanctum')->id();
        $cookieId = $request->cookie('cart_cookie');

        $this->cartService->removeItem($userId, $cookieId, $cartItemId);

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Item removed from cart successfully'
        ]);
    }

    public function clear(Request $request)
    {
        $userId = auth('sanctum')->id();
        $cookieId = $request->cookie('cart_cookie');

        $this->cartService->clearCart($userId, $cookieId);

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Cart cleared successfully'
        ]);
    }
}
