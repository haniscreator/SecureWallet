import { defineStore } from 'pinia';
import { ref } from 'vue';
import { transactionApi, type Transaction, type TransactionFilters } from './api';

export const useTransactionStore = defineStore('transaction', () => {
    const transactions = ref<Transaction[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    // Pagination state
    const page = ref(1);
    const totalItems = ref(0);
    const itemsPerPage = ref(15);

    async function fetchTransactions(filters: TransactionFilters = {}) {
        loading.value = true;
        try {
            const params = {
                ...filters,
                page: page.value
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

    return {
        transactions,
        loading,
        error,
        page,
        totalItems,
        itemsPerPage,
        fetchTransactions
    };
});
