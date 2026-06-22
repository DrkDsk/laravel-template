<?php

namespace App\UseCases\Calculate;

use App\Models\Client;
use App\Repositories\Contract\ClientRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

readonly class SearchClientsUseCase
{
    public function __construct(
        private ClientRepositoryInterface $clients,
    ) {}

    /**
     * @return Collection<int, Client>
     */
    public function execute(string $term, int $limit = 10): Collection
    {
        return $this->clients->searchByTerm($term, $limit);
    }
}
