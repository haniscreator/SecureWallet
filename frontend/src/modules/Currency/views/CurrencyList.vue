<template>
  <v-container fluid class="fill-height align-start pa-6 bg-grey-lighten-4">
    <v-row>
      <v-col cols="12">
        <div class="d-flex justify-space-between align-center mb-6">
          <h1 class="text-h4 font-weight-bold text-grey-darken-3">Currencies</h1>
          <v-btn
            color="primary"
            prepend-icon="mdi-plus"
            elevation="2"
            class="text-capitalize"
            :to="{ name: 'CurrencyCreate' }"
          >
            Add Currency
          </v-btn>
        </div>

        <v-card class="rounded-xl" elevation="0" border>
            <v-data-table
                :headers="headers"
                :items="currencyStore.currencies"
                :loading="currencyStore.loading"
                hover
                class="pa-2"
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
                        <v-tooltip text="Edit Currency" location="top">
                          <template v-slot:activator="{ props }">
                            <v-btn
                                v-bind="props"
                                icon
                                variant="text"
                                size="small"
                                color="primary"
                                :to="{ name: 'CurrencyEdit', params: { id: item.id } }"
                            >
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                          </template>
                        </v-tooltip>

                         <v-tooltip text="Delete Currency" location="top">
                          <template v-slot:activator="{ props }">
                            <v-btn
                                v-bind="props"
                                icon
                                variant="text"
                                size="small"
                                color="error"
                                @click="confirmDelete(item)"
                            >
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                          </template>
                        </v-tooltip>
                    </div>
                </template>
                
                 <!-- No Data -->
                <template v-slot:no-data>
                    <div class="pa-4 text-center text-grey">
                        No currencies found.
                    </div>
                </template>
            </v-data-table>
        </v-card>
      </v-col>
    </v-row>

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="deleteDialog" max-width="400">
        <v-card class="rounded-xl">
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
import { ref, onMounted } from 'vue';
import { useCurrencyStore } from '../store';
import type { Currency } from '../api';

const currencyStore = useCurrencyStore();

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

onMounted(() => {
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
