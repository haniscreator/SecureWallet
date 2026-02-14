<template>
  <v-card class="rounded-0" elevation="0" border>
    <v-card-title class="pa-4 text-h6 font-weight-bold border-b">
      Transactions List
    </v-card-title>
    <v-data-table-server
      :model-value="selected"
      v-model:page="proxyPage"
      :items-per-page="itemsPerPage"
      :headers="headers"
      :items="items"
      :items-length="totalItems"
      :loading="loading"
      @update:options="onUpdateOptions"
      hover
      class="transaction-table"
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
        <div class="d-flex justify-end gap-2">
          <v-btn
            variant="text"
            size="small"
            color="info"
            class="font-weight-bold"
            @click="$emit('view-details', item)"
          >
            VIEW <v-icon size="small" class="ml-1">mdi-eye</v-icon>
          </v-btn>
        </div>
      </template>

      <!-- No Data -->
      <template v-slot:no-data>
        <div class="pa-4 text-center text-grey">
          No transactions found.
        </div>
      </template>

      <template v-slot:bottom>
        <v-divider></v-divider>
        <div class="d-flex align-center justify-space-between pa-4">
          <div class="text-caption text-grey">Showing {{ items.length }} of {{ totalItems }} Transactions</div>
          <div class="d-flex align-center">
            <v-pagination
              v-model="proxyPage"
              :length="totalPages"
              total-visible="3"
              density="compact"
              active-color="primary"
              variant="flat"
              class="details-pagination"
            ></v-pagination>
          </div>
        </div>
      </template>
    </v-data-table-server>
  </v-card>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import type { Transaction } from '../api';

const props = defineProps<{
  loading: boolean;
  items: Transaction[];
  totalItems: number;
  page: number;
  itemsPerPage: number;
}>();

const emit = defineEmits(['update:options', 'update:page', 'view-details']);

const selected = ref([]);

const proxyPage = computed({
  get: () => props.page,
  set: (val) => emit('update:page', val),
});

const totalPages = computed(() => Math.ceil(props.totalItems / props.itemsPerPage) || 1);

const headers = [
  { title: 'Date', key: 'created_at', align: 'start' as const, sortable: true },
  { title: 'To', key: 'to', align: 'start' as const, sortable: false },
  { title: 'Type', key: 'type', align: 'start' as const, sortable: true },
  { title: 'Amount', key: 'amount', align: 'end' as const, sortable: true },
  { title: 'Reference', key: 'reference', align: 'start' as const, sortable: true },
  { title: 'Actions', key: 'actions', align: 'end' as const, sortable: false },
];

function onUpdateOptions(options: any) {
  emit('update:options', options);
}

function getTypeColor(type: string) {
  if (type === 'credit') return 'text-success';
  if (type === 'debit') return 'text-error';
  return '';
}

function getWalletName(item: Transaction) {
    const wallet = item.to_wallet;
    if (wallet?.is_external && wallet.address) {
        // Truncate address: 0x123...456 (first 6, last 4)
        if (wallet.address.length > 13) {
             return `${wallet.address.substring(0, 8)}...${wallet.address.substring(wallet.address.length - 6)}`;
        }
        return wallet.address;
    }
  return item.to_wallet?.name || item.wallet?.name || 'Unknown Wallet';
}

function getCurrencySymbol(item: Transaction) {
  return item.to_wallet?.currency?.symbol || item.from_wallet?.currency?.symbol || '$';
}
</script>

<style scoped>
/* Hide the items-per-page dropdown in the footer */
:deep(.transaction-table .v-data-table-footer__items-per-page) {
    display: none !important;
}

:deep(.transaction-table .v-data-table-footer) {
    display: none !important;
}

.details-pagination :deep(.v-pagination__list) {
    margin-bottom: 0;
}

.details-pagination :deep(.v-pagination__item--is-active) {
    background-color: rgb(var(--v-theme-primary)) !important;
    color: white !important;
}

/* Header Background Color */
:deep(.transaction-table thead tr th) {
    background-color: #F5F6F9 !important;
}
</style>
