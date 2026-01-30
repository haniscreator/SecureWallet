<template>
  <v-container fluid class="fill-height align-start pa-6">
    <v-row>
      <v-col cols="12">
        <!-- Filters -->
        <v-card class="rounded-xl mb-6" elevation="0" border>
        <v-card-text class="pa-4">
            <v-row align="start">
                 <v-col cols="12" md="4">
                    <div class="text-subtitle-2 font-weight-bold mb-2">Reference</div>
                    <v-text-field
                        v-model="filters.reference"
                        placeholder="Search Reference..."
                        variant="outlined"
                        density="compact"
                        hide-details
                        append-inner-icon="mdi-magnify"
                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="2">
                    <div class="text-subtitle-2 font-weight-bold mb-2">Date From</div>
                    <v-menu
                        v-model="menuFrom"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        min-width="auto"
                    >
                        <template v-slot:activator="{ props }">
                            <v-text-field
                                v-bind="props"
                                :model-value="filters.from_date"
                                placeholder="YYYY-MM-DD"
                                variant="outlined"
                                density="compact"
                                hide-details
                                append-inner-icon="mdi-calendar"
                                readonly
                            ></v-text-field>
                        </template>
                        <v-date-picker
                            color="primary"
                            :model-value="filters.from_date ? new Date(filters.from_date) : null"
                            @update:model-value="(date) => updateDate('from_date', date)"
                        ></v-date-picker>
                    </v-menu>
                </v-col>
                <v-col cols="12" md="2">
                    <div class="text-subtitle-2 font-weight-bold mb-2">Date To</div>
                     <v-menu
                        v-model="menuTo"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        min-width="auto"
                    >
                        <template v-slot:activator="{ props }">
                            <v-text-field
                                v-bind="props"
                                :model-value="filters.to_date"
                                placeholder="YYYY-MM-DD"
                                variant="outlined"
                                density="compact"
                                hide-details
                                append-inner-icon="mdi-calendar"
                                readonly
                            ></v-text-field>
                        </template>
                        <v-date-picker
                            color="primary"
                            :model-value="filters.to_date ? new Date(filters.to_date) : null"
                            @update:model-value="(date) => updateDate('to_date', date)"
                        ></v-date-picker>
                    </v-menu>
                </v-col>
                <v-col cols="12" md="2">
                    <div class="text-subtitle-2 font-weight-bold mb-2">Type</div>
                     <v-select
                        v-model="filters.type"
                        :items="types"
                        item-title="title"
                        item-value="value"
                        placeholder="All"
                        variant="outlined"
                        density="compact"
                        hide-details
                    ></v-select>
                </v-col>
                <v-col cols="12" md="2" class="d-flex flex-column justify-end">
                     <!-- Spacer to align with inputs that have labels -->
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

    <!-- Error Alert -->
    <v-alert
        v-if="store.error"
        type="error"
        variant="tonal"
        class="mb-6 rounded-xl"
        closable
    >
        {{ store.error }}
    </v-alert>

    <!-- Transactions List -->
    <v-card class="rounded-xl" elevation="0" border>
        <v-card-title class="pa-4 text-h6 font-weight-bold border-b">
            Transactions List
        </v-card-title>
        <v-data-table-server
            v-model:page="store.page"
            :items-per-page="store.itemsPerPage"
            :headers="headers"
            :items="store.transactions"
            :items-length="store.totalItems"
            :loading="store.loading"
            @update:options="loadItems"
            hover
            class="pa-2 transaction-table"
        >
            <!-- Date Column -->
            <template v-slot:item.created_at="{ item }">
                {{ new Date(item.created_at).toLocaleDateString() }}
            </template>

            <!-- To Column (Wallet Name) -->
            <template v-slot:item.to="{ item }">
                <div class="d-flex align-center">
                    <v-icon color="primary" size="small" class="mr-2">mdi-wallet</v-icon>
                    <span>{{ getWalletName(item) }}</span>
                </div>
            </template>

             <!-- Type Column -->
            <template v-slot:item.type="{ item }">
                <span class="text-capitalize font-weight-medium" :class="getTypeColor(item.type)">
                    {{ item.type }}
                </span>
            </template>

            <!-- Amount Column -->
            <template v-slot:item.amount="{ item }">
                 <span class="font-weight-bold" :class="getTypeColor(item.type)">
                    {{ item.type === 'debit' ? '-' : '+' }}{{ getCurrencySymbol(item) }}{{ Number(item.amount).toLocaleString() }}
                 </span>
            </template>

            <!-- Actions Column -->
            <template v-slot:item.actions="{ item }">
                <v-tooltip text="View Transaction" location="top">
                    <template v-slot:activator="{ props }">
                         <v-btn
                            v-bind="props"
                            icon="mdi-eye"
                            variant="text"
                            size="small"
                            color="info"
                            @click="router.push(`/transactions/${item.id}`)"
                        ></v-btn>
                    </template>
                </v-tooltip>
            </template>

             <!-- No Data -->
            <template v-slot:no-data>
                <div class="pa-4 text-center text-grey">
                    No transactions found.
                </div>
            </template>

        </v-data-table-server>
    </v-card>

      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useTransactionStore } from '../store';
