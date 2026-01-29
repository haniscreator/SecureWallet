import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { authApi, type LoginPayload, type User } from './api';
import router from '@/router';

export const useAuthStore = defineStore('auth', () => {
    const token = ref<string | null>(localStorage.getItem('token'));
    const user = ref<User | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const isAuthenticated = computed(() => !!token.value);

    async function login(payload: LoginPayload) {
        loading.value = true;
        error.value = null;
        try {
            const response = await authApi.login(payload);
            token.value = response.data.token;
            user.value = response.data.user;

            localStorage.setItem('token', token.value);

            // Redirect to dashboard
            router.push('/dashboard');
        } finally {
            loading.value = false;
        }
    }

    async function logout() {
        try {
            await authApi.logout();
        } catch (err) {
            console.error('Logout error', err);
        } finally {
            token.value = null;
            user.value = null;
            localStorage.removeItem('token');
            router.push('/login');
        }
    }

    async function fetchUser() {
        if (!token.value) return;
        try {
            const response = await authApi.getUser();
            user.value = response.data;
        } catch (err) {
            // If fetching user fails (e.g. invalid token), logout
            logout();
        }
    }

    return {
        token,
        user,
        loading,
        error,
        isAuthenticated,
        login,
        logout,
        fetchUser
    };
});
