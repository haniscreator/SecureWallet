<template>
  <v-container fluid class="fill-height align-start pa-6">
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <v-card class="rounded-0" elevation="0" border>
          <v-card-title class="pa-6 pb-4 text-h5 font-weight-bold">
            {{ !isAdmin ? 'View Wallet' : (isEdit ? 'Edit Wallet' : 'Create New Wallet for Korporatio') }}
          </v-card-title>
          <v-divider></v-divider>
          
          <v-card-text class="pa-6">
             <v-alert v-if="error" type="error" class="mb-6" closable @click:close="error = null">{{ error }}</v-alert>

            <v-form ref="form" @submit.prevent="save" validate-on="submit lazy">
                <v-row>
                    <v-col cols="12">
                        <div class="text-subtitle-2 font-weight-bold mb-2">Wallet Name <span v-if="isAdmin" class="text-error">*</span></div>
                        <v-text-field
                            v-model="formData.name"
                            placeholder="Enter wallet name"
                            variant="outlined"
                            density="compact"
                            :readonly="!isAdmin"
                            :rules="isAdmin ? [v => !!v || 'Name is required'] : []"
                        ></v-text-field>
                    </v-col>

                    <!-- Create Mode: Currency & Initial Balance -->
                    <template v-if="!isEdit">
                         <v-col cols="12" md="6">
                            <div class="text-subtitle-2 font-weight-bold mb-2">Currency <span class="text-error">*</span></div>
                            <v-select
                                v-model="formData.currency_id"
                                :items="currencyStore.currencies"
                                item-title="code"
                                item-value="id"
                                placeholder="Select Currency"
                                variant="outlined"
                                density="compact"
                                :rules="[v => !!v || 'Currency is required']"
                                :loading="currencyStore.loading"
                            > 
                              <template v-slot:item="{ props, item }">
                                <v-list-item v-bind="props" :subtitle="item.raw.name"></v-list-item>
                              </template>
                            </v-select>
                        </v-col>
                        <v-col cols="12" md="6">
                             <div class="text-subtitle-2 font-weight-bold mb-2">Initial Balance</div>
                            <v-text-field
                                v-model.number="formData.initial_balance"
                                placeholder="0.00"
                                type="number"
                                variant="outlined"
                                density="compact"
                                min="0"
                            >
                                <template v-slot:append-inner>
                                    <span class="text-grey-darken-1 font-weight-bold pl-2" style="font-size: 0.9rem; align-self: center;">{{ selectedCurrencyCode }}</span>
                                </template>
                            </v-text-field>
                        </v-col>
                    </template>
                    
                    <!-- Edit Mode: Display Info -->
                    <template v-if="isEdit">
                         <v-col cols="12" md="6">
                             <div class="text-subtitle-2 font-weight-bold mb-2">Currency</div>
                             <v-text-field
                                :model-value="currentWalletCurrency"
                                variant="outlined"
                                density="compact"
                                readonly
                                bg-color="grey-lighten-4"
                             ></v-text-field>
                        </v-col>
                         <v-col cols="12" md="6">
                             <div class="text-subtitle-2 font-weight-bold mb-2">Current Balance</div>
                             <v-text-field
                                :model-value="currentWalletBalance"
                                variant="outlined"
                                density="compact"
                                readonly
                                bg-color="grey-lighten-4"
                             ></v-text-field>
                        </v-col>
                    </template>

                    <!-- Status (All Modes) -->
                    <v-col cols="12" md="6">
                        <div class="text-subtitle-2 font-weight-bold mb-2">Status <span v-if="isAdmin" class="text-error">*</span></div>
                        <v-select
                            v-model="formData.status"
                            :items="statusOptions"
                            item-title="title"
                            item-value="value"
                            variant="outlined"
                            density="compact"
                            :readonly="!isAdmin"
                            hide-details
                            placeholder="Select Status"
                        ></v-select>
                    </v-col>

                    <!-- User Access -->
                    <v-col cols="12" md="6">
                        <div class="text-subtitle-2 font-weight-bold mb-2">User Access</div>
                        
                        <!-- Admin: Edit Access -->
                        <v-select
                            v-if="isAdmin"
                            v-model="formData.users"
                            :items="userStore.members"
                            item-title="name"
                            item-value="id"
                            multiple
                            chips
                            closable-chips
                            variant="outlined"
                            density="compact"
                            placeholder="Select users to grant access"
                        ></v-select>

                        <!-- Non-Admin: View Access -->
                        <div v-else class="d-flex flex-wrap gap-2 pa-2 border rounded" style="background-color: #EFF0F3;">
                            <v-chip
                                v-for="user in assignedUsers"
                                :key="user.id"
                                size="small"
                                color="grey-darken-2"
                                variant="tonal"
                            >
                                {{ user.name }}
                            </v-chip>
                            <span v-if="assignedUsers.length === 0" class="text-grey text-caption font-italic">No users assigned</span>
                        </div>
                    </v-col>
                </v-row>

                <v-divider class="mt-6 mb-6"></v-divider>

                <div class="d-flex justify-end gap-2">
                    <v-btn
                        variant="text"
                        color="grey-darken-1"
                        :to="{ name: 'Wallets' }"
                        class="px-6"
                    >
                        {{ isAdmin ? 'Cancel' : 'Back' }}
                    </v-btn>
                    <v-btn
                        v-if="isAdmin"
                        color="primary"
                        type="submit"
                        variant="elevated"
                        class="px-6"
                        :loading="loading"
                    >
                        Save
                    </v-btn>
                </div>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useWalletStore } from '../store';
