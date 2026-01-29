<template>
  <v-container fluid class="fill-height justify-center bg-grey-lighten-4">
    <v-card width="400" elevation="4" class="rounded-lg">
      <v-card-title class="text-h5 text-center py-6 font-weight-bold text-primary">
        Korporatio
      </v-card-title>
      
      <v-card-text>
        <v-alert
          v-if="authStore.error"
          type="error"
          variant="tonal"
          closable
          class="mb-4"
          @click:close="authStore.error = null"
        >
          {{ authStore.error }}
        </v-alert>

        <v-form @submit.prevent="handleLogin" ref="form">
          <v-text-field
            v-model="email"
            label="Email"
            prepend-inner-icon="mdi-email"
            variant="outlined"
            density="comfortable"
            class="mb-2"
            :rules="[v => !!v || 'Email is required']"
          ></v-text-field>

          <v-text-field
            v-model="password"
            label="Password"
            prepend-inner-icon="mdi-lock"
            variant="outlined"
            density="comfortable"
            type="password"
            class="mb-4"
            :rules="[v => !!v || 'Password is required']"
          ></v-text-field>

          <v-btn
            block
            color="primary"
            size="large"
            type="submit"
            :loading="authStore.loading"
          >
            Login
          </v-btn>
        </v-form>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useAuthStore } from '@/modules/Auth/store';

const authStore = useAuthStore();
const email = ref('');
const password = ref('');
const form = ref<any>(null);

async function handleLogin() {
  const { valid } = await form.value.validate();
  if (valid) {
    await authStore.login({
      email: email.value,
      password: password.value,
    });
  }
}
</script>
