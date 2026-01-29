import { defineStore } from 'pinia';
import { ref } from 'vue';
import { userApi, type User, type CreateMemberPayload } from './api';

import { useNotificationStore } from '@/shared/stores/notification';

export const useUserStore = defineStore('user', () => {
    const currentUser = ref<User | null>(null);
    const members = ref<User[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    async function fetchCurrentUser() {
        try {
            const response = await userApi.getCurrentUser();
            const data = response.data as any; // Handle potential wrapping
            currentUser.value = data.data ? data.data : data;
        } catch (err) {
            console.error('Failed to fetch current user', err);
        }
    }

    async function fetchMembers() {
        loading.value = true;
        try {
            const response = await userApi.getMembers();
            const data = response.data as any;
            members.value = Array.isArray(data) ? data : (data.data || []);
        } finally {
            loading.value = false;
        }
    }

    async function createMember(payload: CreateMemberPayload) {
        const notificationStore = useNotificationStore();
        loading.value = true;
        try {
            await userApi.createMember(payload);
            await fetchMembers(); // Refresh list
            notificationStore.success('Member added successfully');
        } finally {
            loading.value = false;
        }
    }

    async function deleteMember(id: number) {
        const notificationStore = useNotificationStore();
        loading.value = true;
        try {
            await userApi.deleteMember(id);
            await fetchMembers();
            notificationStore.success('Member deleted successfully');
        } finally {
            loading.value = false;
        }
    }

    return {
        currentUser,
        members,
        loading,
        error,
        fetchCurrentUser,
        fetchMembers,
        createMember,
        deleteMember
    };
});
