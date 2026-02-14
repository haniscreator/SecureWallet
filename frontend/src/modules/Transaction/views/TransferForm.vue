<template>
  <div class="transfer-form-container">
    <div class="page-header mb-6 d-flex align-center justify-space-between">
      <h1 class="text-h4 font-weight-bold primary--text">Initiate Transfer</h1>
    </div>

    <!-- Source Wallet Info -->
    <v-card class="mb-6 pa-6 rounded-0 elevation-2" v-if="selectedSourceWallet">
        <div class="d-flex align-center justify-space-between">
            <div>
                <div class="text-subtitle-2 text-grey-darken-1">Source Wallet</div>
                <div class="text-h6 font-weight-bold">{{ selectedSourceWallet.name }}</div>
            </div>
            <div class="text-right">
                <div class="text-subtitle-2 text-grey-darken-1">Available Balance</div>
                <div class="text-h6 font-weight-bold" :class="{'text-error': Number(selectedSourceWallet.available_balance) < Number(selectedSourceWallet.balance)}">
                    {{ selectedSourceWallet.currency?.symbol }}{{ Number(selectedSourceWallet.available_balance).toLocaleString() }} 
                    <span class="text-body-2 text-grey-darken-1">{{ selectedSourceWallet.currency?.code }}</span>
                </div>
                <div class="text-caption text-grey-darken-1" v-if="Number(selectedSourceWallet.available_balance) < Number(selectedSourceWallet.balance)">
                    (Total: {{ Number(selectedSourceWallet.balance).toLocaleString() }})
                </div>
            </div>
        </div>
    </v-card>
    <div v-else class="mb-6 text-grey">Loading Source Wallet...</div>

    <!-- Lottie Animation -->
    <div class="d-flex justify-center mb-6">
        <div ref="lottieContainer" style="width: 50px; height: 50px;"></div>
    </div>

    <v-card class="pa-6 rounded-0 elevation-2">
        <!-- Step 1: Choose Transfer Type (Cards) -->
        <h3 class="text-h6 mb-4 font-weight-bold">Choose Transfer Type</h3>
        <v-row class="mb-6">
            <v-col cols="12" sm="6">
                <v-card 
                    class="py-8 px-4 text-center cursor-pointer transition-swing hover-card d-flex flex-column align-center justify-center rounded-0"
                    elevation="0"
                    variant="outlined"
                    min-height="250"
                    @click="formData.type = 'internal'"
                    :class="{'selected-card': formData.type === 'internal'}"
                    :color="formData.type === 'internal' ? 'primary-lighten-5' : undefined"
                    style="border-color: #e0e0e0;"
                >
                    <v-icon size="64" color="primary" class="mb-4">mdi-bank-transfer</v-icon>
                    <div class="text-h6 font-weight-bold text-no-wrap mb-2">Internal Transfer</div>
                    <div class="text-body-2 text-grey-darken-1">Send funds instantly to another user's wallet within the platform.</div>
                </v-card>
            </v-col>
            
            <v-col cols="12" sm="6">
                <v-card 
                    class="py-8 px-4 text-center cursor-pointer transition-swing hover-card d-flex flex-column align-center justify-center rounded-0"
                    elevation="0"
                    variant="outlined"
                    min-height="250"
                    @click="formData.type = 'external'"
                    :class="{'selected-card': formData.type === 'external'}"
                    :color="formData.type === 'external' ? 'primary-lighten-5' : undefined"
                    style="border-color: #e0e0e0;"
                >
                    <v-icon size="64" color="secondary" class="mb-4">mdi-earth</v-icon>
                    <div class="text-h6 font-weight-bold text-no-wrap mb-2">External Transfer</div>
                    <div class="text-body-2 text-grey-darken-1">Withdraw funds to an external blockchain address.</div>
                </v-card>
            </v-col>
        </v-row>

        <!-- Step 2: Transfer Details Form -->
        <v-expand-transition>
            <div v-if="formData.type">
                <v-divider class="mb-6"></v-divider>
                <h3 class="text-h6 mb-4 font-weight-bold">Transfer Details</h3>
                <v-form v-model="validDetails" @submit.prevent="submitTransfer">
                    <!-- Internal: Target Wallet (Col 8) -->
                    <v-row v-if="formData.type === 'internal'">
                        <v-col cols="12" md="8">
                            <v-select
                                v-model="formData.to_wallet_id"
                                :items="internalTargetWallets"
                                item-title="name"
                                item-value="id"
                                label="Choose destination wallet"
                                placeholder="Select a destination wallet"
                                variant="outlined"
                                :rules="[v => !!v || 'Destination wallet is required', validateInternalTarget]"
                                required
                                :loading="loadingTargets"
                                class="mb-4 right-aligned-messages"
                                autocomplete="off"
                            >
                                <template v-slot:item="{ props, item }">
                                    <v-list-item 
                                        v-bind="props" 
                                        :subtitle="(item.raw.currency?.code || '') + ' - User: ' + (item.raw.users?.[0]?.name || 'Unknown')"
                                    ></v-list-item>
                                </template>
                            </v-select>
                        </v-col>
                    </v-row>

                    <!-- External: Address Input (Col 8) -->
                    <v-row v-if="formData.type === 'external'">
                        <v-col cols="12" md="8">
                            <v-text-field
                                v-model="formData.to_address"
                                label="External Wallet Address"
                                placeholder="e.g. 0x123..."
                                variant="outlined"
                                :rules="[v => !!v || 'Address is required']"
                                required
                                class="mb-4 right-aligned-messages"
                                @blur="validateExternalAddress"
                                @input="addressValidationResult = null"
                                :error="addressValidationResult && !addressValidationResult.valid"
                                :color="addressValidationResult?.valid ? 'success' : 'error'"
                                autocomplete="off"
                            >
                                <template v-slot:details v-if="addressValidationResult && (!addressValidationResult.valid || !addressValidationResult.exists)">
                                    <div class="text-caption" :class="{
                                        'text-error': !addressValidationResult.valid,
                                        'text-warning': addressValidationResult.valid && !addressValidationResult.exists
                                    }">
                                        {{ addressValidationResult.message }}
                                    </div>
                                </template>
                            </v-text-field>
                        </v-col>
                    </v-row>

                    <!-- Amount (Half Width) -->
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-text-field
                                v-model.number="formData.amount"
                                label="Amount"
                                placeholder="0.00"
                                variant="outlined"
                                type="number"
                                min="0.01"
                                step="0.01"
                                :rules="amountRules"
                                required
                                class="mb-4 right-aligned-messages"
                                autocomplete="off"
                            ></v-text-field>
                        </v-col>
                    </v-row>

                    <!-- Description -->
                    <v-textarea
                        v-model="formData.description"
                        label="Description (Optional)"
                        placeholder="What is this transfer for?"
                        variant="outlined"
                        rows="3"
                        class="mb-4"
                        autocomplete="off"
                    ></v-textarea>
                    
                    <v-alert v-if="error" type="error" class="mb-4" closable>{{ error }}</v-alert>

                    <div class="d-flex justify-end align-center mt-4">
                        <v-btn variant="text" color="grey-darken-1" class="mr-4 text-none text-subtitle-1" @click="router.push('/wallets')">
                            Cancel
                        </v-btn>
                        <v-btn color="primary" variant="flat" @click="submitTransfer" :loading="submitting" size="large" width="200" class="text-none text-subtitle-1"
                            :disabled="!validDetails || (formData.type === 'external' && addressValidationResult?.valid === false)"
                        >
                            Transfer Funds
                        </v-btn>
                    </div>
                </v-form>
            </div>
        </v-expand-transition>
    </v-card>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { walletApi } from '@/modules/Wallet/api';
