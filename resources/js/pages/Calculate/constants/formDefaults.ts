import type { Client } from '@/models/client';
import type { CalculateFormData, RegimePeriod } from '../types/calculate';
import { BASE_REGIME_TYPES } from './regimeTypes';

export const createBaseRegimePeriods = (): RegimePeriod[] =>
    BASE_REGIME_TYPES.map((regimeType) => ({
        regime_type: regimeType.value,
        regime_name: regimeType.label,
        contribution_start_date: null,
        contribution_end_date: null,
        time: 0,
        is_fixed: true,
    }));

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
    regime_periods: createBaseRegimePeriods(),
});
