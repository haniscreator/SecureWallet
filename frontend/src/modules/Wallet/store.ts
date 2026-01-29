import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { walletApi, type Wallet, type Transaction, type CreateWalletPayload } from './api';

import { useNotificationStore } from '@/shared/stores/notification';

export const useWalletStore = defineStore('wallet', () => {
    const wallets = ref<Wallet[]>([]);
    const currentWallet = ref<Wallet | null>(null);
    const transactions = ref<Transaction[]>([]); // Used in details view
    const recentGlobalTransactions = ref<(Transaction & { wallet_name: string })[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    async function fetchWallets() {
        loading.value = true;
        try {
            const response = await walletApi.getWallets();
            const data = response.data as any;
            wallets.value = Array.isArray(data) ? data : (data.data || []);

            // After fetching wallets, let's fetch transactions for them to populate the dashboard
            // In a real app, we might want to do this lazily or have a separate dashboard init
            await fetchAllTransactions();
        } finally {
            loading.value = false;
        }
    }

    async function fetchWalletDetails(id: number) {
        loading.value = true;
        try {
            const response = await walletApi.getWallet(id);
            const data = response.data as any;
            currentWallet.value = data.data ? data.data : data;

            // Also fetch transactions for this wallet
            const txResponse = await walletApi.getTransactions(id);
            const txData = txResponse.data as any;
            transactions.value = Array.isArray(txData) ? txData : (txData.data || []);
        } finally {
            loading.value = false;
        }
    }

    // New Action: Fetch transactions for ALL wallets and merge them
    async function fetchAllTransactions() {
        // Avoid infinite loading loop if called from fetchWallets, so we don't set global loading default
        // or we manage it carefully. 
        // Let's iterate all wallets.
        if (wallets.value.length === 0) return;

        const allTxs: (Transaction & { wallet_name: string })[] = [];

        // Use Promise.all for parallel fetching
        const promises = wallets.value.map(async (wallet) => {
            try {
                const response = await walletApi.getTransactions(wallet.id);
                const data = response.data as any;
                const txs = Array.isArray(data) ? data : (data.data || []);

                // Tag with wallet name
                return txs.map((tx: Transaction) => ({
                    ...tx,
                    wallet_name: wallet.name
                }));
            } catch (e) {
                console.error(`Failed to fetch transactions for wallet ${wallet.id}`, e);
                return [];
            }
        });

        const results = await Promise.all(promises);
        results.forEach(txList => allTxs.push(...txList));

        // Sort by date desc
        recentGlobalTransactions.value = allTxs.sort((a, b) =>
            new Date(b.created_at).getTime() - new Date(a.created_at).getTime()
        );
    }

    async function createWallet(payload: CreateWalletPayload) {
        const notificationStore = useNotificationStore();
        loading.value = true;
        try {
            await walletApi.createWallet(payload);
            await fetchWallets(); // Refresh list
            notificationStore.success('Wallet created successfully');
        } finally {
            loading.value = false;
        }
    }

    async function assignUsers(walletId: number, userIds: number[]) {
        const notificationStore = useNotificationStore();
        loading.value = true;
        try {
            await walletApi.assignUsers(walletId, userIds);
            notificationStore.success('Users assigned successfully');
        } finally {
            loading.value = false;
        }
    }

    // Getters
    const totalBalanceByCurrency = computed(() => {
        const totals: Record<string, number> = {};

        wallets.value.forEach(w => {
            // Default to USD if no currency code, though API usually provides it
            const currency = w.currency?.code || 'USD';
            if (!totals[currency]) totals[currency] = 0;
            // Handle potential string or number, default to 0 if missing
            const val = w.balance !== undefined && w.balance !== null ? w.balance : 0;
            totals[currency] += Number(val);
        });

        return totals;
    });

    const recentWallets = computed(() => {
        // Return top 3, assuming list is somewhat ordered or just take first 3
        return wallets.value.slice(0, 3);
    });

    return {
        wallets,
        currentWallet,
        transactions,
        recentGlobalTransactions, // Exposed for dashboard
        loading,
        error,
        fetchWallets,
        fetchWalletDetails,
        createWallet,
        assignUsers,
        fetchAllTransactions,
        totalBalanceByCurrency,
        recentWallets
    };
});