import { transactionApi } from '@/modules/Transaction/api';
import lottie from 'lottie-web';
import { useNotificationStore } from '@/shared/stores/notification';

const router = useRouter();
const route = useRoute();
const notificationStore = useNotificationStore();
const lottieContainer = ref<HTMLElement | null>(null);

const validDetails = ref(false);
const submitting = ref(false);
const loadingSource = ref(false);
const loadingTargets = ref(false);
const error = ref('');

const wallets = ref<any[]>([]); 
const allTargets = ref<any[]>([]); 

const formData = reactive({
  source_wallet_id: null as number | null,
  type: null as 'internal' | 'external' | null, 
  to_wallet_id: null as number | null,
  to_address: '',
  amount: null as number | null,
  description: '',
});

const selectedSourceWallet = computed(() => {
    return wallets.value.find(w => w.id === formData.source_wallet_id);
});

const internalTargetWallets = computed(() => {
    if (!formData.source_wallet_id || !selectedSourceWallet.value) return [];
    return allTargets.value.filter(w => w.id !== formData.source_wallet_id);
});

const validateInternalTarget = (value: any) => {
    if (!value) return 'Destination wallet is required';
    const target = allTargets.value.find(w => w.id === value);
    if (!target) return true;
    
    if (target.currency_id !== selectedSourceWallet.value?.currency_id) {
        return `Currency mismatch. Destination wallet is ${target.currency?.code}, Source is ${selectedSourceWallet.value?.currency?.code}.`;
    }

    if (!target.status) {
        return 'Destination wallet is inactive/frozen and cannot receive funds.';
    }

    return true;
};

