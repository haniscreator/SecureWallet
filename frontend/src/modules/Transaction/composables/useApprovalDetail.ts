import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useTransactionStore } from '../store';
import { transactionApi } from '../api';
import type { Transaction } from '../api';
import { useNotificationStore } from '@/shared/stores/notification';

export function useApprovalDetail() {
    const route = useRoute();
    const router = useRouter();
    const transactionStore = useTransactionStore();
    const notification = useNotificationStore();

    const loading = ref(true);
    const transaction = ref<Transaction | null>(null);
    const processing = ref(false);
    const rejectDialog = ref(false);
    const rejectionReason = ref('');

    async function init() {
        const id = Number(route.params.id);
        if (!id) return;

        try {
            await transactionStore.fetchTransaction(id);
            transaction.value = transactionStore.currentTransaction;
        } catch (e) {
            notification.error('Failed to load transaction details');
        } finally {
            loading.value = false;
        }
    }

    function formatAmount(item: Transaction) {
        if (!item) return '';
        const symbol = item.to_wallet?.currency?.symbol || item.from_wallet?.currency?.symbol || '$';
        return `${item.type === 'debit' ? '-' : '+'}${symbol}${Number(item.amount).toLocaleString()}`;
    }

    function getWalletName(item: Transaction) {
        const wallet = item.to_wallet;
        if (wallet?.is_external && wallet.address) {
            if (wallet.address.length > 13) {
                return `${wallet.address.substring(0, 8)}...${wallet.address.substring(wallet.address.length - 6)}`;
            }
            return wallet.address;
        }
        return item.to_wallet?.name || item.wallet?.name || 'Unknown Wallet';
    }

    const confirmDialog = ref({
        isOpen: false,
        title: 'Confirm Approval',
        message: 'Are you sure you want to approve this transfer?',
    });

    const openConfirmDialog = () => {
        confirmDialog.value.isOpen = true;
    };

    const handleConfirmApprove = async () => {
        if (!transaction.value) return;

        processing.value = true;
        try {
            await transactionApi.approveTransfer(transaction.value.id);
            notification.success('Transfer approved successfully');
            router.push('/approvals');
        } catch (e: any) {
            notification.error(e.response?.data?.message || 'Failed to approve');
        } finally {
            processing.value = false;
            confirmDialog.value.isOpen = false;
        }
    };

    const approve = () => {
        if (!transaction.value) return;
        openConfirmDialog();
    };

    const openRejectDialog = () => {
        rejectionReason.value = '';
        rejectDialog.value = true;
    };

    const confirmReject = async () => {
        if (!transaction.value) return;

        processing.value = true;
        try {
            await transactionApi.rejectTransfer(transaction.value.id, rejectionReason.value);
            notification.notify('Transfer rejected', 'info');
            rejectDialog.value = false;
            router.push('/approvals');
        } catch (e: any) {
            notification.error(e.response?.data?.message || 'Failed to reject');
        } finally {
            processing.value = false;
        }
    };

    return {
        loading,
        transaction,
        processing,
        rejectDialog,
        rejectionReason,
        init,
        formatAmount,
        getWalletName,
        approve,
        openRejectDialog,
        confirmReject,
        confirmDialog,
        openConfirmDialog,
        handleConfirmApprove
    };
}
