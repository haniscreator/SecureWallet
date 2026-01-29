<template>
  <v-dialog v-model="dialog" max-width="500">
    <v-card>
      <v-card-title>Create Wallet</v-card-title>
      <v-card-text>
        <v-alert v-if="error" type="error" class="mb-4" closable @click:close="error = null">{{ error }}</v-alert>
        
        <v-form @submit.prevent="submit" ref="form">
          <v-text-field
            v-model="name"
            label="Wallet Name"
            variant="outlined"
            :rules="[v => !!v || 'Name is required']"
            required
          ></v-text-field>

          <v-select
            v-model="currencyId"
            :items="currencies"
            item-title="code"
            item-value="id"
            label="Currency"
            variant="outlined"
            :rules="[v => !!v || 'Currency is required']"
            required
            :loading="loadingCurrencies"
          ></v-select>

          <v-text-field
             v-model.number="initialBalance"
             label="Initial Balance"
             type="number"
             variant="outlined"
             min="0"
          ></v-text-field>
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="grey" variant="text" @click="dialog = false">Cancel</v-btn>
        <v-btn color="primary" @click="submit" :loading="loading">Create</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useWalletStore } from '@/modules/Wallet/store';
import { currencyApi, type Currency } from '@/modules/Currency/api';

const props = defineProps<{
  modelValue: boolean;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void;
  (e: 'created'): void;
}>();

const walletStore = useWalletStore();
const name = ref('');
const currencyId = ref<number | null>(null);
const initialBalance = ref(0);
const currencies = ref<Currency[]>([]);
const loadingCurrencies = ref(false);
const loading = ref(false);
const error = ref<string | null>(null);
const form = ref<any>(null);

const dialog = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val),
});

async function fetchCurrencies() {
  loadingCurrencies.value = true;
  try {
    const res = await currencyApi.getCurrencies();
    const data = res.data as any;
    currencies.value = Array.isArray(data) ? data : (data.data || []);
  } catch (e) {
    console.error('Failed to fetch currencies');
  } finally {
    loadingCurrencies.value = false;
  }
}

async function submit() {
  const { valid } = await form.value.validate();
  if (!valid) return;

  loading.value = true;
  error.value = null;
  try {
    await walletStore.createWallet({
      name: name.value,
      currency_id: currencyId.value!,
      initial_balance: initialBalance.value,
    });
    emit('created');
    dialog.value = false;
    // Reset form
    name.value = '';
    currencyId.value = null;
    initialBalance.value = 0;
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Failed to create wallet';
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  fetchCurrencies();
});
</script>
