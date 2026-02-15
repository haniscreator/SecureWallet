<template>
  <div class="fill-height pa-6 w-100">
    <div class="d-flex justify-space-between align-center mb-6">
      <h1 class="text-h5 font-weight-regular text-grey-darken-1">Pending Approvals - Korporatio</h1>
    </div>

    <!-- Filters -->
    <ApprovalFilter
      @apply-filter="handleFilter"
      class="mb-6"
    />

    <!-- Transactions Table -->
    <ApprovalTable
      :loading="loading"
      :items="transactions"
      :total-items="totalItems"
      :page="page"
      :items-per-page="itemsPerPage"
      @update:options="handleOptionsUpdate"
      @update:page="page = $event"
      @approve="approve"
      @reject="openRejectDialog"
      @view-details="handleViewDetails"
    />

    <!-- Reject Dialog -->
    <v-dialog v-model="rejectDialog" max-width="500px">
      <v-card>
        <v-card-title class="headline error--text">Reject Transfer</v-card-title>
        <v-card-text>
          <p class="mb-2">Please provide a reason for rejecting this transfer.</p>
          <v-textarea
            v-model="rejectionReason"
            label="Reason"
            variant="outlined"
            rows="3"
            :rules="[v => !!v || 'Reason is required']"
          ></v-textarea>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="grey darken-1" text @click="rejectDialog = false">Cancel</v-btn>
          <v-btn color="error" text @click="confirmReject" :loading="!!processingId" :disabled="!rejectionReason">Reject</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { usePendingApprovals } from '@/modules/Transaction/composables/usePendingApprovals';
import ApprovalFilter from '@/modules/Transaction/components/ApprovalFilter.vue';
import ApprovalTable from '@/modules/Transaction/components/ApprovalTable.vue';

const {
    loading,
    transactions,
    totalItems,
    page,
    itemsPerPage,
    processingId,
    rejectDialog,
    rejectionReason,
    handleFilter,
    handleOptionsUpdate,
    handleViewDetails,
    approve,
    openRejectDialog,
    confirmReject
} = usePendingApprovals();

// Initial fetch is triggered by the table or filter on mount usually, 
// but if we need it explicitly:
// fetchTransactions(); 
// However, looking at the original code, it seemed to rely on the table's @update:options or similar.
// Actually, original code didn't call fetchTransactions on mount explicitly? 
// Ah, the original code defined `fetchTransactions` but seemingly didn't call it on mount?
// Wait, `handleOptionsUpdate` calls it. And Vuetify tables usually trigger update:options on mount.
</script>

<style scoped>
</style>
