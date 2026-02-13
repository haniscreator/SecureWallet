<template>
  <div class="transfer-form-container">
    <div class="page-header d-flex justify-space-between align-center mb-4">
      <h1 class="text-h4 font-weight-bold primary--text">Initiate Transfer</h1>
      <v-btn color="secondary" variant="outlined" @click="$router.back()">
        Back
      </v-btn>
    </div>

    <v-card class="pa-6 rounded-lg elevation-2">
      <v-form v-model="valid" @submit.prevent="submitTransfer">
        <v-select
          v-model="formData.source_wallet_id"
          :items="wallets"
          item-title="name"
          item-value="id"
          label="Source Wallet"
          placeholder="Select a wallet"
          variant="outlined"
          :rules="[v => !!v || 'Source wallet is required']"
          required
          :loading="loadingWallets"
          @update:model-value="onSourceWalletChange"
        >
          <template v-slot:item="{ props, item }">
            <v-list-item v-bind="props" :subtitle="item.raw.currency.code + ' - Balance: ' + item.raw.balance"></v-list-item>
          </template>
        </v-select>

        <v-select
          v-model="formData.external_wallet_id"
          :items="filteredExternalWallets"
          item-title="name"
          item-value="id"
          label="External Wallet"
          placeholder="Select an external wallet"
          variant="outlined"
          :rules="[v => !!v || 'External wallet is required']"
          required
          :loading="loadingExternalWallets"
          :disabled="!formData.source_wallet_id"
        >
             <template v-slot:item="{ props, item }">
                <v-list-item v-bind="props" :subtitle="item.raw.address"></v-list-item>
            </template>
        </v-select>

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
        ></v-text-field>

        <v-textarea
          v-model="formData.description"
          label="Description (Optional)"
          placeholder="What is this transfer for?"
          variant="outlined"
          rows="3"
        ></v-textarea>

        <v-alert v-if="error" type="error" class="mb-4" closable>{{ error }}</v-alert>
        <v-alert v-if="successMessage" type="success" class="mb-4" closable>{{ successMessage }}</v-alert>

        <div class="d-flex justify-end mt-4">
          <v-btn color="secondary" variant="text" class="mr-2" @click="$router.back()" :disabled="submitting">
            Cancel
          </v-btn>
          <v-btn color="primary" type="submit" :loading="submitting" :disabled="!valid">
            Transfer Funds
          </v-btn>
        </div>
      </v-form>
    </v-card>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { walletApi } from '@/modules/Wallet/api';
import { transactionApi } from '@/modules/Transaction/api';

const router = useRouter();

const valid = ref(false);
const submitting = ref(false);
const loadingWallets = ref(false);
const loadingExternalWallets = ref(false);
const error = ref('');
const successMessage = ref('');

const wallets = ref<any[]>([]);
const externalWallets = ref<any[]>([]);

const formData = reactive({
  source_wallet_id: null as number | null,
  external_wallet_id: null as number | null,
  amount: null as number | null,
  description: '',
});

const onSourceWalletChange = () => {
    formData.external_wallet_id = null;
};

const filteredExternalWallets = computed(() => {
    if (!formData.source_wallet_id) return [];
    const sourceWallet = wallets.value.find(w => w.id === formData.source_wallet_id);
    if (!sourceWallet) return [];
    
    return externalWallets.value.filter(ew => ew.currency_id === sourceWallet.currency_id);
});

const amountRules = [
  (v: number) => !!v || 'Amount is required',
  (v: number) => v > 0 || 'Amount must be greater than 0',
  (v: number) => {
      if (!formData.source_wallet_id) return true;
      const sourceWallet = wallets.value.find(w => w.id === formData.source_wallet_id);
      if (sourceWallet && v > parseFloat(sourceWallet.balance)) {
          return 'Insufficient balance';
      }
      return true;
  }
];

onMounted(async () => {
  await fetchWallets();
  await fetchExternalWallets();
});

const fetchWallets = async () => {
  loadingWallets.value = true;
  try {
    const response = await walletApi.getWallets();
    // Assuming response.data is the array because apiClient returns common Axios response
    // If apiClient unwraps it, we might need to adjust.
    // Based on other files, it seems variable.
    // Let's safe check.
    const data = (response as any).data || response;
    
    if (Array.isArray(data)) {
         wallets.value = data.filter((w: any) => w.status);
    } else {
         // Maybe inside 'data' property again?
         wallets.value = (data.data || []).filter((w: any) => w.status);
    }
  } catch (e) {
    console.error('Failed to load wallets', e);
    error.value = 'Failed to load your wallets.';
  } finally {
    loadingWallets.value = false;
  }
};

const fetchExternalWallets = async () => {
    loadingExternalWallets.value = true;
    try {
        const response = await transactionApi.getExternalWallets();
        // Handle Axios Response wrapper
        const data = (response as any).data || response;
        externalWallets.value = Array.isArray(data) ? data : (data.data || []);
    } catch (e) {
        console.error('Failed to load external wallets', e);
        // Dont block UI, just wont populate
    } finally {
        loadingExternalWallets.value = false;
    }
}

const submitTransfer = async () => {
  if (!valid.value) return;

  submitting.value = true;
  error.value = '';
  successMessage.value = '';

  try {
    await transactionApi.initiateTransfer({
      source_wallet_id: formData.source_wallet_id!,
      external_wallet_id: formData.external_wallet_id!,
      amount: formData.amount!,
      description: formData.description,
    });
    
    successMessage.value = 'Transfer initiated successfully!';

    // Reset form or redirect
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
  max-width: 600px;
  margin: 0 auto;
  padding: 24px;
}
</style>
