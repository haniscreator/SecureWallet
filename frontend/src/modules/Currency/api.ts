import apiClient from '@/shared/http/client';

export interface Currency {
    id: number;
    code: string;
    name: string;
    symbol: string;
    status: boolean;
}

export const currencyApi = {
    getCurrencies() {
        return apiClient.get<Currency[]>('/currencies');
    }
};
