<?php

namespace App\UseCases\Calculate;

use App\Models\Client;
use App\Repositories\Contract\ClientRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class StoreCalculateUseCase
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws Throwable
     */
    public function execute(array $data): Client
    {
        return DB::transaction(function () use ($data): Client {
            if (! empty($data['client_id'])) {
                $client = $this->clientRepository->find((int) $data['client_id']);

                if (! $client instanceof Client) {
                    throw (new ModelNotFoundException)->setModel(Client::class, [$data['client_id']]);
                }

                return $client;
            }

            $client = $this->clientRepository->create($this->clientData($data));

            if (! $client instanceof Client) {
                throw new ModelNotFoundException;
            }

            return $client;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function clientData(array $data): array
    {
        $client = is_array($data['client'] ?? null) ? $data['client'] : [];
        $allowedFields = array_flip([
            'name',
            'last_name',
            'phone',
            'email',
            'curp',
            'notes',
        ]);

        $clientData = array_intersect_key($client, $allowedFields);

        foreach ($clientData as $field => $value) {
            $clientData[$field] = $value === '' ? null : $value;
        }

        return $clientData;
    }
}
