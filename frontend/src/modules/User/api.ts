import apiClient from '@/shared/http/client';

export interface User {
    id: number;
    name: string;
    email: string;
    role: 'admin' | 'user';
    created_at?: string;
}

export interface CreateMemberPayload {
    name: string;
    email: string;
    password?: string;
    role: 'admin' | 'user';
}

export interface UpdateMemberPayload {
    name?: string;
    role?: 'admin' | 'user';
}

export const userApi = {
    getCurrentUser() {
        return apiClient.get<User>('/user');
    },
    getMembers() {
        return apiClient.get<User[]>('/members');
    },
    createMember(payload: CreateMemberPayload) {
        return apiClient.post<{ member: User; message: string }>('/members', payload);
    },
    updateMember(id: number, payload: UpdateMemberPayload) {
        return apiClient.put<{ member: User; message: string }>(`/members/${id}`, payload);
    },
    deleteMember(id: number) {
        return apiClient.delete(`/members/${id}`);
    }
};
