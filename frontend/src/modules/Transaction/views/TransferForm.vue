<template>
  <div class="transfer-form-container">
    <div class="page-header mb-6">
      <h1 class="text-h4 font-weight-bold primary--text">Initiate Transfer</h1>
    </div>

    <v-card class="pa-8 rounded-lg elevation-2 transfer-card">
      <v-form v-model="valid" @submit.prevent="submitTransfer">
        
        <!-- Source Wallet Info -->
        <div class="mb-6 pa-4 rounded bg-grey-lighten-4" v-if="selectedSourceWallet" style="background-color: #F5F6F9;">
            <div class="text-h6 font-weight-bold">{{ selectedSourceWallet.name }}</div>
            <div class="text-subtitle-1 text-grey-darken-1">
                Balance: {{ selectedSourceWallet.currency?.symbol }}{{ Number(selectedSourceWallet.balance).toLocaleString() }} {{ selectedSourceWallet.currency?.code }}
            </div>
        </div>
        <div v-else class="mb-6 text-grey">Loading Source Wallet...</div>

        <!-- Transfer Type -->
        <v-label class="mb-2 font-weight-medium">Transfer Type</v-label>
        <v-radio-group v-model="formData.type" inline class="mb-4" @update:model-value="onTypeChange" :rules="[v => !!v || 'Transfer type is required']">
            <v-radio label="Internal Transfer" value="internal"></v-radio>
            <v-radio label="External Transfer" value="external"></v-radio>
        </v-radio-group>

        <!-- Form Content Wrapper with Transition -->
        <v-fade-transition>
            <div v-if="formData.type">
                <!-- Internal: Target Wallet -->
                <v-select
                    v-if="formData.type === 'internal'"
                    v-model="formData.to_wallet_id"
                    :items="internalTargetWallets"
                    item-title="name"
                    item-value="id"
                    label="Choose destination wallet"
                    placeholder="Select a destination wallet"
                    variant="outlined"
                    :rules="[v => !!v || 'Target wallet is required', validateInternalTarget]"
                    required
                    :loading="loadingTargets"
                    class="mb-2"
                >
                    <template v-slot:item="{ props, item }">
                        <v-list-item 
                            v-bind="props" 
                            :subtitle="(item.raw.currency?.code || '') + ' - User: ' + (item.raw.users?.[0]?.name || 'Unknown')"
                        ></v-list-item>
                    </template>
                </v-select>

                <!-- External: Address Input -->
                <v-text-field
                    v-if="formData.type === 'external'"
                    v-model="formData.to_address"
                    label="External Wallet Address"
                    placeholder="e.g. 0x123..."
                    variant="outlined"
                    :rules="[v => !!v || 'Address is required']"
                    required
                    class="mb-2"
                ></v-text-field>

                <!-- Amount -->
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
                    class="mb-2"
                ></v-text-field>

                <!-- Description -->
                <v-textarea
                    v-model="formData.description"
                    label="Description (Optional)"
                    placeholder="What is this transfer for?"
                    variant="outlined"
                    rows="3"
                    class="mb-4"
                ></v-textarea>
            </div>
        </v-fade-transition>

        <v-alert v-if="error" type="error" class="mb-4" closable>{{ error }}</v-alert>
        <v-alert v-if="successMessage" type="success" class="mb-4" closable>{{ successMessage }}</v-alert>

        <div class="d-flex justify-end gap-2 mt-2">
            <v-btn variant="outlined" color="secondary" size="large" @click="router.push('/wallets')" class="mr-2">
                Cancel
            </v-btn>
            <v-btn color="primary" type="submit" :loading="submitting" :disabled="!valid" size="large" width="200">
                Transfer Funds
            </v-btn>
        </div>
      </v-form>
    </v-card>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { walletApi } from '@/modules/Wallet/api';
import { transactionApi } from '@/modules/Transaction/api';

const router = useRouter();
const route = useRoute();

const valid = ref(false);
const submitting = ref(false);
const loadingSource = ref(false);
const loadingTargets = ref(false);
const error = ref('');
const successMessage = ref('');

const wallets = ref<any[]>([]); // User's wallets (for Source info)
const allTargets = ref<any[]>([]); // All system wallets (for Internal Target)

