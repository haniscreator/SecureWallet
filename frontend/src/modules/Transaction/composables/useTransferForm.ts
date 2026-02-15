import { ref, reactive, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { walletApi } from '@/modules/Wallet/api';
import { transactionApi } from '@/modules/Transaction/api';
import { useNotificationStore } from '@/shared/stores/notification';

export function useTransferForm() {
    const router = useRouter();
    const route = useRoute();
    const notificationStore = useNotificationStore();

    // --- State ---
    const validDetails = ref(false);
    const submitting = ref(false);
    const loadingSource = ref(false);
    const loadingTargets = ref(false);
    const error = ref('');
    const wallets = ref<any[]>([]);
    const allTargets = ref<any[]>([]);
    const addressValidationResult = ref<{ valid: boolean; exists: boolean; message: string } | null>(null);

    const formData = reactive({
        source_wallet_id: null as number | null,
        type: null as 'internal' | 'external' | null,
        to_wallet_id: null as number | null,
        to_address: '',
        amount: null as number | null,
        description: '',
    });

    // --- Computed ---
    const selectedSourceWallet = computed(() => {
        return wallets.value.find(w => w.id === formData.source_wallet_id);
    });

    const internalTargetWallets = computed(() => {
        if (!formData.source_wallet_id || !selectedSourceWallet.value) return [];
        return allTargets.value.filter(w => w.id !== formData.source_wallet_id);
    });

    // --- Validation Logic ---
    const validateInternalTarget = (value: any) => {
        if (!value) return 'Destination wallet is required';
        const target = allTargets.value.find(w => w.id === value);
        if (!target) return true;

        if (target.currency_id !== selectedSourceWallet.value?.currency_id) {
            return `Currency mismatch. Destination wallet is ${target.currency?.code}, Source is ${selectedSourceWallet.value?.currency?.code}.`;
        }

        if (!target.status) {
            return 'Destination wallet is inactive/frozen and cannot receive funds.';
        }

        return true;
    };

    const amountRules = [
        (v: number) => !!v || 'Amount is required',
        (v: number) => v > 0 || 'Amount must be greater than 0',
        (v: number) => {
            if (!formData.source_wallet_id) return true;
            if (selectedSourceWallet.value) {
                // Check available balance (including pending)
                const available = parseFloat(selectedSourceWallet.value.available_balance || selectedSourceWallet.value.balance);
                if (v > available) {
                    return `Insufficient funds. Available: ${Number(available).toLocaleString()}`;
                }
            }
            return true;
        }
    ];

    // --- Actions ---
    const fetchUserWallets = async () => {
        loadingSource.value = true;
        try {
            const response = await walletApi.getWallets();
            const data = (response as any).data || response;
            wallets.value = Array.isArray(data) ? data : (data.data || []);
        } catch (e) {
            error.value = 'Failed to load source wallet info.';
        } finally {
            loadingSource.value = false;
        }
    };

    const fetchTransferTargets = async () => {
        loadingTargets.value = true;
        try {
            const response = await walletApi.getTransferTargets();
            const data = (response as any).data || response;
            allTargets.value = Array.isArray(data) ? data : (data.data || []);
        } catch (e) {
            console.error('Failed to load transfer targets', e);
        } finally {
            loadingTargets.value = false;
        }
    }

    const validateExternalAddress = async () => {
        if (!formData.to_address || formData.type !== 'external') return;

        // Reset result
        addressValidationResult.value = null;

        try {
            const response = await walletApi.validateAddress(
                formData.to_address,
                selectedSourceWallet.value?.currency_id
            );
            const data = (response as any).data || response;
            addressValidationResult.value = data.data || data;
        } catch (e) {
            console.error('Address validation failed', e);
        }
    };

    const submitTransfer = async () => {
        if (formData.amount && formData.amount <= 0) return;
        if (formData.type === 'internal' && !formData.to_wallet_id) return;
        if (formData.type === 'external' && !formData.to_address) return;

        submitting.value = true;
        error.value = '';

        try {
            await transactionApi.initiateTransfer({
                source_wallet_id: formData.source_wallet_id!,
                type: formData.type!,
                to_wallet_id: formData.type === 'internal' ? formData.to_wallet_id : null,
                to_address: formData.type === 'external' ? formData.to_address : null,
                amount: formData.amount!,
                description: formData.description,
            });

            notificationStore.success('Transfer initiated successfully!');
            router.push('/wallets');
        } catch (e: any) {
            error.value = e.response?.data?.message || 'An error occurred during transfer.';
        } finally {
            submitting.value = false;
        }
    };

    // --- Init Function ---
    const init = async () => {
        await fetchUserWallets();
        if (route.query.from) {
            const fromId = Number(route.query.from);
            const exists = wallets.value.find(w => w.id === fromId);
            if (exists) formData.source_wallet_id = fromId;
        }
        await fetchTransferTargets();
    };

    return {
        // State
        formData, validDetails, submitting, loadingSource, loadingTargets, error,
        wallets, allTargets, addressValidationResult,
        // Computed
        selectedSourceWallet, internalTargetWallets,
        // Methods
        validateInternalTarget, amountRules, validateExternalAddress, submitTransfer, init
    };
}
