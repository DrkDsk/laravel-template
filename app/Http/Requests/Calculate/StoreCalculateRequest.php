<?php

namespace App\Http\Requests\Calculate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCalculateRequest extends FormRequest
{
    private const string CURP_REGEX = '/^[A-Z][AEIOUX][A-Z]{2}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[HM](AS|BC|BS|CC|CL|CM|CS|CH|DF|DG|GT|GR|HG|JC|MC|MN|MS|NT|NL|OC|PL|QT|QR|SP|SL|SR|TC|TS|TL|VZ|YN|ZS|NE)[B-DF-HJ-NP-TV-Z]{3}[A-Z0-9]\d$/i';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $client = $this->input('client');

        if (! is_array($client)) {
            return;
        }

        if (isset($client['phone'])) {
            $client['phone'] = preg_replace('/\D+/', '', (string) $client['phone']);
        }

        if (isset($client['curp'])) {
            $client['curp'] = strtoupper((string) $client['curp']);
        }

        $this->merge([
            'client' => $client,
        ]);
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
            'client.phone' => [
                Rule::excludeIf($hasExistingClient),
                'nullable',
                'string',
                'regex:/^\d{10}$/',
            ],
            'client.email' => [
                Rule::excludeIf($hasExistingClient),
                'nullable',
                'string',
                'max:255',
                'email',
            ],
            'client.curp' => [
                Rule::excludeIf($hasExistingClient),
                Rule::requiredIf(! $hasExistingClient),
                'nullable',
                'string',
                'max:255',
                'regex:'.self::CURP_REGEX,
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

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'client.curp.regex' => 'La CURP debe tener un formato mexicano valido.',
            'client.email.email' => 'El correo electronico debe tener un formato valido y un dominio existente.',
            'client.phone.regex' => 'El telefono debe contener exactamente 10 digitos.',
        ];
    }
}
