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

    async function fetchWallets(params?: any) {
        loading.value = true;
        try {
            const response = await walletApi.getWallets(params);
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
        if (wallets.value.length === 0) return;

        // Fetch currencies to map IDs if needed
        let currencyMap: Record<number, any> = {};
        try {
            const curResponse = await import('@/modules/Currency/api').then(m => m.currencyApi.getCurrencies());
            const curData = curResponse.data as any;
            const currencies = Array.isArray(curData) ? curData : (curData.data || []);
            currencies.forEach((c: any) => currencyMap[c.id] = c);
        } catch (e) {
            console.error('Failed to fetch currencies', e);
        }

        const allTxs: (Transaction & { wallet_name: string })[] = [];

        // Use Promise.all for parallel fetching
        const promises = wallets.value.map(async (wallet) => {
            try {
                const response = await walletApi.getTransactions(wallet.id);
                const data = response.data as any;
                const txs = Array.isArray(data) ? data : (data.data || []);

                // Calculate balance from transactions if not present
                // Assuming initial balance is 0 or captured in transactions
                let calculatedBalance = 0;
                txs.forEach((tx: Transaction) => {
                    if (tx.type === 'credit') calculatedBalance += Number(tx.amount);
                    if (tx.type === 'debit') calculatedBalance -= Number(tx.amount);
                });

                // Update wallet with calculated balance and currency info
                // We mutate the wallet in the list directly to update the UI
                wallet.balance = calculatedBalance;

                if (!wallet.currency && wallet.currency_id && currencyMap[wallet.currency_id]) {
                    wallet.currency = currencyMap[wallet.currency_id];
                }

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

        // Deduplicate by ID (keep first occurrence)
        const uniqueTxs = Array.from(new Map(allTxs.map(tx => [tx.id, tx])).values());

        // Sort by date desc
        recentGlobalTransactions.value = uniqueTxs.sort((a, b) =>
            new Date(b.created_at).getTime() - new Date(a.created_at).getTime()
        );
    }

    async function createWallet(payload: CreateWalletPayload) {
        const notificationStore = useNotificationStore();
        loading.value = true;
        try {
            const response = await walletApi.createWallet(payload);
            await fetchWallets(); // Refresh list
            notificationStore.success('Wallet created successfully');
            return response.data;
        } catch (e: any) {
            const msg = e.response?.data?.message || 'Failed to create wallet';
            notificationStore.error(msg);
            throw e;
        } finally {
            loading.value = false;
        }
    }

    async function updateWallet(id: number, payload: any) {
        const notificationStore = useNotificationStore();
        loading.value = true;
        try {
            await walletApi.updateWallet(id, payload);
            await fetchWallets(); // Refresh list
            notificationStore.success('Wallet updated successfully');
        } catch (e: any) {
            const msg = e.response?.data?.message || 'Failed to update wallet';
            notificationStore.error(msg);
            throw e;
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
    // Getters
    const totalBalanceByCurrency = computed(() => {
        const totals: Record<string, { amount: number, symbol: string }> = {};

        wallets.value.forEach(w => {
            // Use code from currency object or fallback
            // Note: Map ID to code manually if strictly needed and object is still missing
            const currencyCode = w.currency?.code || (w.currency_id === 2 ? 'EUR' : 'USD');
            const currencySymbol = w.currency?.symbol || '$'; // Fallback symbol

            if (!totals[currencyCode]) {
                totals[currencyCode] = { amount: 0, symbol: currencySymbol };
            }

            // If symbol was fallback, verify if we can update it now (in case mixed wallets)
            if (totals[currencyCode].symbol === '$' && currencySymbol !== '$') {
                totals[currencyCode].symbol = currencySymbol;
            }

            const val = w.balance !== undefined && w.balance !== null ? w.balance : 0;
            totals[currencyCode].amount += Number(val);
        });

        return totals;
    });

    const recentWallets = computed(() => {
        // Return top 3, sorted by created_at desc
        return [...wallets.value]
            .sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
            .slice(0, 3);
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
        updateWallet,
        assignUsers,
        fetchAllTransactions,
        totalBalanceByCurrency,
        recentWallets
    };
});
