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
            // Using generic ApiResponse, data is already typed
            const data = response.data as any; // Still need cast until api.ts is updated, but clearer intent
            wallets.value = Array.isArray(data) ? data : (data.data || []);
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

    async function createWallet(payload: CreateWalletPayload) {
        loading.value = true;
        try {
            await walletApi.createWallet(payload);
            await fetchWallets(); // Refresh list
        } finally {
            loading.value = false;
        }
    }

    async function assignUsers(walletId: number, userIds: number[]) {
        loading.value = true;
        try {
            await walletApi.assignUsers(walletId, userIds);
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
