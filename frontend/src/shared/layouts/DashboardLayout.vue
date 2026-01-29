<template>
  <v-layout>
    <!-- App Bar (Top Bar) -->
    <!-- order="-1" ensures it comes first in layout, usually spanning full width -->
    <v-app-bar elevation="0" border="b" height="72" color="white" class="px-2" name="app-bar" order="-1">
        <!-- Mobile Burger -->
        <v-app-bar-nav-icon v-if="isMobile" @click.stop="drawer = !drawer" variant="text"></v-app-bar-nav-icon>
        
        <!-- Branding (Top Left Corner of App Bar) -->
        <div class="d-flex align-center ml-2 ml-md-4">
            <v-avatar color="primary" size="40" class="mr-3" variant="flat">
                 <v-icon color="white" size="24">mdi-shield-check</v-icon>
            </v-avatar>
            <span class="text-h6 font-weight-bold text-grey-darken-3 font-family-primary">SecureWallet</span>
        </div>

        <v-spacer></v-spacer>

        <!-- Right Side items -->
        <div class="d-flex align-center gap-4 mr-2 mr-md-4">
             <!-- Korporatio Label -->
            <div class="py-2 px-4 border rounded-lg mr-2 d-none d-sm-flex align-center bg-grey-lighten-5">
                <span class="font-weight-bold text-body-2 text-grey-darken-2">Korporatio</span>
                <v-icon size="small" class="ml-2 text-grey">mdi-chevron-down</v-icon>
            </div>

            <!-- Notification Icon -->
            <v-btn icon density="comfortable" variant="text" class="mr-1 text-grey-darken-1">
                <v-badge dot color="error" offset-x="2" offset-y="2">
                    <v-icon size="24">mdi-bell-outline</v-icon>
                </v-badge>
            </v-btn>

            <!-- User Profile Dropdown -->
            <v-menu min-width="220px" rounded="lg" offset-y>
                <template v-slot:activator="{ props }">
                    <v-btn icon v-bind="props" class="ml-1">
                        <!-- Use mock image for now to match design -->
                        <v-avatar size="40">
                             <v-img src="https://randomuser.me/api/portraits/women/44.jpg" alt="User"></v-img>
                        </v-avatar>
                    </v-btn>
                </template>
                <v-card class="rounded-lg elevation-4 mt-2">
                    <v-card-text class="pa-4">
                        <div class="mx-auto text-center">
                            <v-avatar class="mb-3" size="64">
                                <v-img src="https://randomuser.me/api/portraits/women/44.jpg"></v-img>
                            </v-avatar>
                            <h3 class="text-subtitle-1 font-weight-bold">{{ authStore.user?.name || 'User' }}</h3>
                            <p class="text-caption text-grey mb-3">
                                {{ authStore.user?.email || 'admin@korporatio.com' }}
                            </p>
                            <v-divider class="mb-3"></v-divider>
                            <v-btn rounded variant="text" block prepend-icon="mdi-account-outline" class="justify-start">
                                Profile
                            </v-btn>
                            <v-btn rounded variant="text" block color="error" prepend-icon="mdi-logout" class="justify-start" @click="authStore.logout">
                                Logout
                            </v-btn>
                        </div>
                    </v-card-text>
                </v-card>
            </v-menu>
        </div>
    </v-app-bar>

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
