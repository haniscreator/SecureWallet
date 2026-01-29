<template>
  <v-app>
    <router-view />
    <v-snackbar
      v-model="notificationStore.show"
      :color="notificationStore.color"
      location="top"
      timeout="3000"
    >
      {{ notificationStore.message }}
      
      <template v-slot:actions>
        <v-btn
          color="white"
          variant="text"
          @click="notificationStore.show = false"
        >
          Close
        </v-btn>
      </template>
    </v-snackbar>
  </v-app>
</template>

<script setup lang="ts">
import { useNotificationStore } from '@/shared/stores/notification';
const notificationStore = useNotificationStore();
</script>
