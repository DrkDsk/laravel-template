<?php

namespace App\Http\Requests\Calculate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCalculateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $hasExistingClient = $this->filled('client_id');

        return [
            'client_id' => [
                'nullable',
                'integer',
                Rule::exists('clients', 'id'),
            ],
            'client' => [
                Rule::excludeIf($hasExistingClient),
                Rule::requiredIf(! $hasExistingClient),
                'array',
            ],
            'client.name' => [
                Rule::excludeIf($hasExistingClient),
                Rule::requiredIf(! $hasExistingClient),
                'nullable',
                'string',
                'max:255',
            ],
            'client.last_name' => [Rule::excludeIf($hasExistingClient), 'nullable', 'string', 'max:255'],
            'client.phone' => [Rule::excludeIf($hasExistingClient), 'nullable', 'string', 'max:255'],
            'client.email' => [Rule::excludeIf($hasExistingClient), 'nullable', 'email', 'max:255'],
            'client.curp' => [
                Rule::excludeIf($hasExistingClient),
                Rule::requiredIf(! $hasExistingClient),
                'nullable',
                'string',
                'max:255',
            ],
            'client.notes' => [Rule::excludeIf($hasExistingClient), 'nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'client_id' => 'cliente',
            'client.name' => 'nombre',
            'client.last_name' => 'apellidos',
            'client.phone' => 'telefono',
            'client.email' => 'correo electronico',
            'client.curp' => 'CURP',
            'client.notes' => 'notas',
        ];
    }
}
