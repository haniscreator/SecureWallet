import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useTransactionStore } from '../store';

export function useTransactionList() {
    const store = useTransactionStore();
    const router = useRouter();

    const filters = ref<{
        type: 'credit' | 'debit' | null;
        from_date: string | null;
        to_date: string | null;
        reference: string;
        timezone: string;
    }>({
        type: null,
        from_date: null,
        to_date: null,
        reference: '',
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
    });

    function onApplyFilter(newFilters: any) {
        filters.value = newFilters;
        store.page = 1;
        store.fetchTransactions(filters.value);
    }

    function loadItems({ page, itemsPerPage, sortBy }: { page: number, itemsPerPage: number, sortBy: any[] }) {
        store.page = page;
        store.itemsPerPage = itemsPerPage;

        const sortParams = sortBy && sortBy.length ? {
            sort_by: sortBy[0].key,
            sort_dir: sortBy[0].order
        } : {};

        store.fetchTransactions({ ...filters.value, ...sortParams });
    }

    function viewDetails(item: any) {
        router.push(`/transactions/${item.id}`);
    }

    const showConfirm = ref(false);
    const confirmingItem = ref<any>(null);

    function onCancelClick(item: any) {
        confirmingItem.value = item;
        showConfirm.value = true;
    }

    async function confirmCancel() {
        if (!confirmingItem.value) return;

        try {
            await store.cancelTransaction(confirmingItem.value.id);
            // Refresh list
            store.fetchTransactions(filters.value);
            showConfirm.value = false;
            confirmingItem.value = null;
        } catch (err) {
            console.error('Failed to cancel transaction', err);
        }
    }

    return {
        store,
        filters,
        showConfirm,
        confirmingItem,
        onApplyFilter,
        loadItems,
        viewDetails,
        onCancelClick,
        confirmCancel
    };
}
