<template>
  <v-container fluid class="fill-height align-start pa-6 bg-grey-lighten-4">
    <v-row>
      <v-col cols="12">
        <div class="d-flex justify-space-between align-center mb-6">
          <h1 class="text-h4 font-weight-bold text-grey-darken-3">Wallets</h1>
          <v-btn
            v-if="isAdmin"
            color="primary"
            prepend-icon="mdi-plus"
            elevation="2"
            class="text-capitalize"
            :to="{ name: 'WalletCreate' }"
          >
            Add Wallet
          </v-btn>
        </div>

        <!-- Filters -->
        <v-card class="rounded-xl mb-6" elevation="0" border>
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

        <!-- Wallets Table -->
        <v-card class="rounded-xl" elevation="0" border>
            <v-data-table
                :headers="headers"
                :items="walletStore.wallets"
                :loading="walletStore.loading"
                :items-per-page="10"
                hover
                class="pa-2 wallet-table"
            >
                <!-- Currency Column -->
                <template v-slot:item.currency="{ item }">
                    <span class="font-weight-medium">{{ item.currency?.code || 'N/A' }}</span>
                </template>

                 <!-- Balance Column -->
                <template v-slot:item.balance="{ item }">
                    <span :class="Number(item.balance) < 0 ? 'text-error' : 'text-success'" class="font-weight-bold">
                        {{ item.currency?.symbol || '' }}{{ Number(item.balance).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                    </span>
                </template>

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

                <!-- User Access Column -->
                <template v-slot:item.users="{ item }">
                    <div v-if="item.users && item.users.length > 0">
                        <!-- Show avatars or text list? Text list as requested "User Name who got access" -->
                         <div class="d-flex flex-wrap gap-1">
                            <v-chip v-for="user in item.users.slice(0, 3)" :key="user.id" size="x-small" density="comfortable">
                                {{ user.name }}
                            </v-chip>
                            <v-chip v-if="item.users.length > 3" size="x-small" density="comfortable" variant="outlined">
                                +{{ item.users.length - 3 }} more
                            </v-chip>
                         </div>
                    </div>
                    <span v-else class="text-grey text-caption">No users assigned</span>
                </template>

                <!-- Actions Column -->
                <template v-slot:item.actions="{ item }">
                    <div class="d-flex gap-2 justify-center">
                        <v-tooltip :text="isAdmin ? 'Edit Wallet' : 'View Wallet'" location="top">
                          <template v-slot:activator="{ props }">
                            <v-btn
                                v-bind="props"
                                icon
                                variant="text"
                                size="small"
                                :color="isAdmin ? 'primary' : 'info'"
                                :to="{ name: 'WalletEdit', params: { id: item.id } }"
                            >
                                <v-icon>{{ isAdmin ? 'mdi-pencil' : 'mdi-eye' }}</v-icon>
                            </v-btn>
                          </template>
                        </v-tooltip>
                    </div>
                </template>
                
                <!-- No Data -->
                <template v-slot:no-data>
                    <div class="pa-4 text-center text-grey">
                        No wallets found.
                    </div>
                </template>
            </v-data-table>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useWalletStore } from '../store';
import { currencyApi, type Currency } from '@/modules/Currency/api';
import { useUserStore } from '@/modules/User/store';

const walletStore = useWalletStore();
const userStore = useUserStore();
const currencies = ref<Currency[]>([]);

const isAdmin = computed(() => userStore.currentUser?.role === 'admin');

const headers = [
    { title: 'ID', key: 'id', align: 'start' as const },
    { title: 'Name', key: 'name', align: 'start' as const },
    { title: 'Currency', key: 'currency', align: 'start' as const },
    { title: 'Balance', key: 'balance', align: 'end' as const },
    { title: 'Status', key: 'status', align: 'start' as const },
    { title: 'User Access', key: 'users', align: 'start' as const, sortable: false },
    { title: 'Actions', key: 'actions', sortable: false, align: 'center' as const },
];

const statusOptions = [
    { title: 'Active', value: true },
    { title: 'Inactive', value: false },
];

const filters = ref({
    name: '',
    currency_id: null as number | null,
    status: null as boolean | null,
});

async function fetchCurrencies() {
    try {
        const res = await currencyApi.getCurrencies();
        const data = res.data as any;
        currencies.value = Array.isArray(data) ? data : (data.data || []);
    } catch (e) {
        console.error('Failed to fetch currencies');
    }
}

function applyFilters() {
    walletStore.fetchWallets(filters.value);
}

function clearFilters() {
    filters.value = {
        name: '',
        currency_id: null,
        status: null,
    };
    applyFilters();
}

onMounted(async () => {
    await userStore.fetchCurrentUser();
    walletStore.fetchWallets(); // Initial fetch
    fetchCurrencies();
});
</script>

<style scoped>
/* Hide the items-per-page dropdown in the footer */
:deep(.wallet-table .v-data-table-footer__items-per-page) {
    display: none !important;
}
</style>