const formData = reactive({
  source_wallet_id: null as number | null,
  type: null as 'internal' | 'external' | null, // Default null
  to_wallet_id: null as number | null,
  to_address: '',
  amount: null as number | null,
  description: '',
});

const selectedSourceWallet = computed(() => {
    return wallets.value.find(w => w.id === formData.source_wallet_id);
});

const onTypeChange = () => {
    formData.to_wallet_id = null;
    formData.to_address = '';
}

// Compute valid targets for Internal Transfer
const internalTargetWallets = computed(() => {
    if (!formData.source_wallet_id || !formData.type) return [];
    
    // Check if source wallet is loaded
    if (!selectedSourceWallet.value) return [];

    // Filter from `allTargets`: 
    // Return ALL other wallets (exclude self)
    return allTargets.value.filter(w => w.id !== formData.source_wallet_id);
});

// Validation rule for internal target
const validateInternalTarget = (value: any) => {
    if (!value) return 'Target wallet is required';
    const target = allTargets.value.find(w => w.id === value);
    if (!target) return true;
    
    // Check Currency
    if (target.currency_id !== selectedSourceWallet.value?.currency_id) {
        return `Currency mismatch. Target wallet is ${target.currency?.code}, Source is ${selectedSourceWallet.value?.currency?.code}.`;
    }

    // Check Status
    if (!target.status) {
        return 'Target wallet is inactive/frozen and cannot receive funds.';
    }

    return true;
};


const amountRules = [
  (v: number) => !!v || 'Amount is required',
  (v: number) => v > 0 || 'Amount must be greater than 0',
  (v: number) => {
      if (!formData.source_wallet_id) return true;
      if (selectedSourceWallet.value && v > parseFloat(selectedSourceWallet.value.balance)) {
          return 'Insufficient balance';
      }
      return true;
  }
];

onMounted(async () => {
  // Load Source Wallet (User's wallets)
  await fetchUserWallets();
  
  // Auto-select Source from query
  if (route.query.from) {
      const fromId = Number(route.query.from);
      const exists = wallets.value.find(w => w.id === fromId);
      if (exists) {
          formData.source_wallet_id = fromId;
      }
  }

  // Load All Targets for Internal Transfer
  await fetchTransferTargets();
});

const fetchUserWallets = async () => {
  loadingSource.value = true;
  try {
    const response = await walletApi.getWallets();
    const data = (response as any).data || response;
    
    if (Array.isArray(data)) {
         wallets.value = data;
    } else {
         wallets.value = (data.data || []); // .data if paginated or wrapped
    }
  } catch (e) {
    console.error('Failed to load wallets', e);
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
        if (Array.isArray(data)) {
            allTargets.value = data;
        } else {
            allTargets.value = (data.data || []);
        }
    } catch (e) {
        console.error('Failed to load transfer targets', e);
    } finally {
        loadingTargets.value = false;
    }
}

const submitTransfer = async () => {
  if (!valid.value) return;
  if (!formData.type) {
      error.value = "Please select a transfer type.";
      return;
  }

  submitting.value = true;
  error.value = '';
  successMessage.value = '';

  try {
    await transactionApi.initiateTransfer({
      source_wallet_id: formData.source_wallet_id!,
      type: formData.type,
      to_wallet_id: formData.type === 'internal' ? formData.to_wallet_id : null,
      to_address: formData.type === 'external' ? formData.to_address : null,
      amount: formData.amount!,
      description: formData.description,
    });
    
    successMessage.value = 'Transfer initiated successfully!';

    setTimeout(() => {
        router.push('/transactions');
    }, 2000);

  } catch (e: any) {
    if (e.response && e.response.data && e.response.data.message) {
        error.value = e.response.data.message;
    } else {
        error.value = 'An error occurred during transfer.';
    }
  } finally {
    submitting.value = false;
  }
};
</script>

<style scoped>
.transfer-form-container {
  max-width: 900px; /* Bigger width as requested */
  margin: 0 auto;
  padding: 24px;
}
.transfer-card {
    border: 1px solid #e0e0e0;
}
.border-dashed {
    border: 1px dashed #bdbdbd;
}
</style>
