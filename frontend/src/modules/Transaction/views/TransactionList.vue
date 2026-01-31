<template>
  <v-container fluid class="fill-height align-start pa-6">
    <v-row>
      <v-col cols="12">
        <div class="d-flex justify-space-between align-center mb-6">
          <h1 class="text-h5 font-weight-regular text-grey-darken-1">Transaction - Korporatio</h1>
        </div>

        <!-- Filters -->
        <TransactionFilter @apply-filter="onApplyFilter" />

        <!-- Error Alert -->
        <v-alert
            v-if="store.error"
            type="error"
            variant="tonal"
            class="mb-6 rounded-0"
            closable
        >
            {{ store.error }}
        </v-alert>

        <!-- Transactions List -->
        <TransactionTable
            :loading="store.loading"
            :items="store.transactions"
            :total-items="store.totalItems"
            :page="store.page"
            :items-per-page="store.itemsPerPage"
            @update:options="loadItems"
            @update:page="val => store.page = val"
            @view-details="viewDetails"
        />

      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useTransactionStore } from '../store';
import TransactionFilter from '../components/TransactionFilter.vue';
import TransactionTable from '../components/TransactionTable.vue';

const store = useTransactionStore();
const router = useRouter();

const filters = ref<{
    type: 'credit' | 'debit' | null;
    from_date: string | null;
    to_date: string | null;
    reference: string;
    timezone: string;
}>({
    type: null,
    from_date: null,
    to_date: null,
    reference: '',
    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
});

function onApplyFilter(newFilters: any) {
    filters.value = newFilters;
    store.page = 1; 
    store.fetchTransactions(filters.value);
}

function loadItems({ page, itemsPerPage, sortBy }: { page: number, itemsPerPage: number, sortBy: any[] }) {
    store.page = page;
    store.itemsPerPage = itemsPerPage;
    
    const sortParams = sortBy && sortBy.length ? {
        sort_by: sortBy[0].key,
        sort_dir: sortBy[0].order
    } : {};

    store.fetchTransactions({ ...filters.value, ...sortParams });
}

function viewDetails(item: any) {
    router.push(`/transactions/${item.id}`);
}
</script>
