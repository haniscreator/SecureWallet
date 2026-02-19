import apiClient from '@/shared/http/client';

export interface Transaction {
    id: number;
    amount: number;
    type: 'credit' | 'debit' | 'transfer';
    reference: string;
    created_at: string;
    wallet?: {
        name: string;
        address?: string;
        is_external?: boolean;
        currency?: {
            symbol: string;
        }
    };
    to_wallet?: {
        name: string;
        address?: string;
        is_external?: boolean;
        currency?: {
            symbol: string;
        }
    };
    from_wallet?: {
        name: string;
        address?: string;
        is_external?: boolean;
        currency?: {
            symbol: string;
        }
    };
    user?: {
        id: number;
        name: string;
    };
    status?: {
        code: string;
        name: string;
    };
}

export interface TransactionFilters {
    type?: 'credit' | 'debit' | null;
    from_date?: string | null;
    to_date?: string | null;
    reference?: string | null;
    page?: number;
    sort_by?: string;
    sort_dir?: 'asc' | 'desc';
    per_page?: number;
    status_id?: number;
}

export const transactionApi = {
    getTransactions(data?: TransactionFilters) {
        return apiClient.post<{ data: Transaction[]; meta: any; links: any }>('/transactions/search', data);
    },
    getTransaction(id: number) {
        return apiClient.get<{ data: Transaction }>(`/transactions/${id}`);
    },
    getExternalWallets() {
        return apiClient.get<any[]>('/external-wallets');
    },
    initiateTransfer(data: { source_wallet_id: number; type: 'internal' | 'external'; to_wallet_id?: number | null; to_address?: string | null; amount: number; description?: string }) {
        return apiClient.post<{ message: string; transaction: Transaction }>('/transfers', data);
    },
    approveTransfer(id: number) {
        return apiClient.post<{ message: string; transaction: Transaction }>(`/transfers/${id}/approve`);
    },
    rejectTransfer(id: number, reason: string) {
        return apiClient.post<{ message: string; transaction: Transaction }>(`/transfers/${id}/reject`, { reason });
    },
    cancelTransaction(id: number) {
        return apiClient.post<{ message: string; transaction: Transaction }>(`/transfers/${id}/cancel`);
    }
};
