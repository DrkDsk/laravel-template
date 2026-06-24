import type { Client } from '@/models/client';
import type { CalculateFormData } from '../types/calculate';

export const createCalculateFormDefaults = (
    selectedClient: Client | null,
): CalculateFormData => ({
    client_id: selectedClient?.id ?? null,
    client: {
        name: '',
        last_name: '',
        phone: '',
        email: '',
        curp: '',
        birthdate: '',
        nss: '',
        regime_end_date: '',
        unemployment_assistance_discounted_weeks: '',
        notes: '',
    },
    family_information: {
        has_spouse: '',
        minor_or_student_children_count: '',
        parents_count: '',
    },
});
