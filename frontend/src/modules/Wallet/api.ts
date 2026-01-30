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
    users_count?: number;
    users?: { id: number; name: string }[];
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
    status?: boolean | number;
}

export interface UpdateWalletPayload {
    name?: string;
    status?: boolean | number;
}

export const walletApi = {
    getWallets(params?: { name?: string; currency_id?: number | null; status?: boolean | number | null }) {
        return apiClient.get<Wallet[]>('/wallets', { params });
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
    getTransactions(walletId: number, data?: { type?: string; from_date?: string; reference?: string }) {
        return apiClient.post<Transaction[]>(`/wallets/${walletId}/transactions/search`, data);
    },
    assignUsers(walletId: number, userIds: number[]) {
        return apiClient.post(`/wallets/${walletId}/users`, { user_ids: userIds });
    }
};
