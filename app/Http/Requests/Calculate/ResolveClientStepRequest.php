<?php

namespace App\Http\Requests\Calculate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResolveClientStepRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'client_mode' => $this->input('client_mode', $this->filled('client_id') ? 'existing' : 'new'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $isExistingClient = $this->input('client_mode') === 'existing';
        $isNewClient = $this->input('client_mode') === 'new';

        return [
            'client_mode' => ['required', Rule::in(['existing', 'new'])],
            'client_id' => [
                Rule::requiredIf($isExistingClient),
                'nullable',
                'integer',
                Rule::exists('clients', 'id'),
            ],
            'name' => [
                Rule::requiredIf($isNewClient),
                'nullable',
                'string',
                'max:255',
            ],
            'last_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'rfc' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'client_id' => 'cliente',
            'name' => 'nombre',
            'last_name' => 'apellidos',
            'phone' => 'telefono',
            'email' => 'correo electronico',
            'rfc' => 'RFC',
            'address' => 'direccion',
            'city' => 'ciudad',
            'postal_code' => 'codigo postal',
            'notes' => 'notas',
        ];
    }
}
