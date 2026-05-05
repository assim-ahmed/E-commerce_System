<?php
// app/Services/AuthService.php

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class AuthService implements AuthServiceInterface
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'customer';
        $data['is_active'] = true;
        
        $user = $this->userRepository->createUser($data);
        
        event(new Registered($user));
        
        return $user;
    }

    public function login(string $email, string $password): ?array
    {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }
        
        if (!$user->hasVerifiedEmail()) {
            return ['error' => 'email_not_verified'];
        }
        
        if (!$user->is_active) {
            return ['error' => 'account_deactivated'];
        }
        
        $this->userRepository->deleteUserTokens($user->id);
        
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ];
    }

    public function logout(User $user): bool
    {
        $user->currentAccessToken()->delete();
        return true;
    }

    public function updateProfile(User $user, array $data): bool
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        return $this->userRepository->updateUser($user->id, $data);
    }

    public function verifyEmail(User $user): bool
    {
        if ($user->hasVerifiedEmail()) {
            return false;
        }
        
        $user->markEmailAsVerified();
        return true;
    }

    public function sendVerificationEmail(User $user): void
    {
        $user->sendEmailVerificationNotification();
    }
}