<?php

namespace App\Http\Controllers;

use App\Http\Requests\Calculate\StoreCalculateRequest;
use App\Models\Client;
use App\UseCases\Calculate\SearchClientsUseCase;
use App\UseCases\Calculate\StoreCalculateUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class CalculateController extends Controller
{
    public function index(SearchClientsUseCase $searchClients): Response
    {
        return Inertia::render('Calculate', [
            'clients' => $searchClients->execute('', 6)->map(fn (Client $client): array => $this->serializeClient($client)),
            'selectedClient' => null,
            'filters' => [
                'search' => '',
            ],
        ]);
    }

    public function searchClients(Request $request, SearchClientsUseCase $searchClients): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        return response()->json([
            'clients' => $searchClients
                ->execute((string) ($validated['search'] ?? ''))
                ->map(fn (Client $client): array => $this->serializeClient($client))
                ->values(),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function store(StoreCalculateRequest $request, StoreCalculateUseCase $storeCalculate): RedirectResponse
    {
        $storeCalculate->execute($request->validated());

        return to_route('calculate');
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeClient(Client $client): array
    {
        return [
            'id' => $client->id,
            'name' => $client->name,
            'last_name' => $client->last_name,
            'phone' => $client->phone,
            'email' => $client->email,
            'curp' => $client->curp,
            'notes' => $client->notes,
        ];
    }
}
