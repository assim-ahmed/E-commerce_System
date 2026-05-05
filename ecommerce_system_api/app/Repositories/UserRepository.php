<?php
// app/Repositories/UserRepository.php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->findBy('email', $email);
    }

    public function createUser(array $data): User
    {
        return $this->create($data);
    }

    public function updateUser(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    public function deleteUserTokens(int $userId): bool
    {
        $user = $this->find($userId);
        if (!$user) return false;
        
        $user->tokens()->delete();
        return true;
    }
}