<?php
// app/Repositories/BaseRepository.php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $columns = ['*']): Collection
    {
        return $this->model->get($columns);
    }

    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $model = $this->find($id);
        if (!$model) return false;
        return $model->update($data);
    }

    public function delete(int $id): bool
    {
        $model = $this->find($id);
        if (!$model) return false;
        return $model->delete();
    }

    public function findBy(string $column, $value): ?Model
    {
        return $this->model->where($column, $value)->first();
    }

    public function findAllBy(string $column, $value): Collection
    {
        return $this->model->where($column, $value)->get();
    }
}