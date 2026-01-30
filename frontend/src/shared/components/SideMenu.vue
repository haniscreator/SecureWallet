<template>
  <v-navigation-drawer
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    :permanent="!isMobile"
    :temporary="isMobile"
    class="bg-white border-e"
    width="260"
    elevation="0"
  >
    <div class="pa-4 mt-2">
      <!-- Search -->
      <v-text-field
        v-model="search"
        density="compact"
        variant="outlined"
        label="Search"
        prepend-inner-icon="mdi-magnify"
        hide-details
        class="search-input"
        rounded="lg"
        base-color="grey-lighten-2" 
        style="background-color: #EFF0F3;"
        placeholder="Menu..."
      ></v-text-field>
    </div>

    <v-list density="comfortable" nav class="px-3">
      <template v-if="filteredMenuItems.length > 0">
        <v-list-item 
          v-for="item in filteredMenuItems"
          :key="item.value"
          :to="item.to"
          :prepend-icon="item.icon"
          :title="item.title"
          :value="item.value"
          active-color="primary"
          rounded="lg"
          class="mb-1 font-weight-medium text-body-2 mb-2"
        ></v-list-item>
      </template>
      <template v-else>
        <div class="text-center text-caption text-grey pa-4">
          No matches found
        </div>
      </template>
    </v-list>
  </v-navigation-drawer>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';

const props = defineProps<{
  modelValue: boolean;
  isMobile: boolean;
}>();

const emit = defineEmits(['update:modelValue']);

// Menu Items Definition
const menuItems = [
  { title: 'Dashboard', icon: 'mdi-view-dashboard-outline', value: 'dashboard', to: '/dashboard' },
  { title: 'Wallets', icon: 'mdi-wallet-outline', value: 'wallets', to: '/wallets' }, 
  { title: 'Transactions', icon: 'mdi-swap-horizontal', value: 'transactions', to: '/transactions' },
  { title: 'Currencies', icon: 'mdi-currency-usd', value: 'currencies', to: '/currencies' },
  { title: 'Team Members', icon: 'mdi-account-group-outline', value: 'members', to: '/members' },
];

// Search Logic
const search = ref('');

const filteredMenuItems = computed(() => {
  if (!search.value) return menuItems;
  const query = search.value.toLowerCase();
  return menuItems.filter(item => 
    item.title.toLowerCase().includes(query)
  );
});
</script>

<style scoped>
/* Optional: Fine-tune search input styling */
:deep(.v-field__outline) {
    --v-field-border-opacity: 0.15;
}

:deep(.v-list-item--active) {
    border-left: 4px solid rgb(var(--v-theme-primary));
    background: rgb(var(--v-theme-primary), 0.1);
    color: rgb(var(--v-theme-primary)) !important;
}
</style>
