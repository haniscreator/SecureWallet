import apiClient from '@/shared/http/client';

export interface Setting {
    id: number;
    key: string;
    value: string;
    description?: string;
    updated_at: string;
}

export interface UpdateSettingPayload {
    settings: {
        key: string;
        value: string;
    }[];
}

export const settingApi = {
    getSettings() {
        return apiClient.get<Setting[]>('/settings');
    },
    updateSettings(payload: UpdateSettingPayload) {
        return apiClient.post<{ message: string }>('/settings', payload);
    }
};
