<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contract\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection<int, Model>
     */
    public function getAll(): Collection
    {
        /** @var Collection<int, Model> $models */
        $models = $this->model::query()->get();

        return $models;
    }

    /**
     * @return Collection<int, Model>
     */
    public function limit(int $limit = 100): Collection
    {
        /** @var Collection<int, Model> $models */
        $models = $this->model::query()->limit($limit)->get();

        return $models;
    }

    public function find(int $id): ?Model
    {
        return $this->model::query()->where('id', $id)->first();
    }

    public function getByField(string $field, string $value): ?Model
    {
        return $this->model::query()
            ->where($field, $value)
            ->first();
    }

    public function create(array $data): ?Model
    {
        /** @var Model $model */
        $model = $this->model::query()->create($data);

        return $model;
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, Model> $paginator */
        $paginator = $this->model::query()->paginate($perPage);

        return $paginator;
    }
}
