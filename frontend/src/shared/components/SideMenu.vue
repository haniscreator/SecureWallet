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

    <v-list density="compact" nav class="px-3">
      <template v-if="filteredMenuItems.length > 0">
        <v-list-item 
          v-for="item in filteredMenuItems"
          :key="item.value"
          :to="item.to"
          :prepend-icon="item.icon"
          :title="item.title"
          :value="item.value"
          :active="isItemActive(item)"
          active-color="primary"
          rounded="lg"
          class="mb-1 font-weight-bold text-body-2"
        ></v-list-item>
      </template>
      <template v-else>
        <div class="text-center text-caption text-grey pa-4">
          No matches found
        </div>
      </template>
    </v-list>

    <template v-slot:append>
      <div class="pa-4 text-center">
        <span class="text-caption text-grey">v1.1.0</span>
      </div>
    </template>
  </v-navigation-drawer>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '@/modules/Auth/store';

const props = defineProps<{
  modelValue: boolean;
  isMobile: boolean;
}>();

const emit = defineEmits(['update:modelValue']);
const route = useRoute();

// Menu Items Definition
const menuItems = [
  { title: 'Dashboard', icon: 'mdi-view-dashboard-outline', value: 'dashboard', to: '/dashboard' },
  { title: 'Wallets', icon: 'mdi-wallet-outline', value: 'wallets', to: '/wallets' }, 
  { title: 'Transactions', icon: 'mdi-swap-horizontal', value: 'transactions', to: '/transactions' },
  { title: 'Currencies', icon: 'mdi-currency-usd', value: 'currencies', to: '/currencies' },
  { title: 'Team Members', icon: 'mdi-account-group-outline', value: 'members', to: '/members' },
  { title: 'Approvals', icon: 'mdi-check-decagram', value: 'approvals', to: '/approvals', roles: ['admin', 'manager'] },
  { title: 'Settings', icon: 'mdi-cog-outline', value: 'settings', to: '/settings', roles: ['admin'] },
];

const authStore = useAuthStore();

// Search Logic
const search = ref('');

const filteredMenuItems = computed(() => {
  let items = menuItems;

  // Filter by Role
  items = items.filter(item => {
    if (!item.roles) return true;
    const role = authStore.user?.role;
    return item.roles.includes(role || ''); 
  });

  if (!search.value) return items;
  const query = search.value.toLowerCase();
  return items.filter(item => 
    item.title.toLowerCase().includes(query)
  );
});

function isItemActive(item: any) {
  // dashboard is exact match usually or default
  if (item.to === '/dashboard') {
     return route.path === '/dashboard';
  }
  // Others are prefix based (e.g. /wallets, /wallets/create, /wallets/1/edit)
  return route.path.startsWith(item.to);
}
</script>

<style scoped>
/* Optional: Fine-tune search input styling */
:deep(.v-field__outline) {
    --v-field-border-opacity: 0.15;
}

:deep(.v-list-item__prepend) {
    width: 32px !important;
}

:deep(.v-list-item--active) {
    border-left: 4px solid rgb(var(--v-theme-primary));
    background: rgb(var(--v-theme-primary), 0.1);
    color: rgb(var(--v-theme-primary)) !important;
    font-weight: bold !important;
}

:deep(.v-list-item-title) {
    font-weight: 700 !important;
    color: #505050 !important;
}

:deep(.v-list-item--active .v-list-item-title) {
    color: rgb(var(--v-theme-primary)) !important;
}
</style>
