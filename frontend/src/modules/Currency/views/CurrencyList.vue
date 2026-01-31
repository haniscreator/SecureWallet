<template>
  <v-container fluid class="fill-height align-start pa-6">
    <v-row>
      <v-col cols="12">
        <div class="d-flex justify-space-between align-center mb-6">
          <h1 class="text-h5 font-weight-regular text-grey-darken-1">Currencies - Korporatio</h1>
          <v-btn
            v-if="isAdmin"
            color="primary"
            prepend-icon="mdi-plus"
            elevation="2"
            class="text-capitalize"
            :to="{ name: 'CurrencyCreate' }"
          >
            Create Currency
          </v-btn>
        </div>

        <v-card class="rounded-0" elevation="0" border>
            <v-data-table
                :headers="headers"
                :items="currencyStore.currencies"
                :loading="currencyStore.loading"
                :items-per-page="itemsPerPage"
                v-model:page="page"
                hover
                class="currency-table"
            >
                <!-- Status Column -->
                <template v-slot:item.status="{ item }">
                     <v-chip
                        :color="item.status ? 'success' : 'grey'"
                        size="small"
                        variant="tonal"
                        class="font-weight-medium"
                    >
                        {{ item.status ? 'Active' : 'Inactive' }}
                    </v-chip>
                </template>

                <!-- Actions Column -->
                <template v-slot:item.actions="{ item }">
                    <div class="d-flex gap-2 justify-center">
                        <!-- Edit -->
                            <v-btn
                                variant="text"
                                size="small"
                                :color="isAdmin ? 'primary' : 'info'"
                                class="font-weight-bold"
                                :to="{ name: 'CurrencyEdit', params: { id: item.id } }"
                            >
                                {{ isAdmin ? 'EDIT' : 'VIEW' }} <v-icon size="small" class="ml-1">{{ isAdmin ? 'mdi-pencil' : 'mdi-eye' }}</v-icon>
                            </v-btn>

                        <!-- Delete -->
                            <v-btn
                                variant="text"
                                size="small"
                                color="error"
                                class="font-weight-bold"
                                @click="confirmDelete(item)"
                            >
                                DELETE <v-icon size="small" class="ml-1">mdi-delete</v-icon>
                            </v-btn>
                    </div>
                </template>
                
                 <!-- No Data -->
                <template v-slot:no-data>
                    <div class="pa-4 text-center text-grey">
                        No currencies found.
                    </div>
                </template>
                <template v-slot:bottom>
                     <v-divider></v-divider>
                     <div class="d-flex align-center justify-space-between pa-4">
                        <div class="text-caption text-grey">Showing {{ currentCount }} of {{ totalCount }}</div>
                        <div class="d-flex align-center">
                            <v-pagination
                                v-model="page"
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
            </v-data-table>
        </v-card>
      </v-col>
    </v-row>

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="deleteDialog" max-width="400">
        <v-card class="rounded-0">
             <v-card-title class="text-h6 pa-4">Confirm Delete</v-card-title>
             <v-card-text class="pa-4 pt-0">
                 Are you sure you want to delete <strong>{{ editedItem?.name }}</strong>? This action cannot be undone.
             </v-card-text>
             <v-card-actions class="pa-4">
                 <v-spacer></v-spacer>
                 <v-btn color="grey-darken-1" variant="text" @click="deleteDialog = false">Cancel</v-btn>
                 <v-btn color="error" variant="elevated" @click="deleteItem" :loading="currencyStore.loading">Delete</v-btn>
             </v-card-actions>
        </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useCurrencyStore } from '../store';
import { useUserStore } from '@/modules/User/store';
import type { Currency } from '../api';

const currencyStore = useCurrencyStore();
const userStore = useUserStore();
const page = ref(1);
const itemsPerPage = 10;

const totalCount = computed(() => currencyStore.currencies.length);
const totalPages = computed(() => Math.ceil(totalCount.value / itemsPerPage) || 1);
const currentCount = computed(() => {
    const remaining = totalCount.value - (page.value - 1) * itemsPerPage;
    return remaining > 0 ? Math.min(itemsPerPage, remaining) : 0;
});

const isAdmin = computed(() => userStore.currentUser?.role === 'admin');

const headers = [
    { title: 'ID', key: 'id', align: 'start' as const },
    { title: 'Name', key: 'name', align: 'start' as const },
    { title: 'Code', key: 'code', align: 'start' as const },
    { title: 'Symbol', key: 'symbol', align: 'start' as const },
    { title: 'Status', key: 'status', align: 'start' as const },
    { title: 'Actions', key: 'actions', sortable: false, align: 'center' as const },
];

const deleteDialog = ref(false);
const editedItem = ref<Currency | null>(null);

onMounted(async () => {
    // Ensure user loaded
    await userStore.fetchCurrentUser();
    currencyStore.fetchCurrencies();
});

function confirmDelete(item: Currency) {
    editedItem.value = item;
    deleteDialog.value = true;
}

async function deleteItem() {
    if (editedItem.value) {
        await currencyStore.deleteCurrency(editedItem.value.id);
        deleteDialog.value = false;
        editedItem.value = null;
    }
}
</script>

<style scoped>
/* Hide the items-per-page dropdown in the footer */
:deep(.currency-table .v-data-table-footer__items-per-page) {
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
:deep(.currency-table thead tr th) {
    background-color: #F5F6F9 !important;
}
</style>
