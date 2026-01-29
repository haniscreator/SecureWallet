<template>
  <v-dialog v-model="dialog" max-width="500px">
    <v-card class="rounded-xl">
      <v-card-title class="pa-4 text-h6 font-weight-bold">
        <span class="text-h5">{{ isEdit ? 'Edit Wallet' : 'New Wallet' }}</span>
      </v-card-title>

      <v-divider></v-divider>
      
      <v-card-text class="pa-4">
        <v-form ref="form" @submit.prevent="save">
            <v-container class="pa-0">
            <v-row>
                <v-col cols="12">
                <v-text-field
                    v-model="form.name"
                    label="Wallet Name"
                    variant="outlined"
                    density="compact"
                    hide-details="auto"
                    :rules="[v => !!v || 'Name is required']"
                    required
                ></v-text-field>
                </v-col>
                
                <!-- Currency Selection (Only for Create) -->
                <v-col cols="12" v-if="!isEdit">
                <v-select
                    v-model="form.currency_id"
                    :items="currencyStore.currencies"
                    item-title="code"
                    item-value="id"
                    label="Currency"
                    variant="outlined"
                    density="compact"
                    hide-details="auto"
                    :rules="[v => !!v || 'Currency is required']"
                    :loading="currencyStore.loading"
                    required
                >
                    <template v-slot:item="{ props, item }">
                        <v-list-item v-bind="props" :subtitle="item.raw.name"></v-list-item>
                    </template>
                </v-select>
                </v-col>
                
                 <!-- Initial Balance (Only for Create) -->
                <v-col cols="12" v-if="!isEdit">
                    <v-text-field
                        v-model.number="form.initial_balance"
                        label="Initial Balance"
                        type="number"
                        variant="outlined"
                        density="compact"
                         hide-details="auto"
                         min="0"
                    ></v-text-field>
                </v-col>

                <!-- Status (Only for Edit) -->
                <v-col cols="12" v-if="isEdit">
                <v-switch
                    v-model="form.status"
                    color="primary"
                    :label="`Status: ${form.status ? 'Active' : 'Inactive'}`"
                    hide-details
                    ></v-switch>
                </v-col>
            </v-row>
            </v-container>
        </v-form>
      </v-card-text>

      <v-card-actions class="pa-4 pt-0">
        <v-spacer></v-spacer>
        <v-btn
          color="grey-darken-1"
          variant="text"
          @click="close"
        >
          Cancel
        </v-btn>
        <v-btn
          color="primary"
          variant="elevated"
          class="px-6"
          @click="save"
          :loading="loading"
        >
          Save
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import { useCurrencyStore } from '@/modules/Currency/store';
import type { Wallet } from '../api';

const props = defineProps<{
  modelValue: boolean;
  item?: Wallet | null;
  loading?: boolean;
}>();

const emit = defineEmits(['update:modelValue', 'save']);

const currencyStore = useCurrencyStore();

const dialog = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val),
});

const isEdit = computed(() => !!props.item);

const defaultForm = {
  name: '',
  currency_id: null as number | null,
  initial_balance: 0,
  status: true,
};

const form = ref({ ...defaultForm });

onMounted(() => {
    if (currencyStore.currencies.length === 0) {
        currencyStore.fetchCurrencies();
    }
});

watch(() => props.item, (newItem) => {
  if (newItem) {
    form.value = {
      name: newItem.name,
      currency_id: newItem.currency_id,
      initial_balance: 0, // Not editable
      status: Boolean(newItem.status),
    };
  } else {
    form.value = { ...defaultForm };
  }
}, { immediate: true });

function close() {
  dialog.value = false;
  setTimeout(() => {
    form.value = { ...defaultForm };
  }, 300);
}

function save() {
  if (!form.value.name) return;
  if (!isEdit.value && !form.value.currency_id) return;
  
  emit('save', { ...form.value });
}
</script>
