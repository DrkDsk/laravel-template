<?php

namespace App\Repositories\Contract;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    /**
     * @return Collection<int, Model>
     */
    public function getAll(): Collection;

    /**
     * @return Collection<int, Model>
     */
    public function limit(int $limit = 100): Collection;

    public function find(int $id): ?Model;

    public function getByField(string $field, string $value): ?Model;

    /**
     * @param  array<string, mixed>  $data
     * @return Model|null
     */
    public function create(array $data): ?Model;

    /**
     * @return LengthAwarePaginator<int, Model>
     */
    public function paginate(int $perPage = 10): LengthAwarePaginator;
}
