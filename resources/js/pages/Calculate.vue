<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Search } from '@lucide/vue';
import { computed, reactive, ref, nextTick } from 'vue';
import AppButton from '@/components/AppButton.vue';
import AppCard from '@/components/AppCard.vue';
import AppInput from '@/components/AppInput.vue';
import type { Client } from '@/models/client';

const props = defineProps<{
    clients: Client[];
    selectedClient: Client | null;
    filters: {
        type: object;
        search: string;
    };
}>();

const stepErrors = reactive({
    client_id: '',
    customer_name: '',
    customer_phone: '',
    issue: '',
});

const form = useForm({
    client_id: props.selectedClient?.id ?? null,
    customer_name: '',
    customer_phone: '',
    notes: '',
    device_category_id: null,
    brand: '',
    model: '',
    serial_number: '',
    inventory_number: '',
    password: '',
    issue: '',
    observations: '',
    accessories: '',
    service_id: null,
});

const normalizedSearch = computed(() =>
    clientSearch.value.trim().toLowerCase(),
);

const filteredClients = computed(() => {
    if (!normalizedSearch.value) {
        return props.clients.slice(0, 6);
    }

    return props.clients
        .filter((client) => {
            const fullName = `${client.name ?? ''} ${client.last_name ?? ''}`
                .trim()
                .toLowerCase();
            const phone = `${client.phone ?? ''}`.toLowerCase();

            return (
                fullName.includes(normalizedSearch.value) ||
                phone.includes(normalizedSearch.value)
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
const showClientDropdown = ref(false);
const manualCustomerMode = ref(false);
const manualNameInputRef = ref(null);

const progressWidth = computed(
    () => `${((currentStep.value - 1) / (steps.length - 1)) * 100}%`,
);

const showManualCustomerFields = computed(
    () =>
        manualCustomerMode.value ||
        (!!clientSearch.value.trim() && !form.client_id) ||
        !!form.customer_name ||
        !!form.customer_phone,
);

const handleSearchInput = (value: string) => {
    clientSearch.value = value;
    showClientDropdown.value = true;

    if (form.client_id) {
        form.client_id = null;
    }

    stepErrors.client_id = '';
};

const selectClient = (client: Client) => {
    form.client_id = client.id;
    form.customer_name = '';
    form.customer_phone = '';
    clientSearch.value =
        `${client.name ?? ''} ${client.last_name ?? ''}`.trim();
    showClientDropdown.value = false;
    manualCustomerMode.value = false;
    stepErrors.client_id = '';
    stepErrors.customer_name = '';
    stepErrors.customer_phone = '';
};

const focusField = (target: HTMLInputElement | null) => {
    target?.focus();
};

const activateManualCustomer = async () => {
    form.client_id = null;
    clientSearch.value = '';
    manualCustomerMode.value = true;
    showClientDropdown.value = false;
    await nextTick();
    console.log(manualNameInputRef.value);
    focusField(manualNameInputRef.value);
};

const hideDropdown = () => {
    window.setTimeout(() => {
        showClientDropdown.value = false;
    }, 120);
};

const clearStepError = (field) => {
    stepErrors[field] = '';
};

const handleManualInput = (field, value) => {
    form.client_id = null;
    form[field] = value;
    manualCustomerMode.value = true;
    clearStepError(field);
    stepErrors.client_id = '';
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
                                                placeholder="Nombre, apellido o telefono"
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
                                                v-if="!clients.length"
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
                                    :model-value="form.customer_name"
                                    label="Nombre del cliente"
                                    placeholder="Ej. Maria Lopez"
                                    :required="!form.client_id"
                                    :error="stepErrors.customer_name"
                                    @update:model-value="
                                        handleManualInput(
                                            'customer_name',
                                            $event,
                                        )
                                    "
                                />

                                <AppInput
                                    :model-value="form.customer_phone"
                                    label="Telefono de contacto"
                                    placeholder="Ej. 55 1234 5678"
                                    :required="!form.client_id"
                                    :error="stepErrors.customer_phone"
                                    @update:model-value="
                                        handleManualInput(
                                            'customer_phone',
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
        </AppCard>
    </div>
</template>

<style scoped></style>
