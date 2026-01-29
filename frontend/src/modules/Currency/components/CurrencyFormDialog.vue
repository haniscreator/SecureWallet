<template>
  <v-dialog v-model="dialog" max-width="500px">
    <v-card class="rounded-xl">
      <v-card-title class="pa-4 text-h6 font-weight-bold">
        <span class="text-h5">{{ isEdit ? 'Edit Currency' : 'New Currency' }}</span>
      </v-card-title>

      <v-divider></v-divider>
      
      <v-card-text class="pa-4">
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-text-field
                v-model="form.name"
                label="Currency Name"
                variant="outlined"
                density="compact"
                hide-details="auto"
                :rules="[v => !!v || 'Name is required']"
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field
                v-model="form.code"
                label="Code (e.g. USD)"
                variant="outlined"
                density="compact"
                hide-details="auto"
                :rules="[v => !!v || 'Code is required']"
                required
                class="text-uppercase"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field
                v-model="form.symbol"
                label="Symbol (e.g. $)"
                variant="outlined"
                density="compact"
                hide-details="auto"
                :rules="[v => !!v || 'Symbol is required']"
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12">
               <v-switch
                  v-model="form.status"
                  color="primary"
                  :label="`Status: ${form.status ? 'Active' : 'Inactive'}`"
                  hide-details
                ></v-switch>
            </v-col>
          </v-row>
        </v-container>
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
import { ref, computed, watch } from 'vue';
import type { Currency } from '../api';

const props = defineProps<{
  modelValue: boolean;
  item?: Currency | null;
  loading?: boolean;
}>();

const emit = defineEmits(['update:modelValue', 'save']);

const dialog = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val),
});

const isEdit = computed(() => !!props.item);

const defaultForm = {
  name: '',
  code: '',
  symbol: '',
  status: true,
};

const form = ref({ ...defaultForm });

watch(() => props.item, (newItem) => {
  if (newItem) {
    form.value = {
      name: newItem.name,
      code: newItem.code,
      symbol: newItem.symbol,
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
  // basic validation check (though v-form is better, simplicity here)
  if (!form.value.name || !form.value.code || !form.value.symbol) return;
  
  emit('save', { ...form.value });
}
</script>
