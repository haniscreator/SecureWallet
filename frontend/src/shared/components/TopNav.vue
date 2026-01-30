<template>
  <v-app-bar elevation="0" border="b" height="72" color="white" class="px-2" name="app-bar" order="-1">
    <!-- Mobile Burger -->
    <v-app-bar-nav-icon v-if="isMobile" @click.stop="$emit('toggle-drawer')" variant="text"></v-app-bar-nav-icon>
    
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
        <div class="py-2 px-4 border rounded-lg mr-2 d-none d-sm-flex align-center" style="background-color: #EFF0F3;">
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
                    <v-avatar size="40">
                         <v-img :src="userAvatar" alt="User"></v-img>
                    </v-avatar>
                </v-btn>
            </template>
            <v-card class="rounded-lg elevation-4 mt-2">
                <v-card-text class="pa-4">
                    <div class="mx-auto text-center">
                        <v-avatar class="mb-3" size="64">
                            <v-img :src="userAvatar"></v-img>
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
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { useAuthStore } from '@/modules/Auth/store';

const props = defineProps<{
    isMobile: boolean
}>();

const emit = defineEmits<{
    (e: 'toggle-drawer'): void
}>();

const authStore = useAuthStore();

const userAvatar = computed(() => {
    // Admin uses specific man profile
    if (authStore.user?.email === 'admin@gmail.com') {
        return 'https://randomuser.me/api/portraits/men/32.jpg';
    }
    // All other users use the women profile
    return 'https://randomuser.me/api/portraits/women/44.jpg';
});

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