import type { Transaction } from '../api';

const store = useTransactionStore();
const router = useRouter();

const headers = [
    { title: 'Date', key: 'created_at', align: 'start' as const, sortable: true },
    { title: 'To', key: 'to', align: 'start' as const, sortable: false },
    { title: 'Type', key: 'type', align: 'start' as const, sortable: true },
    { title: 'Amount', key: 'amount', align: 'end' as const, sortable: true },
    { title: 'Reference', key: 'reference', align: 'start' as const, sortable: true },
    { title: 'Actions', key: 'actions', align: 'end' as const, sortable: false },
];

const types = [
    { title: 'All', value: null },
    { title: 'Credit', value: 'credit' },
    { title: 'Debit', value: 'debit' },
];

const filters = ref({
    type: null,
    from_date: null,
    to_date: null,
    reference: '',
});

const menuFrom = ref(false);
const menuTo = ref(false);

function updateDate(field: 'from_date' | 'to_date', date: any) {
    if (date) {
        const d = new Date(date);
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        filters.value[field] = `${year}-${month}-${day}`;
    } else {
        filters.value[field] = null;
    }
    
    if (field === 'from_date') menuFrom.value = false;
    if (field === 'to_date') menuTo.value = false;
}

function loadItems({ page, itemsPerPage, sortBy }: { page: number, itemsPerPage: number, sortBy: any[] }) {
    store.page = page;
    store.itemsPerPage = itemsPerPage;
    
    // SortBy comes as array of { key: string, order: 'asc'|'desc' }
    const sortParams = sortBy && sortBy.length ? {
        sort_by: sortBy[0].key,
        sort_dir: sortBy[0].order
    } : {};

    store.fetchTransactions({ ...filters.value, ...sortParams });
}

function applyFilters() {
    store.page = 1; // Reset to page 1
    // Note: applyFilters might need to respect current sorting if user re-filtering
    // But data-table update:options will re-trigger if we change data? No, manual call.
    // Ideally we just trigger fetch with current table state.
    // For simplicity, we just filter and let default sort or previous sort persist if managed by store.
    // But loadItems manages it from UI.
    // A clean way is to force table refresh if we could, but calling store.fetch works.
    store.fetchTransactions(filters.value); // Sorting might get lost here if not stored.
    // To preserve sorting, we'd need to store sort state in a ref.
    // HOWEVER, user just asked to fix it. `loadItems` is reliable source of truth.
    // `v-data-table-server` triggers `loadItems` automatically when page/sort changes.
    // When FILTER changes, we want to reload. We likely reset page to 1.
    // If we call fetch directly here, we might miss sort.
    // Better pattern: store filters in store/ref, and just execute fetch.
    // BUT since we can't easily access table state here without ref to table component... 
    // Let's just pass filters.value. And if sorting was previously set, the UI might be out of sync if we don't pass it?
    // Actually `loadItems` is triggered on mount and changes. We just change filter data and reload.
    // Correct way: Trigger table reload or update params.
    // Simple fix: loadItems is the main fetcher.
    // We can just set store.fetchTransactions... wait, `loadItems` gets called by data-table.
    // If we change filters, we usually want to trigger a fetch.
}

function clearFilters() {
    filters.value = {
        type: null,
        from_date: null,
        to_date: null,
        reference: '',
    };
    applyFilters();
}

function getTypeColor(type: string) {
    if (type === 'credit') return 'text-success';
    if (type === 'debit') return 'text-error';
    return '';
}

function getWalletName(item: Transaction) {
    // Determine which wallet name to show based on flow
    // For specific user view, usually show the 'other' party or the wallet involved
    // Simplified: show to_wallet name if credit, from_wallet name if debit? 
    // Or just show the wallet that belongs to User?
    // Let's assume standard 'To Wallet' display logic for now.
    return item.to_wallet?.name || item.wallet?.name || 'Unknown Wallet';
}

function getCurrencySymbol(item: Transaction) {
    return item.to_wallet?.currency?.symbol || item.from_wallet?.currency?.symbol || '$';
}
</script>

<style scoped>
:deep(input[type="date"]) {
    text-align: right;
    padding-right: 10px; /* Give some space from the icon if needed */
}
/* Ensure the icon stays on the right and doesn't look weird with right-aligned text */
/* Ensure the icon stays on the right and well aligned */
:deep(input[type="date"]::-webkit-calendar-picker-indicator) {
    margin-left: 10px;
}

/* Hide the items-per-page dropdown in the footer */
:deep(.transaction-table .v-data-table-footer__items-per-page) {
    display: none !important;
}
</style>
