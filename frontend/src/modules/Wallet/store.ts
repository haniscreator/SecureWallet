import { defineStore } from 'pinia';
import { ref } from 'vue';
import { walletApi, type Wallet, type Transaction, type CreateWalletPayload } from './api';

export const useWalletStore = defineStore('wallet', () => {
    const wallets = ref<Wallet[]>([]);
    const currentWallet = ref<Wallet | null>(null);
    const transactions = ref<Transaction[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    async function fetchWallets() {
        loading.value = true;
        try {
            const response = await walletApi.getWallets();
            // Handle Laravel Resource response wrapping
            const data = response.data as any;
            wallets.value = Array.isArray(data) ? data : (data.data || []);
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch wallets';
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
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch wallet details';
        } finally {
            loading.value = false;
        }
    }

    async function createWallet(payload: CreateWalletPayload) {
        loading.value = true;
        try {
            await walletApi.createWallet(payload);
            await fetchWallets(); // Refresh list
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to create wallet';
            throw err; // Re-throw to handle in UI
        } finally {
            loading.value = false;
        }
    }

    async function assignUsers(walletId: number, userIds: number[]) {
        loading.value = true;
        try {
            await walletApi.assignUsers(walletId, userIds);
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to assign users';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    return {
        wallets,
        currentWallet,
        transactions,
        loading,
        error,
        fetchWallets,
        fetchWalletDetails,
        createWallet,
        assignUsers
    };
});
