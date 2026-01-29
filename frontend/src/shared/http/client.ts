import axios from 'axios';
import { useNotificationStore } from '@/shared/stores/notification';

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Request interceptor to add the auth token header
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor to handle errors
apiClient.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    // In Vite/Vue3, we can use the store inside the hook if pinia is active.
    // Since requests happen after main.ts runs, this is safe.
    const notificationStore = useNotificationStore();

    if (error.response) {
      if (error.response.status === 401) {
        // If 401 (Unauthorized), but NOT from the login endpoint itself
        // we redirect. 
        const isLoginRequest = error.config.url && (error.config.url.endsWith('/login') || error.config.url.endsWith('/login/'));

        if (!isLoginRequest) {
          localStorage.removeItem('token');
          window.location.href = '/login';
          return Promise.reject(error);
        }
      }

      // Handle Laravel Validation Errors (422) or generic messages
      const message = error.response.data?.message || 'An unexpected error occurred';
      notificationStore.error(message);
    } else {
      notificationStore.error('Network Error: Please check your connection');
    }
    return Promise.reject(error);
  }
);

export default apiClient;
