<template>
    <v-card class="rounded-0 mb-6" elevation="0" border>
        <v-card-text class="pa-4">
            <v-row align="start">
                <!-- Name Filter -->
                <v-col cols="12" md="4">
                    <div class="text-subtitle-2 font-weight-bold mb-2">Name</div>
                    <v-text-field
                        v-model="filters.name"
                        placeholder="Search Name..."
                        variant="outlined"
                        density="compact"
                        hide-details
                        append-inner-icon="mdi-magnify"
                        @keyup.enter="applyFilters"
                    ></v-text-field>
                </v-col>

                <!-- Currency Filter -->
                <v-col cols="12" md="3">
                    <div class="text-subtitle-2 font-weight-bold mb-2">Currency</div>
                        <v-select
                        v-model="filters.currency_id"
                        :items="currencies"
                        item-title="code"
                        item-value="id"
                        placeholder="All"
                        variant="outlined"
                        density="compact"
                        hide-details
                        clearable
                    ></v-select>
                </v-col>

                <!-- Status Filter -->
                <v-col cols="12" md="3">
                    <div class="text-subtitle-2 font-weight-bold mb-2">Status</div>
                        <v-select
                        v-model="filters.status"
                        :items="statusOptions"
                        item-title="title"
                        item-value="value"
                        placeholder="All"
                        variant="outlined"
                        density="compact"
                        hide-details
                        clearable
                    ></v-select>
                </v-col>

                <!-- Buttons -->
                <v-col cols="12" md="2" class="d-flex flex-column justify-end">
                        <div class="text-subtitle-2 font-weight-bold mb-2" style="visibility: hidden">Spacer</div>
                        <div class="d-flex">
                            <v-btn
                            variant="outlined"
                            color="grey-darken-1"
                            class="mr-2 text-capitalize flex-grow-1"
                            @click="clearFilters"
                        >
                            Clear
                        </v-btn>
                        <v-btn
                            color="primary"
                            elevation="0"
                            class="text-capitalize flex-grow-1"
                            @click="applyFilters"
                        >
                            Filter
                        </v-btn>
                        </div>
                </v-col>
            </v-row>
        </v-card-text>
    </v-card>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import type { Currency } from '@/modules/Currency/api';

defineProps<{
    currencies: Currency[];
}>();

const emit = defineEmits(['apply-filter']);

const statusOptions = [
    { title: 'Active', value: true },
    { title: 'Inactive', value: false },
];

const filters = ref({
    name: '',
    currency_id: null as number | null,
    status: null as boolean | null,
});

function applyFilters() {
    emit('apply-filter', filters.value);
}

function clearFilters() {
    filters.value = {
        name: '',
        currency_id: null,
        status: null,
    };
    applyFilters();
}
</script>