import { useCurrencyStore } from '@/modules/Currency/store';
import { useUserStore } from '@/modules/User/store';

const route = useRoute();
const router = useRouter();
const walletStore = useWalletStore();
const currencyStore = useCurrencyStore();
const userStore = useUserStore();

const isEdit = computed(() => !!route.params.id);
const isAdmin = computed(() => userStore.currentUser?.role === 'admin');
const loading = ref(false);
const error = ref<string | null>(null);

const formData = ref({
    name: '',
    currency_id: null as number | null,
    initial_balance: 0,
    status: true,
    users: [] as number[],
});

// Display helpers for edit mode
const currentWalletCurrency = ref('');
const currentWalletBalance = ref('');
const assignedUsers = ref<{id: number, name: string}[]>([]); 

const statusOptions = [
    { title: 'Active', value: true },
    { title: 'Frozen', value: false }
];

const selectedCurrencyCode = computed(() => {
    if (!formData.value.currency_id) return '';
    const c = currencyStore.currencies.find(c => c.id === formData.value.currency_id);
    return c ? c.code : '';
}); 

onMounted(async () => {
    // Ensure user loaded
    await userStore.fetchCurrentUser();
    
    // Load currencies if needed
    if (currencyStore.currencies.length === 0) {
        await currencyStore.fetchCurrencies();
    }
    
    // Load users for assignment ONLY if admin
    if (isAdmin.value) {
        await userStore.fetchMembers();
    }

    if (isEdit.value) {
        const id = Number(route.params.id);
        loading.value = true;
        try {
            await walletStore.fetchWalletDetails(id);
            const w = walletStore.currentWallet;
            if (w) {
                formData.value = {
                    name: w.name,
                    currency_id: w.currency_id,
                    initial_balance: 0,
                    status: Boolean(w.status),
                    users: (w as any).users ? (w as any).users.map((u: any) => u.id) : [],
                };
                
                // Populate assigned users for view mode
                if ((w as any).users) {
                    assignedUsers.value = (w as any).users;
                }

                // Resolve Currency Code: Try wallet relation first, then store lookup
                const currencyCode = w.currency?.code || 
                                     currencyStore.currencies.find(c => c.id === w.currency_id)?.code || 
                                     'N/A';
                                     
                const currencySymbol = w.currency?.symbol || 
                                       currencyStore.currencies.find(c => c.id === w.currency_id)?.symbol || 
                                       '';

                currentWalletCurrency.value = currencyCode;
                currentWalletBalance.value = `${currencySymbol}${Number(w.balance).toLocaleString('en-US')}`;
            }
        } catch (e) {
            error.value = 'Failed to load wallet details';
        } finally {
            loading.value = false;
        }
    }
});

async function save() {
    if (!isAdmin.value) return; // Guard clause

    // Basic validation
    if (!formData.value.name) return;
    if (!isEdit.value && !formData.value.currency_id) return;

    loading.value = true;
    error.value = null;
    
    try {
        if (isEdit.value) {
            const walletId = Number(route.params.id);
            await walletStore.updateWallet(walletId, {
                name: formData.value.name,
                status: formData.value.status ? 1 : 0
            });
            // Assign Users
            await walletStore.assignUsers(walletId, formData.value.users);
        } else {
            const res = await walletStore.createWallet({
                name: formData.value.name,
                currency_id: formData.value.currency_id!,
                initial_balance: formData.value.initial_balance,
                status: formData.value.status ? 1 : 0
            });
            
            if (res && res.wallet && formData.value.users.length > 0) {
                 await walletStore.assignUsers(res.wallet.id, formData.value.users);
            }
        }
        router.push({ name: 'Wallets' });
    } catch (e: any) {
        // Notification store handles the popup
    } finally {
        loading.value = false;
    }
}
</script>
