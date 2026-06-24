<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Search } from '@lucide/vue';
import { computed, reactive, ref, nextTick } from 'vue';
import AppButton from '@/components/AppButton.vue';
import AppCard from '@/components/AppCard.vue';
import AppInput from '@/components/AppInput.vue';
import AppSelect from '@/components/AppSelect.vue';
import AppTextArea from '@/components/AppTextArea.vue';
import type { Client } from '@/models/client';
import calculate from '@/routes/calculate';

const props = defineProps<{
    clients: Client[];
    selectedClient: Client | null;
    filters: {
        search: string;
    };
}>();

type ClientStepField =
    | 'client_id'
    | 'name'
    | 'last_name'
    | 'phone'
    | 'email'
    | 'curp'
    | 'birthdate'
    | 'nss'
    | 'regime_end_date'
    | 'unemployment_assistance_discounted_weeks'
    | 'notes'
    | 'has_spouse'
    | 'minor_or_student_children_count'
    | 'parents_count';

const stepErrors = reactive<Record<ClientStepField, string>>({
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

const form = useForm({
    client_id: props.selectedClient?.id ?? null,
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

const normalizedSearch = computed(() =>
    clientSearch.value.trim().toLowerCase(),
);

const filteredClients = computed(() => {
    if (!normalizedSearch.value) {
        return clientResults.value.slice(0, 6);
    }

    return clientResults.value
        .filter((client) => {
            const fullName = `${client.name ?? ''} ${client.last_name ?? ''}`
                .trim()
                .toLowerCase();
            const phone = `${client.phone ?? ''}`.toLowerCase();
            const email = `${client.email ?? ''}`.toLowerCase();
            const curp = `${client.curp ?? ''}`.toLowerCase();

            return (
                fullName.includes(normalizedSearch.value) ||
                phone.includes(normalizedSearch.value) ||
                email.includes(normalizedSearch.value) ||
                curp.includes(normalizedSearch.value)
            );
        })
        .slice(0, 6);
});

const steps = [
    { id: 1, label: 'Cliente', helper: 'Capturas del Cliente' },
    { id: 2, label: 'Régimen', helper: 'Notas internas' },
    { id: 3, label: 'Costo Modalidad 40', helper: 'Datos del dispositivo' },
    { id: 4, label: 'Confirmar', helper: 'Revision final' },
];

const currentStep = ref(1);
const clientSearch = ref(props.filters?.search ?? '');
const clientResults = ref<Client[]>(props.clients ?? []);
const selectedClient = ref<Client | null>(props.selectedClient);
const showClientDropdown = ref(false);
const manualCustomerMode = ref(false);
const manualNameInputRef = ref<{ focus: () => void } | null>(null);
let searchTimer: ReturnType<typeof window.setTimeout> | null = null;
let searchRequestId = 0;

const progressWidth = computed(
    () => `${((currentStep.value - 1) / (steps.length - 1)) * 100}%`,
);

const showManualCustomerFields = computed(
    () =>
        manualCustomerMode.value ||
        (!!clientSearch.value.trim() && !form.client_id) ||
        !!form.client.name ||
        !!form.client.last_name ||
        !!form.client.phone ||
        !!form.client.email ||
        !!form.client.curp ||
        !!form.client.birthdate ||
        !!form.client.nss ||
        !!form.client.regime_end_date ||
        !!form.client.unemployment_assistance_discounted_weeks ||
        form.family_information.has_spouse !== '' ||
        !!form.family_information.minor_or_student_children_count ||
        !!form.family_information.parents_count ||
        !!form.client.notes,
);

const handleSearchInput = (event: Event) => {
    const value = (event.target as HTMLInputElement).value;

    clientSearch.value = value;
    showClientDropdown.value = true;
    manualCustomerMode.value = false;

    if (form.client_id) {
        form.client_id = null;
        selectedClient.value = null;
    }

    stepErrors.client_id = '';
    scheduleClientSearch();
};

const selectClient = (client: Client) => {
    form.client_id = client.id;
    clearClientFields();
    selectedClient.value = client;
    clientSearch.value =
        `${client.name ?? ''} ${client.last_name ?? ''}`.trim();
    showClientDropdown.value = false;
    manualCustomerMode.value = false;
    clearStepErrors();
};

const activateManualCustomer = async () => {
    form.client_id = null;
    selectedClient.value = null;
    clientSearch.value = '';
    manualCustomerMode.value = true;
    showClientDropdown.value = false;
    await nextTick();
    manualNameInputRef.value?.focus?.();
};

const hideDropdown = () => {
    window.setTimeout(() => {
        showClientDropdown.value = false;
    }, 120);
};

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

const curpPattern =
    /^[A-Z][AEIOUX][A-Z]{2}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[HM](AS|BC|BS|CC|CL|CM|CS|CH|DF|DG|GT|GR|HG|JC|MC|MN|MS|NT|NL|OC|PL|QT|QR|SP|SL|SR|TC|TS|TL|VZ|YN|ZS|NE)[B-DF-HJ-NP-TV-Z]{3}[A-Z0-9]\d$/i;
const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

const normalizePhone = (value: string) => value.replace(/\D+/g, '');
const normalizeDigits = (value: string) => value.replace(/\D+/g, '');

const isValidDateValue = (value: string) => {
    if (!value) {
        return false;
    }

    const date = new Date(`${value}T00:00:00`);

    return !Number.isNaN(date.getTime());
};

const eighteenYearsAgo = () => {
    const date = new Date();
    date.setHours(0, 0, 0, 0);
    date.setFullYear(date.getFullYear() - 18);

    return date;
};

const isAtLeast18YearsOld = (value: string) => {
    if (!isValidDateValue(value)) {
        return false;
    }

    const birthdate = new Date(`${value}T00:00:00`);

    return birthdate <= eighteenYearsAgo();
};

const isAfterDate = (value: string, comparisonValue: string) => {
    if (!isValidDateValue(value) || !isValidDateValue(comparisonValue)) {
        return false;
    }

    const date = new Date(`${value}T00:00:00`);
    const comparisonDate = new Date(`${comparisonValue}T00:00:00`);

    return date > comparisonDate;
};

const eighteenthBirthdayFor = (value: string) => {
    if (!isValidDateValue(value)) {
        return null;
    }

    const date = new Date(`${value}T00:00:00`);
    date.setFullYear(date.getFullYear() + 18);

    return date;
};

const isAfterEighteenthBirthday = (value: string, birthdateValue: string) => {
    if (!isValidDateValue(value)) {
        return false;
    }

    const eighteenthBirthday = eighteenthBirthdayFor(birthdateValue);

    if (!eighteenthBirthday) {
        return false;
    }

    const date = new Date(`${value}T00:00:00`);

    return date > eighteenthBirthday;
};

const isNonNegativeInteger = (value: string) =>
    /^\d+$/.test(value) && Number(value) >= 0;

const validateClientField = (
    field: Exclude<
        ClientStepField,
        | 'client_id'
        | 'last_name'
        | 'notes'
        | 'has_spouse'
        | 'minor_or_student_children_count'
        | 'parents_count'
    >,
    options: { requireRequiredFields?: boolean } = {},
) => {
    if (field === 'name') {
        stepErrors.name = form.client.name.trim()
            ? ''
            : options.requireRequiredFields
              ? 'El nombre es obligatorio.'
              : '';

        return !stepErrors.name;
    }

    if (field === 'phone') {
        const normalizedPhone = normalizePhone(form.client.phone);

        stepErrors.phone =
            normalizedPhone && normalizedPhone.length !== 10
                ? 'El telefono debe contener exactamente 10 digitos.'
                : '';

        return !stepErrors.phone;
    }

    if (field === 'email') {
        const email = form.client.email.trim();

        stepErrors.email =
            email && !emailPattern.test(email)
                ? 'El correo electronico debe tener un formato valido.'
                : '';

        return !stepErrors.email;
    }

    if (field === 'birthdate') {
        stepErrors.birthdate = !form.client.birthdate
            ? options.requireRequiredFields
                ? 'La fecha de nacimiento es obligatoria.'
                : ''
            : !isValidDateValue(form.client.birthdate)
              ? 'La fecha de nacimiento no es valida.'
              : !isAtLeast18YearsOld(form.client.birthdate)
                ? 'El cliente debe tener al menos 18 anos cumplidos.'
                : '';

        if (form.client.regime_end_date) {
            validateClientField('regime_end_date');
        }

        return !stepErrors.birthdate;
    }

    if (field === 'nss') {
        form.client.nss = normalizeDigits(form.client.nss);

        stepErrors.nss = !form.client.nss
            ? options.requireRequiredFields
                ? 'El NSS es obligatorio.'
                : ''
            : form.client.nss.length === 11
              ? ''
              : 'El NSS debe contener exactamente 11 digitos.';

        return !stepErrors.nss;
    }

    if (field === 'regime_end_date') {
        stepErrors.regime_end_date =
            form.client.regime_end_date &&
            !isValidDateValue(form.client.regime_end_date)
                ? 'La fecha de baja de regimen no es valida.'
                : form.client.regime_end_date &&
                    form.client.birthdate &&
                    isValidDateValue(form.client.birthdate) &&
                    !isAfterDate(
                        form.client.regime_end_date,
                        form.client.birthdate,
                    )
                  ? 'La fecha de baja de regimen debe ser posterior a la fecha de nacimiento.'
                  : form.client.regime_end_date &&
                      form.client.birthdate &&
                      isValidDateValue(form.client.birthdate) &&
                      !isAfterEighteenthBirthday(
                          form.client.regime_end_date,
                          form.client.birthdate,
                      )
                    ? 'La fecha de baja de regimen debe ser posterior a la fecha en que el cliente cumplio 18 anos.'
                    : '';

        return !stepErrors.regime_end_date;
    }

    if (field === 'unemployment_assistance_discounted_weeks') {
        stepErrors.unemployment_assistance_discounted_weeks = !form.client
            .unemployment_assistance_discounted_weeks
            ? options.requireRequiredFields
                ? 'Las semanas descontadas son obligatorias.'
                : ''
            : isNonNegativeInteger(
                    form.client.unemployment_assistance_discounted_weeks,
                )
              ? ''
              : 'Las semanas descontadas deben ser un entero mayor o igual a 0.';

        return !stepErrors.unemployment_assistance_discounted_weeks;
    }

    form.client.curp = form.client.curp.toUpperCase();

    stepErrors.curp = !form.client.curp.trim()
        ? options.requireRequiredFields
            ? 'La CURP es obligatoria.'
            : ''
        : curpPattern.test(form.client.curp)
          ? ''
          : 'La CURP debe tener un formato mexicano valido.';

    return !stepErrors.curp;
};

const validateClientFormatFields = () => {
    const phoneIsValid = validateClientField('phone');
    const emailIsValid = validateClientField('email');
    const curpIsValid = validateClientField('curp', {
        requireRequiredFields: true,
    });
    const birthdateIsValid = validateClientField('birthdate', {
        requireRequiredFields: true,
    });
    const nssIsValid = validateClientField('nss', {
        requireRequiredFields: true,
    });
    const regimeEndDateIsValid = validateClientField('regime_end_date');
    const unemploymentWeeksAreValid = validateClientField(
        'unemployment_assistance_discounted_weeks',
        {
            requireRequiredFields: true,
        },
    );

    return (
        phoneIsValid &&
        emailIsValid &&
        curpIsValid &&
        birthdateIsValid &&
        nssIsValid &&
        regimeEndDateIsValid &&
        unemploymentWeeksAreValid
    );
};

const validateFamilyInformationField = (
    field: 'has_spouse' | 'minor_or_student_children_count' | 'parents_count',
    options: { requireRequiredFields?: boolean } = {},
) => {
    if (field === 'has_spouse') {
        stepErrors.has_spouse =
            form.family_information.has_spouse === '' &&
            options.requireRequiredFields
                ? 'Selecciona si tiene esposo/a.'
                : '';

        return !stepErrors.has_spouse;
    }

    const value = form.family_information[field];
    const fieldLabel =
        field === 'minor_or_student_children_count'
            ? 'hijos menores o estudiando'
            : 'padres';

    stepErrors[field] = !value
        ? options.requireRequiredFields
            ? `El numero de ${fieldLabel} es obligatorio.`
            : ''
        : isNonNegativeInteger(value)
          ? ''
          : `El numero de ${fieldLabel} debe ser un entero mayor o igual a 0.`;

    return !stepErrors[field];
};

const validateFamilyInformation = () => {
    const hasSpouseIsValid = validateFamilyInformationField('has_spouse', {
        requireRequiredFields: true,
    });
    const childrenCountIsValid = validateFamilyInformationField(
        'minor_or_student_children_count',
        {
            requireRequiredFields: true,
        },
    );
    const parentsCountIsValid = validateFamilyInformationField(
        'parents_count',
        {
            requireRequiredFields: true,
        },
    );

    return hasSpouseIsValid && childrenCountIsValid && parentsCountIsValid;
};

const handleManualInput = (
    field:
        | Exclude<
              ClientStepField,
              | 'client_id'
              | 'notes'
              | 'has_spouse'
              | 'minor_or_student_children_count'
              | 'parents_count'
          >
        | 'client_notes',
    value: string | number | undefined,
) => {
    form.client_id = null;
    selectedClient.value = null;
    const clientField = field === 'client_notes' ? 'notes' : field;
    const normalizedValue =
        clientField === 'curp' ? String(value ?? '').toUpperCase() : value;

    form.client[clientField] = String(normalizedValue ?? '');
    manualCustomerMode.value = true;
    stepErrors.client_id = '';

    if (
        clientField === 'name' ||
        clientField === 'phone' ||
        clientField === 'email' ||
        clientField === 'curp' ||
        clientField === 'birthdate' ||
        clientField === 'nss' ||
        clientField === 'regime_end_date' ||
        clientField === 'unemployment_assistance_discounted_weeks'
    ) {
        validateClientField(clientField);

        return ;
    }

    clearStepError(clientField);
};

const handleFamilyInformationInput = (
    field: 'has_spouse' | 'minor_or_student_children_count' | 'parents_count',
    value: string | number | undefined,
) => {
    form.client_id = null;
    selectedClient.value = null;
    form.family_information[field] = String(value ?? '');
    manualCustomerMode.value = true;
    stepErrors.client_id = '';
    validateFamilyInformationField(field);
};

const scheduleClientSearch = () => {
    if (searchTimer) {
        window.clearTimeout(searchTimer);
    }

    searchTimer = window.setTimeout(() => {
        void searchClients();
    }, 250);
};

const searchClients = async () => {
    const requestId = ++searchRequestId;
    const params = new URLSearchParams({
        search: clientSearch.value.trim(),
    });

    const response = await fetch(
        calculate.clients.search.url({
            query: Object.fromEntries(params),
        }),
        {
            headers: {
                Accept: 'application/json',
            },
        },
    );

    if (!response.ok || requestId !== searchRequestId) {
        return;
    }

    const data = (await response.json()) as { clients: Client[] };
    clientResults.value = data.clients;
};

const applyServerErrors = (errors: Record<string, string[]>) => {
    clearStepErrors();

    Object.entries(errors).forEach(([field, messages]) => {
        const target = field as ClientStepField;

        if (target in stepErrors) {
            stepErrors[target] = messages[0] ?? '';
        }
    });

    if (Object.keys(errors).some((field) => field !== 'client_id')) {
        manualCustomerMode.value = true;
    }
};

const validateClientStep = () => {
    clearStepErrors();

    if (form.client_id) {
        return true;
    }

    const nameIsValid = validateClientField('name', {
        requireRequiredFields: true,
    });
    const formatFieldsAreValid = validateClientFormatFields();
    const familyInformationIsValid = validateFamilyInformation();

    if (!nameIsValid || !formatFieldsAreValid || !familyInformationIsValid) {
        manualCustomerMode.value = true;

        return false;
    }

    return true;
};

const submitCalculate = () => {
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

            applyServerErrors(normalizedErrors);
            currentStep.value = 1;
        },
    });
};

const goToNextStep = () => {
    if (currentStep.value === 1) {
        if (validateClientStep()) {
            currentStep.value = 2;
        }

        return;
    }

    if (currentStep.value === steps.length) {
        if (!validateClientStep()) {
            currentStep.value = 1;

            return;
        }

        submitCalculate();

        return;
    }

    currentStep.value = Math.min(currentStep.value + 1, steps.length);
};
</script>

<template>
    <Head title="Cálculo de Pensión" />

    <div class="mx-auto w-full p-4">
        <AppCard class="overflow-hidden">
            <div
                class="border-b border-slate-200 px-6 py-5 sm:px-8 dark:border-slate-800"
            >
                <div class="flex flex-col gap-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p
                                class="text-primary-600 dark:text-primary-300 text-xs font-semibold tracking-[0.24em] uppercase"
                            >
                                Formulario de registro
                            </p>
                            <h2
                                class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100"
                            >
                                Captura ordenada para el cálculo de pensión
                            </h2>
                            <p
                                class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                            >
                                Avanza paso a paso y revisa todo antes de
                                finalizar el registro.
                            </p>
                        </div>

                        <div
                            class="hidden min-w-36 rounded-sm border border-slate-200 bg-slate-50 px-4 py-3 text-right sm:block dark:border-slate-800 dark:bg-slate-950/60"
                        >
                            <p
                                class="text-xs tracking-[0.2em] text-slate-400 uppercase dark:text-slate-500"
                            >
                                Progreso
                            </p>
                            <p
                                class="mt-1 text-lg font-semibold text-slate-900 dark:text-slate-100"
                            >
                                {{ currentStep }}/4
                            </p>
                        </div>
                    </div>

                    <div class="relative">
                        <div
                            class="absolute top-5 right-0 left-0 h-px bg-slate-200 dark:bg-slate-800"
                        />
                        <div
                            class="bg-primary-500 absolute top-5 left-0 h-px transition-all duration-300"
                            :style="{ width: progressWidth }"
                        />

                        <div class="relative grid gap-4 sm:grid-cols-4">
                            <button
                                v-for="step in steps"
                                :key="step.id"
                                type="button"
                                class="text-left"
                                @click="
                                    step.id <= currentStep
                                        ? (currentStep = step.id)
                                        : null
                                "
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-sm border text-sm font-semibold transition-all duration-200"
                                        :class="
                                            step.id === currentStep
                                                ? 'bg-primary-500 border-slate-200 text-slate-200 shadow-sm dark:text-slate-200'
                                                : step.id < currentStep
                                                  ? 'bg-primary-500/10 border-slate-400 text-slate-400 dark:border-gray-700 dark:text-slate-400'
                                                  : 'border-slate-200 bg-white text-slate-400 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-500'
                                        "
                                    >
                                        {{ step.id }}
                                    </div>

                                    <div class="min-w-0">
                                        <p
                                            class="text-sm font-medium"
                                            :class="
                                                step.id === currentStep
                                                    ? 'text-slate-600 dark:text-slate-100'
                                                    : step.id < currentStep
                                                      ? 'text-slate-600 dark:text-slate-400'
                                                      : 'text-slate-400 dark:text-slate-400'
                                            "
                                        >
                                            {{ step.label }}
                                        </p>
                                        <p
                                            class="text-xs text-slate-400 dark:text-slate-500"
                                        >
                                            {{ step.helper }}
                                        </p>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-6 sm:px-8">
                <Transition
                    mode="out-in"
                    enter-active-class="transition duration-250 ease-out"
                    enter-from-class="translate-y-2 opacity-0"
                    enter-to-class="translate-y-0 opacity-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="translate-y-0 opacity-100"
                    leave-to-class="-translate-y-1 opacity-0"
                >
                    <section :key="currentStep" class="space-y-6">
                        <template v-if="currentStep === 1">
                            <div class="space-y-2">
                                <h3
                                    class="text-lg font-semibold text-slate-900 dark:text-slate-100"
                                >
                                    Cliente
                                </h3>
                                <p
                                    class="text-sm text-slate-500 dark:text-slate-400"
                                >
                                    Busca un cliente existente o captura los
                                    datos para continuar.
                                </p>
                            </div>

                            <div class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
                                <div class="space-y-4">
                                    <div class="relative">
                                        <label
                                            class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200"
                                        >
                                            Buscar cliente
                                        </label>
                                        <div class="relative">
                                            <span
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"
                                            >
                                                <Search class="size-4" />
                                            </span>
                                            <input
                                                ref="searchInputRef"
                                                :value="clientSearch"
                                                type="text"
                                                placeholder="Nombre, apellido, telefono, correo o CURP"
                                                class="focus:border-primary-500 focus:ring-primary-500/15 block w-full rounded-sm border bg-white py-2 pr-4 pl-10 text-sm text-slate-900 transition duration-200 ease-in-out placeholder:text-slate-400 focus:ring-2 focus:outline-none dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500"
                                                :class="
                                                    stepErrors.client_id
                                                        ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/15'
                                                        : 'border-slate-200'
                                                "
                                                @input="handleSearchInput"
                                                @focus="
                                                    showClientDropdown = true
                                                "
                                                @blur="hideDropdown"
                                            />
                                        </div>
                                        <p
                                            v-if="stepErrors.client_id"
                                            class="mt-1.5 text-xs text-rose-600 dark:text-rose-300"
                                        >
                                            {{ stepErrors.client_id }}
                                        </p>

                                        <div
                                            v-if="showClientDropdown"
                                            class="z-20 mt-2 w-full overflow-hidden rounded-sm border border-slate-200 bg-white shadow-lg dark:border-slate-800 dark:bg-slate-950"
                                        >
                                            <button
                                                v-for="client in filteredClients"
                                                :key="client.id"
                                                type="button"
                                                class="flex w-full items-start justify-between gap-3 border-b border-slate-100 px-4 py-3 text-left transition hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-900"
                                                @mousedown.prevent="
                                                    selectClient(client)
                                                "
                                            >
                                                <div class="min-w-0">
                                                    <p
                                                        class="truncate text-sm font-medium text-slate-900 dark:text-slate-100"
                                                    >
                                                        {{
                                                            `${client.name ?? ''} ${client.last_name ?? ''}`.trim()
                                                        }}
                                                    </p>
                                                    <p
                                                        class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                                                    >
                                                        {{
                                                            client.phone ||
                                                            'Sin telefono registrado'
                                                        }}
                                                    </p>
                                                </div>
                                                <span
                                                    class="text-xs text-slate-400 dark:text-slate-500"
                                                    >Seleccionar</span
                                                >
                                            </button>

                                            <div
                                                v-if="!filteredClients.length"
                                                class="px-4 py-4 text-sm text-slate-500 dark:text-slate-400"
                                            >
                                                No encontramos coincidencias con
                                                esa busqueda.
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        v-if="selectedClient"
                                        class="bg-primary-500/5 dark:bg-primary-500/10 rounded-sm border border-slate-200 px-4 py-4 dark:border-slate-800"
                                    >
                                        <div
                                            class="flex items-start justify-between gap-4"
                                        >
                                            <div>
                                                <p
                                                    class="text-xs font-semibold tracking-[0.2em] text-slate-500 uppercase dark:text-slate-400"
                                                >
                                                    Cliente seleccionado
                                                </p>
                                                <p
                                                    class="mt-2 text-base font-semibold text-slate-600 dark:text-slate-300"
                                                >
                                                    {{
                                                        `${selectedClient.name ?? ''} ${selectedClient.last_name ?? ''}`.trim()
                                                    }}
                                                </p>
                                                <p
                                                    class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                                                >
                                                    {{
                                                        selectedClient.phone ||
                                                        'Sin telefono registrado'
                                                    }}
                                                </p>
                                                <p
                                                    v-if="selectedClient.email"
                                                    class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                                                >
                                                    {{ selectedClient.email }}
                                                </p>
                                            </div>

                                            <button
                                                type="button"
                                                class="text-sm font-medium text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100"
                                                @click="activateManualCustomer"
                                            >
                                                Cambiar
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <AppButton
                                            variant="ghost"
                                            @click="activateManualCustomer"
                                        >
                                            + Nuevo cliente
                                        </AppButton>
                                        <p
                                            class="text-xs text-slate-400 dark:text-slate-500"
                                        >
                                            Captura manual solo si no existe en
                                            la lista.
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="rounded-sm border border-dashed border-slate-200 bg-slate-50/80 p-5 dark:border-slate-800 dark:bg-slate-950/50"
                                >
                                    <p
                                        class="text-xs font-semibold tracking-[0.2em] text-slate-400 uppercase dark:text-slate-500"
                                    >
                                        Contexto rapido
                                    </p>
                                    <div
                                        class="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-300"
                                    >
                                        <p>
                                            Usa la busqueda para evitar
                                            duplicados y conservar historial del
                                            cliente.
                                        </p>
                                        <p>
                                            Si no existe, puedes capturarlo
                                            manualmente y continuar sin
                                            friccion.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div
                                v-if="
                                    showManualCustomerFields && !selectedClient
                                "
                                class="grid gap-4 rounded-sm border border-slate-200 bg-slate-50/60 p-5 md:grid-cols-2 dark:border-slate-800 dark:bg-slate-950/40"
                            >
                                <AppInput
                                    ref="manualNameInputRef"
                                    :model-value="form.client.name"
                                    label="Nombre"
                                    placeholder="Ej. Maria"
                                    :required="!form.client_id"
                                    :error="stepErrors.name"
                                    @update:model-value="
                                        handleManualInput('name', $event)
                                    "
                                    @blur="
                                        validateClientField('name', {
                                            requireRequiredFields: true,
                                        })
                                    "
                                />

                                <AppInput
                                    :model-value="form.client.last_name"
                                    label="Apellidos"
                                    placeholder="Ej. Lopez Hernandez"
                                    :error="stepErrors.last_name"
                                    @update:model-value="
                                        handleManualInput('last_name', $event)
                                    "
                                />

                                <AppInput
                                    :model-value="form.client.phone"
                                    label="Telefono"
                                    placeholder="Ej. 55 1234 5678"
                                    :error="stepErrors.phone"
                                    @update:model-value="
                                        handleManualInput('phone', $event)
                                    "
                                    @blur="validateClientField('phone')"
                                />

                                <AppInput
                                    :model-value="form.client.email"
                                    label="Correo electronico"
                                    placeholder="cliente@correo.com"
                                    type="email"
                                    :error="stepErrors.email"
                                    @update:model-value="
                                        handleManualInput('email', $event)
                                    "
                                    @blur="validateClientField('email')"
                                />

                                <AppInput
                                    :model-value="form.client.curp"
                                    label="CURP"
                                    placeholder="Ej. LOMM800101HDFPRR09"
                                    :required="!form.client_id"
                                    :error="stepErrors.curp"
                                    @update:model-value="
                                        handleManualInput('curp', $event)
                                    "
                                    @blur="
                                        validateClientField('curp', {
                                            requireRequiredFields: true,
                                        })
                                    "
                                />

                                <AppInput
                                    :model-value="form.client.birthdate"
                                    label="Fecha de nacimiento"
                                    type="date"
                                    :required="!form.client_id"
                                    :error="stepErrors.birthdate"
                                    @update:model-value="
                                        handleManualInput('birthdate', $event)
                                    "
                                    @blur="
                                        validateClientField('birthdate', {
                                            requireRequiredFields: true,
                                        })
                                    "
                                />

                                <AppInput
                                    :model-value="form.client.nss"
                                    label="NSS"
                                    placeholder="11 digitos"
                                    inputmode="numeric"
                                    :required="!form.client_id"
                                    :error="stepErrors.nss"
                                    @update:model-value="
                                        handleManualInput('nss', $event)
                                    "
                                    @blur="
                                        validateClientField('nss', {
                                            requireRequiredFields: true,
                                        })
                                    "
                                />

                                <AppInput
                                    :model-value="form.client.regime_end_date"
                                    label="Fecha de baja de regimen"
                                    type="date"
                                    :error="stepErrors.regime_end_date"
                                    @update:model-value="
                                        handleManualInput(
                                            'regime_end_date',
                                            $event,
                                        )
                                    "
                                    @blur="
                                        validateClientField('regime_end_date')
                                    "
                                />

                                <AppInput
                                    :model-value="
                                        form.client
                                            .unemployment_assistance_discounted_weeks
                                    "
                                    label="Semanas descontadas por ayuda de desempleo"
                                    type="number"
                                    min="0"
                                    step="1"
                                    :required="!form.client_id"
                                    :error="
                                        stepErrors.unemployment_assistance_discounted_weeks
                                    "
                                    @update:model-value="
                                        handleManualInput(
                                            'unemployment_assistance_discounted_weeks',
                                            $event,
                                        )
                                    "
                                    @blur="
                                        validateClientField(
                                            'unemployment_assistance_discounted_weeks',
                                            {
                                                requireRequiredFields: true,
                                            },
                                        )
                                    "
                                />

                                <div
                                    class="border-t border-slate-200 pt-5 md:col-span-2 dark:border-slate-800"
                                >
                                    <div class="mb-4">
                                        <h4
                                            class="text-sm font-semibold text-slate-800 dark:text-slate-100"
                                        >
                                            Información familiar
                                        </h4>
                                    </div>

                                    <div class="grid gap-4 md:grid-cols-3">
                                        <AppSelect
                                            :model-value="
                                                form.family_information
                                                    .has_spouse
                                            "
                                            label="Esposo/a"
                                            :required="!form.client_id"
                                            :error="stepErrors.has_spouse"
                                            @update:model-value="
                                                handleFamilyInformationInput(
                                                    'has_spouse',
                                                    $event,
                                                )
                                            "
                                            @blur="
                                                validateFamilyInformationField(
                                                    'has_spouse',
                                                    {
                                                        requireRequiredFields: true,
                                                    },
                                                )
                                            "
                                        >
                                            <option value="">
                                                Seleccionar
                                            </option>
                                            <option value="1">Si</option>
                                            <option value="0">No</option>
                                        </AppSelect>

                                        <AppInput
                                            :model-value="
                                                form.family_information
                                                    .minor_or_student_children_count
                                            "
                                            label="Hijos menores o estudiando"
                                            type="number"
                                            min="0"
                                            step="1"
                                            :required="!form.client_id"
                                            :error="
                                                stepErrors.minor_or_student_children_count
                                            "
                                            @update:model-value="
                                                handleFamilyInformationInput(
                                                    'minor_or_student_children_count',
                                                    $event,
                                                )
                                            "
                                            @blur="
                                                validateFamilyInformationField(
                                                    'minor_or_student_children_count',
                                                    {
                                                        requireRequiredFields: true,
                                                    },
                                                )
                                            "
                                        />

                                        <AppInput
                                            :model-value="
                                                form.family_information
                                                    .parents_count
                                            "
                                            label="Padres"
                                            type="number"
                                            min="0"
                                            step="1"
                                            :required="!form.client_id"
                                            :error="stepErrors.parents_count"
                                            @update:model-value="
                                                handleFamilyInformationInput(
                                                    'parents_count',
                                                    $event,
                                                )
                                            "
                                            @blur="
                                                validateFamilyInformationField(
                                                    'parents_count',
                                                    {
                                                        requireRequiredFields: true,
                                                    },
                                                )
                                            "
                                        />
                                    </div>
                                </div>

                                <AppTextArea
                                    :model-value="form.client.notes"
                                    label="Notas del cliente"
                                    placeholder="Datos adicionales del expediente"
                                    :error="stepErrors.notes"
                                    class="md:col-span-2"
                                    @update:model-value="
                                        handleManualInput(
                                            'client_notes',
                                            $event,
                                        )
                                    "
                                />
                            </div>
                        </template>

                        <template v-else-if="currentStep === 2">
                            <div class="space-y-2">
                                <h3
                                    class="text-lg font-semibold text-slate-900 dark:text-slate-100"
                                >
                                    Recepcion
                                </h3>
                                <p
                                    class="text-sm text-slate-500 dark:text-slate-400"
                                >
                                    Agrega solo las notas necesarias para
                                    contextualizar el ingreso.
                                </p>
                            </div>
                        </template>
                    </section>
                </Transition>
            </div>

            <div
                class="flex items-center justify-between gap-3 border-t border-slate-200 px-6 py-5 sm:px-8 dark:border-slate-800"
            >
                <AppButton
                    variant="ghost"
                    :disabled="currentStep === 1"
                    @click="currentStep = Math.max(currentStep - 1, 1)"
                >
                    Anterior
                </AppButton>

                <AppButton
                    :loading="form.processing"
                    :disabled="form.processing"
                    @click="goToNextStep"
                >
                    {{ currentStep === 4 ? 'Finalizar' : 'Continuar' }}
                </AppButton>
            </div>
        </AppCard>
    </div>
</template>

<style scoped></style>
