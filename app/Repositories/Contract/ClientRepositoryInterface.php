<?php

namespace App\Repositories\Contract;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

interface ClientRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @return Collection<int, Client>
     */
    public function searchByTerm(string $term, int $limit = 10): Collection;
}
