<?php
// app/Contracts/Repositories/UserRepositoryInterface.php

namespace App\Contracts\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;
    public function createUser(array $data): User;
    public function updateUser(int $id, array $data): bool;
    public function deleteUserTokens(int $userId): bool;
}