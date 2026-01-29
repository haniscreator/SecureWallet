<template>
  <v-container fluid class="fill-height align-start pa-6 bg-grey-lighten-4">
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <v-card class="rounded-xl" elevation="0" border>
          <v-card-title class="pa-6 pb-0 text-h5 font-weight-bold">
            {{ isEdit ? 'Edit Wallet' : 'New Wallet' }}
          </v-card-title>
          
          <v-card-text class="pa-6">
             <v-alert v-if="error" type="error" class="mb-6" closable @click:close="error = null">{{ error }}</v-alert>

            <v-form ref="form" @submit.prevent="save" validate-on="submit lazy">
                <v-row>
                    <v-col cols="12">
                        <div class="text-subtitle-2 font-weight-bold mb-2">Wallet Name <span class="text-error">*</span></div>
                        <v-text-field
                            v-model="formData.name"
                            placeholder="Enter wallet name"
                            variant="outlined"
                            density="compact"
                            :rules="[v => !!v || 'Name is required']"
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
                            ></v-text-field>
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
                    <v-col cols="12">
                        <div class="text-subtitle-2 font-weight-bold mb-2">Status <span class="text-error">*</span></div>
                        <div class="d-flex align-center">
                            <v-switch
                                v-model="formData.status"
                                color="primary"
                                hide-details
                                density="compact"
                                class="ma-0"
                            ></v-switch>
                            <span class="ml-2 pt-1 font-weight-medium">{{ formData.status ? 'Active' : 'Inactive' }}</span>
                        </div>
                    </v-col>
                </v-row>

                <div class="d-flex justify-end gap-2 mt-6">
                    <v-btn
                        variant="text"
                        color="grey-darken-1"
                        :to="{ name: 'Wallets' }"
                        class="px-6"
                    >
                        Cancel
                    </v-btn>
                    <v-btn
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

const route = useRoute();
const router = useRouter();
const walletStore = useWalletStore();
const currencyStore = useCurrencyStore();

const isEdit = computed(() => !!route.params.id);
const loading = ref(false);
const error = ref<string | null>(null);

const formData = ref({
    name: '',
    currency_id: null as number | null,
    initial_balance: 0,
    status: true,
});

// Display helpers for edit mode
const currentWalletCurrency = ref('');
const currentWalletBalance = ref('');

onMounted(async () => {
    // Load currencies if needed
    if (currencyStore.currencies.length === 0) {
        await currencyStore.fetchCurrencies();
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
                };
                
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
    // Basic validation
    if (!formData.value.name) return;
    if (!isEdit.value && !formData.value.currency_id) return;

    loading.value = true;
    error.value = null;
    
    try {
        if (isEdit.value) {
            await walletStore.updateWallet(Number(route.params.id), {
                name: formData.value.name,
                status: formData.value.status ? 1 : 0
            });
        } else {
            await walletStore.createWallet({
                name: formData.value.name,
                currency_id: formData.value.currency_id!,
                initial_balance: formData.value.initial_balance,
                status: formData.value.status ? 1 : 0
            });
        }
        router.push({ name: 'Wallets' });
    } catch (e: any) {
        // Notification store handles the popup, but we can also show inline error if needed
        // The store throws, so we catch here to stop loading spinner
        // error.value = e.message; 
    } finally {
        loading.value = false;
    }
}
</script>
