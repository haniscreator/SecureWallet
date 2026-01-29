<template>
  <v-layout>
    <v-navigation-drawer v-model="drawer" permanent>
      <v-list>
        <v-list-item
          prepend-avatar="https://randomuser.me/api/portraits/men/85.jpg"
          :title="authStore.user?.name || 'User'"
          :subtitle="authStore.user?.email || 'user@example.com'"
        ></v-list-item>
      </v-list>

      <v-divider></v-divider>

      <v-list density="compact" nav>
        <v-list-item 
          prepend-icon="mdi-view-dashboard" 
          title="Dashboard" 
          value="dashboard" 
          to="/dashboard"
        ></v-list-item>
        
        <v-list-item 
          prepend-icon="mdi-wallet" 
          title="Wallets" 
          value="wallets" 
          to="/wallets"
        ></v-list-item>

        <v-list-group value="Admin" v-if="isAdmin">
          <template v-slot:activator="{ props }">
            <v-list-item
              v-bind="props"
              prepend-icon="mdi-shield-account"
              title="Admin"
            ></v-list-item>
          </template>

          <v-list-item
            prepend-icon="mdi-account-group"
            title="Members"
            value="members"
            to="/members"
          ></v-list-item>
        </v-list-group>
      </v-list>

      <template v-slot:append>
        <div class="pa-2">
          <v-btn block color="error" variant="text" @click="authStore.logout">
            Logout
          </v-btn>
        </div>
      </template>
    </v-navigation-drawer>

    <v-app-bar elevation="1">
      <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
      <v-app-bar-title>Korporatio</v-app-bar-title>
    </v-app-bar>

    <v-main>
      <v-container fluid>
        <router-view></router-view>
      </v-container>
    </v-main>
  </v-layout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '@/modules/Auth/store';

const drawer = ref(true);
const authStore = useAuthStore();

const isAdmin = computed(() => authStore.user?.role === 'admin');

onMounted(() => {
  if (!authStore.user) {
    authStore.fetchUser();
  }
});
</script>
