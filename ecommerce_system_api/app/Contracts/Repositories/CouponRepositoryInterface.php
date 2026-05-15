<?php

namespace App\Contracts\Repositories;

interface CouponRepositoryInterface
{
    public function findByCode(string $code);
    public function incrementUsage(string $code);
    public function find(int $id);
    public function all();
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
}