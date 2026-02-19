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
            @cancel-transaction="onCancelClick"
        />

        <!-- Confirm Cancel Dialog -->
        <ConfirmDialog
            v-model="showConfirm"
            title="Cancel Transfer"
            message="Are you sure you want to cancel this pending transfer? The funds will be released back to your available balance."
            confirm-text="YES, CANCEL"
            confirm-color="error"
            :loading="store.loading"
            @confirm="confirmCancel"
        />

      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
import { useTransactionList } from '@/modules/Transaction/composables/useTransactionList';
import TransactionFilter from '../components/TransactionFilter.vue';
import TransactionTable from '../components/TransactionTable.vue';
import ConfirmDialog from '@/shared/components/ConfirmDialog.vue';

const {
    store,
    showConfirm,
    onApplyFilter,
    loadItems,
    viewDetails,
    onCancelClick,
    confirmCancel
} = useTransactionList();
</script>
