<?php

namespace App\Contracts\Repositories;
use Illuminate\Database\Eloquent\Collection;

interface RepositoryInterface
{
    public function all(): Collection;
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}