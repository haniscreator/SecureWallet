import { defineStore } from 'pinia';
import { ref } from 'vue';
import { transactionApi, type Transaction, type TransactionFilters } from './api';

export const useTransactionStore = defineStore('transaction', () => {
    const transactions = ref<Transaction[]>([]);
    const currentTransaction = ref<Transaction | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    // Pagination state
    const page = ref(1);
    const totalItems = ref(0);
    const itemsPerPage = ref(10);

    async function fetchTransactions(filters: TransactionFilters = {}) {
        loading.value = true;
        try {
            const params = {
                ...filters,
                page: page.value,
                per_page: itemsPerPage.value
            };
            const response = await transactionApi.getTransactions(params);
            const data = response.data;

            transactions.value = data.data || [];
            if (data.meta) {
                totalItems.value = data.meta.total;
                itemsPerPage.value = data.meta.per_page;
            }
        } catch (e: any) {
            error.value = e.message || 'Failed to fetch transactions';
            console.error(e);
        } finally {
            loading.value = false;
        }
    }

    async function fetchTransaction(id: number) {
        loading.value = true;
        error.value = null;
        try {
            const { data } = await transactionApi.getTransaction(id);
            currentTransaction.value = data.data || data; // Handle wrapper
        } catch (e: any) {
            error.value = 'Failed to load transaction details';
            console.error(e);
            throw e;
        } finally {
            loading.value = false;
        }
    }

    async function cancelTransaction(id: number) {
        loading.value = true;
        error.value = null;
        try {
            await transactionApi.cancelTransaction(id);
        } catch (e: any) {
            error.value = e.response?.data?.message || 'Failed to cancel transaction';
            console.error(e);
            throw e;
        } finally {
            loading.value = false;
        }
    }

    return {
        transactions,
        currentTransaction,
        loading,
        error,
        page,
        totalItems,
        itemsPerPage,
        fetchTransactions,
        fetchTransaction,
        cancelTransaction
    };
});
