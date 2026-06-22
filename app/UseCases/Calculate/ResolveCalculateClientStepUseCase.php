<?php

namespace App\UseCases\Calculate;

use App\Models\Client;
use App\Repositories\Contract\ClientRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

readonly class ResolveCalculateClientStepUseCase
{
    public function __construct(
        private ClientRepositoryInterface $clients,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data): Client
    {
        if (($data['client_mode'] ?? null) === 'existing') {
            $client = $this->clients->find((int) $data['client_id']);

            if (! $client instanceof Client) {
                throw (new ModelNotFoundException)->setModel(Client::class, [$data['client_id']]);
            }

            return $client;
        }

        $client = $this->clients->create($this->clientData($data));

        if (! $client instanceof Client) {
            throw new ModelNotFoundException;
        }

        return $client;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function clientData(array $data): array
    {
        return collect($data)
            ->only([
                'name',
                'last_name',
                'phone',
                'email',
                'curp',
                'notes',
            ])
            ->map(fn (mixed $value): mixed => $value === '' ? null : $value)
            ->all();
    }
}
