import apiClient from '@/shared/http/client';

export interface Currency {
    id: number;
    code: string;
    name: string;
    symbol: string;
    status: boolean;
}

export interface CreateCurrencyPayload {
    name: string;
    code: string;
    symbol: string;
    status: boolean;
}

export interface UpdateCurrencyPayload extends Partial<CreateCurrencyPayload> { }

export const currencyApi = {
    getCurrencies() {
        return apiClient.get<Currency[]>('/currencies');
    },
    getCurrency(id: number) {
        return apiClient.get<{ data: Currency }>(`/currencies/${id}`);
    },
    createCurrency(payload: CreateCurrencyPayload) {
        return apiClient.post<{ currency: Currency; message: string }>('/currencies', payload);
    },
    updateCurrency(id: number, payload: UpdateCurrencyPayload) {
        return apiClient.put<{ currency: Currency; message: string }>(`/currencies/${id}`, payload);
    },
    deleteCurrency(id: number) {
        return apiClient.delete<{ message: string }>(`/currencies/${id}`);
    }
};
