import apiClient from '@/shared/http/client';

export interface Transaction {
    id: number;
    amount: number;
    type: 'credit' | 'debit';
    reference: string;
    created_at: string;
    wallet?: {
        name: string;
        currency?: {
            symbol: string;
        }
    };
    to_wallet?: {
        name: string;
        currency?: {
            symbol: string;
        }
    };
    from_wallet?: {
        name: string;
        currency?: {
            symbol: string;
        }
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
}

export const transactionApi = {
    getTransactions(data?: TransactionFilters) {
        return apiClient.post<{ data: Transaction[]; meta: any; links: any }>('/transactions/search', data);
    },
    getTransaction(id: number) {
        return apiClient.get<{ data: Transaction }>(`/transactions/${id}`);
    }
};
