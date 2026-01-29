import apiClient from '@/shared/http/client';

export interface Wallet {
    id: number;
    name: string;
    balance: number;
    currency_id: number;
    status: boolean;
    created_at: string;
    currency?: {
        code: string;
        symbol: string;
    };
}

export interface Transaction {
    id: number;
    amount: number;
    type: 'credit' | 'debit';
    reference: string;
    created_at: string;
}

export interface CreateWalletPayload {
    name: string;
    currency_id: number;
    initial_balance: number;
}

export interface UpdateWalletPayload {
    name?: string;
    status?: boolean;
}

export const walletApi = {
    getWallets() {
        return apiClient.get<Wallet[]>('/wallets');
    },
    getWallet(id: number) {
        return apiClient.get<Wallet>(`/wallets/${id}`);
    },
    createWallet(payload: CreateWalletPayload) {
        return apiClient.post<{ wallet: Wallet; message: string }>('/wallets', payload);
    },
    updateWallet(id: number, payload: UpdateWalletPayload) {
        return apiClient.put<{ wallet: Wallet; message: string }>(`/wallets/${id}`, payload);
    },
    getTransactions(walletId: number, params?: { type?: string; from_date?: string }) {
        return apiClient.get<Transaction[]>(`/wallets/${walletId}/transactions`, { params });
    },
    assignUsers(walletId: number, userIds: number[]) {
        return apiClient.post(`/wallets/${walletId}/users`, { user_ids: userIds });
    }
};
