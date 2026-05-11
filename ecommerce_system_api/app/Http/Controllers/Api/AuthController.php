<?php
// app/Http/Controllers/Api/AuthController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Services\CartServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'message' => 'Please check your email to verify your account'
            ],
            'message' => 'Registration successful. Please verify your email.',
        ], 201);
    }

    public function login(LoginRequest $request, CartServiceInterface $cartService): JsonResponse
    {
        $result = $this->authService->login(
            $request->email,
            $request->password
        );

        if (!$result) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Invalid credentials',
                'errors' => null
            ], 401);
        }

        if (isset($result['error'])) {
            $messages = [
                'email_not_verified' => 'Please verify your email before logging in',
                'account_deactivated' => 'Your account has been deactivated'
            ];

            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $messages[$result['error']],
                'errors' => null
            ], 403);
        }

        $cookieId = $request->cookie('cart_cookie');
        if ($cookieId) {
            $cartService->mergeGuestCartWithUserCart($cookieId, auth('sanctum')->id());
        }

        return response()->json([
            'success' => true,
            'data' => $result,
            'message' => 'Login successful',
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Logged out successfully',
        ], 200);
    }

    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
            'message' => 'Profile retrieved successfully',
        ], 200);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $updated = $this->authService->updateProfile(
            $request->user(),
            $request->validated()
        );

        if (!$updated) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to update profile',
                'errors' => null
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $request->user()->fresh(),
            'message' => 'Profile updated successfully',
        ], 200);
    }

    public function sendVerificationEmail(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Email already verified',
                'errors' => null
            ], 400);
        }

        $this->authService->sendVerificationEmail($request->user());

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Verification email sent',
        ], 200);
    }
}
