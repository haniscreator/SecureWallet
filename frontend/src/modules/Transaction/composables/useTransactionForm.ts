import { ref } from 'vue';
import { useRoute } from 'vue-router';
import { useTransactionStore } from '../store';
import type { Transaction } from '../api';

export function useTransactionForm() {
    const route = useRoute();
    const transactionStore = useTransactionStore();

    const loading = ref(true);
    const transaction = ref<Transaction | null>(null);

    async function init() {
        const id = Number(route.params.id);
        if (!id) return;

        try {
            await transactionStore.fetchTransaction(id);
            transaction.value = transactionStore.currentTransaction;
        } catch (e) {
            // Error handled in store
        } finally {
            loading.value = false;
        }
    }

    function formatAmount(item: Transaction) {
        if (!item) return '';
        const symbol = item.to_wallet?.currency?.symbol || item.from_wallet?.currency?.symbol || '$';
        return `${symbol}${Number(item.amount).toLocaleString()}`;
    }

    function getWalletName(item: Transaction, side: 'to' | 'from' = 'to') {
        const wallet = side === 'to' ? item.to_wallet : item.from_wallet;

        if (wallet?.is_external && wallet.address) {
            if (wallet.address.length > 13) {
                return `${wallet.address.substring(0, 8)}...${wallet.address.substring(wallet.address.length - 6)}`;
            }
            return wallet.address;
        }
        return wallet?.name || (side === 'from' ? 'System Deposit' : 'Unknown');
    }

    return {
        loading,
        transaction,
        init,
        formatAmount,
        getWalletName
    };
}
