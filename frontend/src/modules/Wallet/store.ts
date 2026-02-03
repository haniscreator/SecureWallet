import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { walletApi, type Wallet, type Transaction, type CreateWalletPayload } from './api';

import { useNotificationStore } from '@/shared/stores/notification';

export const useWalletStore = defineStore('wallet', () => {
    const wallets = ref<Wallet[]>([]);
    const currentWallet = ref<Wallet | null>(null);
    const transactions = ref<Transaction[]>([]); // Used in details view

    const dashboardWallets = ref<Wallet[]>([]); // New state for dashboard widget
    const dashboardWidgetLoading = ref(false);

    // Dashboard Pagination State
    const dashboardTransactions = ref<any[]>([]);
    const dashboardTotalItems = ref(0);
    const dashboardPage = ref(1);
    const dashboardItemsPerPage = ref(10);
    const dashboardLoading = ref(false);

    // Total Balance State
    const totalBalance = ref<Record<string, { amount: number, symbol: string }>>({});
    const totalBalanceLoading = ref(false);

    const loading = ref(false);
    const error = ref<string | null>(null);

    async function fetchWallets(params?: any) {
        loading.value = true;
        try {
            const response = await walletApi.getWallets(params);
            const data = response.data as any;
            const fetchedWallets = Array.isArray(data) ? data : (data.data || []);

            wallets.value = fetchedWallets;
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
            notificationStore.success('Wallet updated successfully.');
        } finally {
            loading.value = false;
        }
    }

    // Getters


    const recentWallets = computed(() => {
        // Return top 3, sorted by created_at desc
        return [...wallets.value]
            .sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
            .slice(0, 3);
    });


    async function fetchDashboardTransactions(page = 1, itemsPerPage = 10) {
        dashboardLoading.value = true;
        dashboardPage.value = page;
        dashboardItemsPerPage.value = itemsPerPage;

        try {
            const response = await walletApi.getGlobalTransactions({
                page,
                per_page: itemsPerPage,
                sort_by: 'created_at',
                sort_dir: 'desc'
            });
            const data = response.data as any; // Cast because backend returns standard paginated structure

            // Backend returns { data: [], links: {}, meta: {} }
            dashboardTransactions.value = data.data || [];
            dashboardTotalItems.value = data.meta?.total || 0;

        } catch (e) {
            console.error('Failed to fetch dashboard transactions', e);
        } finally {
            dashboardLoading.value = false;
        }
    }

    async function fetchDashboardWidget() {
        dashboardWidgetLoading.value = true;
        try {
            const response = await walletApi.getDashboardWidget();
            const data = response.data as any;
            dashboardWallets.value = Array.isArray(data) ? data : (data.data || []);
        } catch (e) {
            console.error('Failed to fetch dashboard widget', e);
        } finally {
            dashboardWidgetLoading.value = false;
        }
    }

    async function fetchTotalBalance() {
        totalBalanceLoading.value = true;
        try {
            const response = await walletApi.getTotalBalanceWidget();
            totalBalance.value = response.data as any; // Backend returns Record<string, {amount, symbol}>
        } catch (e) {
            console.error('Failed to fetch total balance', e);
        } finally {
            totalBalanceLoading.value = false;
        }
    }

    return {
        wallets,
        currentWallet,
        transactions,
        // recentGlobalTransactions, // Exposed for dashboard (legacy/fallback)
        loading,
        error,
        fetchWallets,
        fetchWalletDetails,
        createWallet,
        updateWallet,
        assignUsers,


        recentWallets,

        // Dashboard Pagination
        dashboardTransactions,
        dashboardTotalItems,
        dashboardPage,
        dashboardItemsPerPage,
        dashboardLoading,
        fetchDashboardTransactions,
        dashboardWallets,
        dashboardWidgetLoading,
        fetchDashboardWidget,
        totalBalance,
        totalBalanceLoading,
        fetchTotalBalance
    };
});
