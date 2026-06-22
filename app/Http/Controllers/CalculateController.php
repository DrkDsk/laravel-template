<?php

namespace App\Http\Controllers;

use App\Http\Requests\Calculate\ResolveClientStepRequest;
use App\Models\Client;
use App\UseCases\Calculate\ResolveCalculateClientStepUseCase;
use App\UseCases\Calculate\SearchClientsUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

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
                ->execute((string) ($validated['search'] ?? ''), 10)
                ->map(fn (Client $client): array => $this->serializeClient($client))
                ->values(),
        ]);
    }

    public function resolveClientStep(
        ResolveClientStepRequest $request,
        ResolveCalculateClientStepUseCase $resolveClientStep,
    ): JsonResponse {
        $client = $resolveClientStep->execute($request->validated());

        return response()->json([
            'client' => $this->serializeClient($client),
        ]);
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
