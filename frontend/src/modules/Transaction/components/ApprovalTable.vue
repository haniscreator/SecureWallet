<template>
  <v-card class="rounded-lg elevation-2 w-100" border width="100%">
    <v-card-title class="font-weight-bold py-4 px-4">
      Transfer List
    </v-card-title>
    <v-divider></v-divider>
    <v-data-table-server
      :headers="headers"
      :items="items"
      :loading="loading"
      :items-length="totalItems"
      :items-per-page="itemsPerPage"
      :page="page"
      class="elevation-0 rounded-0 header-bg w-100"
      width="100%"
      density="compact"
      @update:options="onUpdateOptions"
      hover
    >
      <!-- Reference Column -->
      <template v-slot:item.reference="{ item }">
        <span class="font-weight-medium text-small-body">{{ item.reference || '-' }}</span>
      </template>

      <!-- From Column -->
      <template v-slot:item.from_wallet="{ item }">
         <span class="text-small-body">{{ item.from_wallet?.name || 'System Deposit' }}</span>
      </template>

      <!-- To Column -->
      <template v-slot:item.to="{ item }">
        <div class="d-flex align-center text-small-body">
            <span v-if="item.to_wallet?.is_external">
                {{ truncateAddress(item.to_wallet?.address) }}
            </span>
            <span v-else>
                {{ item.to_wallet?.name || 'Internal' }}
            </span>
        </div>
      </template>

      <!-- Amount Column -->
      <!-- Amount Column -->
      <!-- Amount Column -->
      <template v-slot:item.amount="{ item }">
        <span class="warning--text text-small-body">
          {{ getCurrencySymbol(item) }} {{ Number(item.amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
        </span>
      </template>

      <!-- User Column -->
      <template v-slot:item.user="{ item }">
        <div class="d-flex align-center text-small-body">
            {{ item.user?.name || 'Unknown' }}
        </div>
      </template>

      <!-- Date Column -->
      <template v-slot:item.created_at="{ item }">
        <span class="text-small-body">{{ new Date(item.created_at).toLocaleDateString() }}</span>
      </template>

      <!-- Actions Column -->
      <template v-slot:item.actions="{ item }">
        <div class="d-flex align-center justify-end">
          <v-btn
            color="success"
            variant="text"
            size="small"
            prepend-icon="mdi-check"
            class="mr-1 px-1 text-none"
            style="min-width: auto;"
            @click="$emit('approve', item)"
          >
            Approve
          </v-btn>
          <v-btn
            color="error"
            variant="text"
            size="small"
            prepend-icon="mdi-close"
            class="mr-1 px-1 text-none"
            style="min-width: auto;"
            @click="$emit('reject', item)"
          >
            Reject
          </v-btn>
          <v-btn
            color="info"
            variant="text"
            size="small"
            prepend-icon="mdi-eye"
            class="px-1 text-none"
            style="min-width: auto;"
            @click="$emit('view-details', item)"
          >
            View
          </v-btn>
        </div>
      </template>

      <!-- No Data -->
      <template v-slot:no-data>
        <div class="pa-4 text-center text-grey">
          No pending approvals found.
        </div>
      </template>

      <!-- Pagination Footer -->
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
import { computed } from 'vue';
import type { Transaction } from '../api';

const props = defineProps<{
  loading: boolean;
  items: Transaction[];
  totalItems: number;
  page: number;
  itemsPerPage: number;
}>();

const emit = defineEmits(['update:options', 'update:page', 'view-details', 'approve', 'reject']);

const proxyPage = computed({
  get: () => props.page,
  set: (val) => emit('update:page', val),
});

const totalPages = computed(() => Math.ceil(props.totalItems / props.itemsPerPage) || 1);

const headers = [
  { title: 'Reference', key: 'reference', align: 'start' as const, sortable: true, minWidth: '230px' },
  { title: 'By', key: 'user', align: 'start' as const, sortable: false, width: '150px' },
  { title: 'From', key: 'from_wallet', align: 'start' as const, sortable: false, width: '130px' },
  { title: 'To', key: 'to', align: 'start' as const, sortable: false },
  { title: 'Amount', key: 'amount', align: 'end' as const, sortable: true },
  { title: 'Date', key: 'created_at', align: 'end' as const, sortable: true },
  { title: 'Actions', key: 'actions', align: 'end' as const, sortable: false, width: '1%' },
];

function onUpdateOptions(options: any) {
  emit('update:options', options);
}

function truncateAddress(address?: string) {
    if (!address) return '';
    if (address.length <= 13) return address;
    return `${address.substring(0, 6)}...${address.substring(address.length - 4)}`;
}

function getCurrencySymbol(item: Transaction) {
  return item.from_wallet?.currency?.symbol || '$';
}
</script>

<style scoped>
:deep(thead) {
  background-color: #F5F6F9;
}
:deep(th) {
    color: rgba(0, 0, 0, 0.87) !important;
}
.text-small-body {
    font-size: 12.4px !important;
}
</style>
