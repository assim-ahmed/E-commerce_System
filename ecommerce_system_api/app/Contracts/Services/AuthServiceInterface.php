<?php
// app/Contracts/Services/AuthServiceInterface.php

namespace App\Contracts\Services;

use App\Models\User;

interface AuthServiceInterface
{
    public function register(array $data): User;
    public function login(string $email, string $password): ?array;
    public function logout(User $user): bool;
    public function updateProfile(User $user, array $data): bool;
    public function verifyEmail(User $user): bool;
    public function sendVerificationEmail(User $user): void;
}