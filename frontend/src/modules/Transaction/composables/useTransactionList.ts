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

    return {
        store,
        filters,
        onApplyFilter,
        loadItems,
        viewDetails
    };
}
