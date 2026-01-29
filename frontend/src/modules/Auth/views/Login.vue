<template>
  <v-container fluid class="fill-height bg-grey-lighten-5">
    <v-row justify="center" align="center">
      <v-col cols="12" sm="8" md="6" lg="4">
        <!-- Modern Login Card -->
        <v-card class="mx-auto rounded-xl" elevation="0" border>
            <div class="pa-8 text-center bg-white rounded-t-xl">
                 <!-- Logo -->
                <v-avatar color="primary" size="56" class="mb-4" variant="flat">
                    <v-icon color="white" size="32">mdi-shield-check</v-icon>
                </v-avatar>
                <h1 class="text-h4 font-weight-bold text-grey-darken-3 mb-2 font-family-primary">SecureWallet</h1>
                <p class="text-body-1 text-grey-darken-1">Please sign in to continue</p>
            </div>

            <v-card-text class="pa-8 pt-0">
                <v-form @submit.prevent="handleLogin" ref="form">
                <v-text-field
                    v-model="email"
                    label="Email Address"
                    prepend-inner-icon="mdi-email-outline"
                    variant="outlined"
                    density="comfortable"
                    class="mb-4"
                    rounded="lg"
                    base-color="grey-lighten-1"
                    :rules="[v => !!v || 'Email is required']"
                ></v-text-field>

                <v-text-field
                    v-model="password"
                    label="Password"
                    prepend-inner-icon="mdi-lock-outline"
                    :type="showPassword ? 'text' : 'password'"
                    :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                    @click:append-inner="showPassword = !showPassword"
                    variant="outlined"
                    density="comfortable"
                    class="mb-6"
                    rounded="lg"
                    base-color="grey-lighten-1"
                    :rules="[v => !!v || 'Password is required']"
                ></v-text-field>

                <v-btn
                    block
                    color="primary"
                    size="large"
                    type="submit"
                    :loading="loading"
                    elevation="0"
                    rounded="lg"
                    class="text-capitalize font-weight-bold"
                    height="48"
                >
                    Sign In
                </v-btn>
                </v-form>
                
                <div class="mt-6 text-center">
                    <a href="#" class="text-decoration-none text-body-2 text-primary font-weight-medium">
                        Forgot password?
                    </a>
                </div>
            </v-card-text>
        </v-card>
        
        <div class="text-center mt-6 text-caption text-grey">
            &copy; 2024 Korporatio. All rights reserved.
        </div>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useAuthStore } from '@/modules/Auth/store';
import { useRouter } from 'vue-router';

const email = ref('');
const password = ref('');
const showPassword = ref(false);
const loading = ref(false); // Local loading state for button feedback

const authStore = useAuthStore();
const router = useRouter();

const handleLogin = async () => {
  if (!email.value || !password.value) return;

  loading.value = true;
  try {
    const success = await authStore.login({
      email: email.value,
      password: password.value,
    });
    
    if (success) {
      router.push('/dashboard');
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.font-family-primary {
    font-family: 'Inter', sans-serif !important;
}
</style>
