<template>
  <v-layout>
    <!-- App Bar (Top Bar) -->
    <!-- order="-1" ensures it comes first in layout, usually spanning full width -->
    <!-- Top Navigation -->
    <TopNav 
        :is-mobile="isMobile" 
        @toggle-drawer="drawer = !drawer" 
    />

    <!-- Navigation Drawer -->
    <SideMenu 
      v-model="drawer" 
      :is-mobile="isMobile" 
    />

    <v-main class="bg-grey-lighten-5">
      <v-container fluid class="pa-6 fill-height align-start">
        <router-view></router-view>
      </v-container>
    </v-main>
  </v-layout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '@/modules/Auth/store';
import { useDisplay } from 'vuetify';
import SideMenu from '@/shared/components/SideMenu.vue';
import TopNav from '@/shared/components/TopNav.vue';

const { mobile } = useDisplay();
const authStore = useAuthStore();

// Drawer state
const drawer = ref(true); // Permanent by default on large screens
const isMobile = computed(() => mobile.value);

onMounted(() => {
  if (!authStore.user) {
    authStore.fetchUser();
  }
});
</script>

<style scoped>
.font-family-primary {
    font-family: 'Inter', sans-serif !important;
}
</style>
