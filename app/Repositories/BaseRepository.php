<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function newInstance(): Model
    {
        return $this->model->newInstance();
    }

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function getById(int|string $id, array $relations = []): ?Model
    {
        $query = $this->model->newQuery();
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->find($id);
    }

    public function getByIdOrFail(int|string $id, array $relations = []): Model
    {
        $result = $this->getById($id, $relations);
        
        if (!$result) {
            throw new ModelNotFoundException("Model not found with ID: {$id}");
        }
        
        return $result;
    }

    public function getByIds(array $ids, array $relations = []): Collection
    {
        if (empty($ids)) {
            return Collection::empty();
        }

        $query = $this->model->newQuery();
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->whereIn('id', $ids)->get();
    }

    public function paginate(?int $perPage = null, array $relations = [], array $criteria = [])
    {
        $query = $this->model->newQuery();

        if (!empty($relations)) {
            $query->with($relations);
        }

        if (!empty($criteria)) {
            foreach ($criteria as $criterion) {
                $query->where(
                    $criterion['field'],
                    $criterion['operator'] ?? '=',
                    $criterion['value']
                );
            }
        }

        return $query->paginate($perPage);
    }

    public function create(array|Model $data): Model
    {
        if (is_array($data)) {
            return $this->model->create($data);
        }

        if ($data instanceof Model && get_class($data) === get_class($this->model)) {
            $data->save();
            return $data;
        }

        throw new InvalidArgumentException(
            sprintf("Argument must be an array or an instance of %s", get_class($this->model))
        );
    }

    public function createMany(array $data): Collection
    {
        return DB::transaction(function () use ($data) {
            return collect($data)->map(function ($item) {
                return $this->create($item);
            });
        });
    }

    public function insertMany(array $data): bool
    {
        return $this->model->insert($data);
    }

    public function update(Model|array $data, int|string|null $id = null, string $attribute = 'id'): bool
    {
        if ($data instanceof Model) {
            return $data->save();
        }

        if (!$id) {
            throw new InvalidArgumentException("ID is required when updating with array data");
        }

        return $this->model->where($attribute, $id)->update($data);
    }

    public function updateOrCreate(array $attributes, array $values = []): Model
    {
        return $this->model->updateOrCreate($attributes, $values);
    }

    public function delete(array|int|string $ids): int
    {
        return $this->model->destroy($ids);
    }

    public function deleteWhere(array $criteria): int
    {
        $query = $this->model->newQuery();

        foreach ($criteria as $criterion) {
            $query->where(
                $criterion['field'],
                $criterion['operator'] ?? '=',
                $criterion['value']
            );
        }

        return $query->delete();
    }

    public function count(array $criteria = []): int
    {
        $query = $this->model->newQuery();

        foreach ($criteria as $criterion) {
            $query->where(
                $criterion['field'],
                $criterion['operator'] ?? '=',
                $criterion['value']
            );
        }

        return $query->count();
    }

    protected function getNewQuery(): Builder
    {
        return $this->model->newQuery();
    }
}