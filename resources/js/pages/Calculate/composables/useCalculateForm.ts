import { useForm } from '@inertiajs/vue3';
import { reactive } from 'vue';
import type { Client } from '@/models/client';
import calculate from '@/routes/calculate';
import { createCalculateFormDefaults } from '../constants/formDefaults';
import type {
    CalculateFormData,
    ClientStepField,
    StepErrors,
} from '../types/calculate';

export const createStepErrors = () =>
    reactive<StepErrors>({
        client_id: '',
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
        has_spouse: '',
        minor_or_student_children_count: '',
        parents_count: '',
    });

export const useCalculateForm = (selectedClient: Client | null) => {
    const form = useForm<CalculateFormData>(
        createCalculateFormDefaults(selectedClient),
    );
    const stepErrors = createStepErrors();

    const clearStepError = (field: ClientStepField) => {
        stepErrors[field] = '';
    };

    const clearStepErrors = () => {
        Object.keys(stepErrors).forEach((field) => {
            stepErrors[field as ClientStepField] = '';
        });
    };

    const clearClientFields = () => {
        form.client.name = '';
        form.client.last_name = '';
        form.client.phone = '';
        form.client.email = '';
        form.client.curp = '';
        form.client.birthdate = '';
        form.client.nss = '';
        form.client.regime_end_date = '';
        form.client.unemployment_assistance_discounted_weeks = '';
        form.client.notes = '';
        form.family_information.has_spouse = '';
        form.family_information.minor_or_student_children_count = '';
        form.family_information.parents_count = '';
    };

    const applyServerErrors = (
        errors: Record<string, string[]>,
        enableManualMode: () => void,
    ) => {
        clearStepErrors();

        Object.entries(errors).forEach(([field, messages]) => {
            const target = field as ClientStepField;

            if (target in stepErrors) {
                stepErrors[target] = messages[0] ?? '';
            }
        });

        if (Object.keys(errors).some((field) => field !== 'client_id')) {
            enableManualMode();
        }
    };

    const submitCalculate = (
        enableManualMode: () => void,
        returnToClientStep: () => void,
    ) => {
        form.post(calculate.store().url, {
            preserveScroll: true,
            onError: (errors) => {
                const normalizedErrors = Object.fromEntries(
                    Object.entries(errors).map(([field, message]) => {
                        const normalizedField = field
                            .replace('client.', '')
                            .replace('family_information.', '');

                        return [normalizedField, [message]];
                    }),
                );

                applyServerErrors(normalizedErrors, enableManualMode);
                returnToClientStep();
            },
        });
    };

    return {
        form,
        stepErrors,
        clearStepError,
        clearStepErrors,
        clearClientFields,
        applyServerErrors,
        submitCalculate,
    };
};
