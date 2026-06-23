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

        if (isset($client['nss'])) {
            $client['nss'] = preg_replace('/\D+/', '', (string) $client['nss']);
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
            'client.birthdate' => [
                Rule::excludeIf($hasExistingClient),
                Rule::requiredIf(! $hasExistingClient),
                'date',
                'before:today',
            ],
            'client.nss' => [
                Rule::excludeIf($hasExistingClient),
                Rule::requiredIf(! $hasExistingClient),
                'digits:11',
            ],
            'client.regime_end_date' => [
                Rule::excludeIf($hasExistingClient),
                'nullable',
                'date',
            ],
            'client.unemployment_assistance_discounted_weeks' => [
                Rule::excludeIf($hasExistingClient),
                Rule::requiredIf(! $hasExistingClient),
                'integer',
                'min:0',
            ],
            'client.notes' => [Rule::excludeIf($hasExistingClient), 'nullable', 'string'],
            'family_information' => [
                Rule::excludeIf($hasExistingClient),
                Rule::requiredIf(! $hasExistingClient),
                'array',
            ],
            'family_information.has_spouse' => [
                Rule::excludeIf($hasExistingClient),
                'required',
                'boolean',
            ],
            'family_information.minor_or_student_children_count' => [
                Rule::excludeIf($hasExistingClient),
                'required',
                'integer',
                'min:0',
            ],
            'family_information.parents_count' => [
                Rule::excludeIf($hasExistingClient),
                'required',
                'integer',
                'min:0',
            ],
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
            'client.birthdate' => 'fecha de nacimiento',
            'client.nss' => 'NSS',
            'client.regime_end_date' => 'fecha de baja de regimen',
            'client.unemployment_assistance_discounted_weeks' => 'semanas descontadas por ayuda de desempleo',
            'client.notes' => 'notas',
            'family_information.has_spouse' => 'esposo/a',
            'family_information.minor_or_student_children_count' => 'hijos menores o estudiando',
            'family_information.parents_count' => 'padres',
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
            'client.nss.digits' => 'El NSS debe contener exactamente 11 digitos.',
        ];
    }
}
