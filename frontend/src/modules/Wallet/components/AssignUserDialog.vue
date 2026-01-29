<template>
  <v-dialog v-model="dialog" max-width="500">
    <v-card>
      <v-card-title>Assign Users to Wallet</v-card-title>
      <v-card-text>
        <v-alert v-if="error" type="error" class="mb-4" closable @click:close="error = null">{{ error }}</v-alert>
        
        <p class="mb-4 text-body-2">Select users to grant access to this wallet.</p>

        <v-autocomplete
          v-model="selectedUserIds"
          :items="userStore.members"
          item-title="name"
          item-value="id"
          label="Select Users"
          variant="outlined"
          multiple
          chips
          closable-chips
          :loading="userStore.loading"
        ></v-autocomplete>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="grey" variant="text" @click="dialog = false">Cancel</v-btn>
        <v-btn color="primary" @click="submit" :loading="loading">Assign</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useWalletStore } from '@/modules/Wallet/store';
import { useUserStore } from '@/modules/User/store';

const props = defineProps<{
  modelValue: boolean;
  walletId: number;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void;
  (e: 'assigned'): void;
}>();

const walletStore = useWalletStore();
const userStore = useUserStore();

const selectedUserIds = ref<number[]>([]);
const loading = ref(false);
const error = ref<string | null>(null);

const dialog = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val),
});

async function submit() {
  if (selectedUserIds.value.length === 0) return;

  loading.value = true;
  error.value = null;
  try {
    await walletStore.assignUsers(props.walletId, selectedUserIds.value);
    emit('assigned');
    dialog.value = false;
    selectedUserIds.value = [];
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Failed to assign users';
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  if (userStore.members.length === 0) {
    userStore.fetchMembers();
  }
});
</script>
