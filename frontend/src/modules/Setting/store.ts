import { defineStore } from 'pinia';
import { ref } from 'vue';
import { settingApi, type Setting } from './api';
import { useNotificationStore } from '@/shared/stores/notification';

export const useSettingStore = defineStore('setting', () => {
    const settings = ref<Setting[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    async function fetchSettings() {
        loading.value = true;
        try {
            const response = await settingApi.getSettings();
            const data = response.data as any;
            settings.value = Array.isArray(data) ? data : (data.data || []);
        } catch (err) {
            console.error('Failed to fetch settings', err);
            error.value = 'Failed to load settings';
        } finally {
            loading.value = false;
        }
    }

    async function updateSettings(updates: { key: string; value: string }[]) {
        const notificationStore = useNotificationStore();
        loading.value = true;
        try {
            await settingApi.updateSettings({ settings: updates });
            await fetchSettings();
            notificationStore.success('Settings updated successfully');
        } catch (err) {
            console.error('Failed to update settings', err);
            notificationStore.error('Failed to update settings');
        } finally {
            loading.value = false;
        }
    }

    return {
        settings,
        loading,
        error,
        fetchSettings,
        updateSettings
    };
});
