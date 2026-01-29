import { defineStore } from 'pinia';
import { ref } from 'vue';
import { currencyApi, type Currency, type CreateCurrencyPayload, type UpdateCurrencyPayload } from './api';
import { useNotificationStore } from '@/shared/stores/notification';

export const useCurrencyStore = defineStore('currency', () => {
    const currencies = ref<Currency[]>([]);
    const currentCurrency = ref<Currency | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);
    const notificationStore = useNotificationStore();

    async function fetchCurrencies() {
        loading.value = true;
        try {
            const response = await currencyApi.getCurrencies();
            const data = response.data as any;
            currencies.value = Array.isArray(data) ? data : (data.data || []);
        } catch (e: any) {
            error.value = e.message || 'Failed to fetch currencies';
            console.error(e);
        } finally {
            loading.value = false;
        }
    }

    async function fetchCurrency(id: number) {
        loading.value = true;
        try {
            const response = await currencyApi.getCurrency(id);
            const data = response.data as any;
            currentCurrency.value = data.data || data;
        } catch (e: any) {
            error.value = e.message || 'Failed to fetch currency details';
            throw e;
        } finally {
            loading.value = false;
        }
    }

    async function createCurrency(payload: CreateCurrencyPayload) {
        loading.value = true;
        try {
            await currencyApi.createCurrency(payload);
            await fetchCurrencies(); // Refresh list
            notificationStore.success('Currency created successfully');
        } catch (e: any) {
            notificationStore.error(e.response?.data?.message || 'Failed to create currency');
            throw e;
        } finally {
            loading.value = false;
        }
    }

    async function updateCurrency(id: number, payload: UpdateCurrencyPayload) {
        loading.value = true;
        try {
            await currencyApi.updateCurrency(id, payload);
            await fetchCurrencies(); // Refresh list
            notificationStore.success('Currency updated successfully');
        } catch (e: any) {
            notificationStore.error(e.response?.data?.message || 'Failed to update currency');
            throw e;
        } finally {
            loading.value = false;
        }
    }

    async function deleteCurrency(id: number) {
        loading.value = true;
        try {
            await currencyApi.deleteCurrency(id);
            await fetchCurrencies(); // Refresh list
            notificationStore.success('Currency deleted successfully');
        } catch (e: any) {
            // Handle 403 or other errors
            const msg = e.response?.status === 403
                ? 'You do not have permission to delete currencies'
                : (e.response?.data?.message || 'Failed to delete currency');
            notificationStore.error(msg);
            throw e;
        } finally {
            loading.value = false;
        }
    }

    return {
        currencies,
        currentCurrency,
        loading,
        error,
        fetchCurrencies,
        fetchCurrency,
        createCurrency,
        updateCurrency,
        deleteCurrency
    };
});