const amountRules = [
  (v: number) => !!v || 'Amount is required',
  (v: number) => v > 0 || 'Amount must be greater than 0',
  (v: number) => {
      if (!formData.source_wallet_id) return true;
      if (selectedSourceWallet.value) {
            // Check available balance (including pending)
           const available = parseFloat(selectedSourceWallet.value.available_balance || selectedSourceWallet.value.balance);
           if (v > available) {
               return `Insufficient funds. Available: ${Number(available).toLocaleString()}`;
           }
      }
      return true;
  }
];

onMounted(async () => {
  if (lottieContainer.value) {
    lottie.loadAnimation({
      container: lottieContainer.value,
      renderer: 'svg',
      loop: true,
      autoplay: true,
      path: '/Arrow Down.json'
    });
  }

  await fetchUserWallets();
  if (route.query.from) {
      const fromId = Number(route.query.from);
      const exists = wallets.value.find(w => w.id === fromId);
      if (exists) formData.source_wallet_id = fromId;
  }
  await fetchTransferTargets();
});

const fetchUserWallets = async () => {
  loadingSource.value = true;
  try {
    const response = await walletApi.getWallets();
    const data = (response as any).data || response;
    wallets.value = Array.isArray(data) ? data : (data.data || []);
  } catch (e) {
    error.value = 'Failed to load source wallet info.';
  } finally {
    loadingSource.value = false;
  }
};

const fetchTransferTargets = async () => {
    loadingTargets.value = true;
    try {
        const response = await walletApi.getTransferTargets();
        const data = (response as any).data || response;
        allTargets.value = Array.isArray(data) ? data : (data.data || []);
    } catch (e) {
        console.error('Failed to load transfer targets', e);
    } finally {
        loadingTargets.value = false;
    }
}

const addressValidationResult = ref<{ valid: boolean; exists: boolean; message: string } | null>(null);

const validateExternalAddress = async () => {
    if (!formData.to_address || formData.type !== 'external') return;

    // Reset result
    addressValidationResult.value = null;

    try {
        const response = await walletApi.validateAddress(
            formData.to_address, 
            selectedSourceWallet.value?.currency_id
        );
        const data = (response as any).data || response;
        addressValidationResult.value = data.data || data;
    } catch (e) {
        console.error('Address validation failed', e);
    }
};

const submitTransfer = async () => {
  if (formData.amount && formData.amount <= 0) return;
  if (formData.type === 'internal' && !formData.to_wallet_id) return;
  if (formData.type === 'external' && !formData.to_address) return;

  submitting.value = true;
  error.value = '';

  try {
    await transactionApi.initiateTransfer({
      source_wallet_id: formData.source_wallet_id!,
      type: formData.type!,
      to_wallet_id: formData.type === 'internal' ? formData.to_wallet_id : null,
      to_address: formData.type === 'external' ? formData.to_address : null,
      amount: formData.amount!,
      description: formData.description,
    });
    
    notificationStore.success('Transfer initiated successfully!');
    router.push('/wallets');
  } catch (e: any) {
    error.value = e.response?.data?.message || 'An error occurred during transfer.';
  } finally {
    submitting.value = false;
  }
};
</script>

<style scoped>
.transfer-form-container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 16px;
}
.transfer-card {
    border: 1px solid #e0e0e0;
}

.hover-card:hover {
    background-color: #f5f5f5;
    border-color: #1976D2 !important;
    transform: translateY(-2px);
}
.selected-card {
    border: 2px solid #1976D2;
    background-color: #F0F7FF;
}

/* --- Right Aligned Validation Message --- */
.right-aligned-messages :deep(.v-input__details) {
    justify-content: flex-end;
    text-align: right;
}

@media (max-width: 600px) {
    .text-h6 {
        font-size: 1rem !important; /* Adjust for mobile if needed */
    }
}
</style>