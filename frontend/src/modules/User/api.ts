import apiClient from '@/shared/http/client';

export interface CreateMemberPayload {
    name: string;
    email: string;
    password?: string;
    role?: string;
    status?: boolean;
    wallet_ids?: number[];
}

export interface UpdateMemberPayload {
    name?: string;
    role?: string;
    status?: boolean;
    wallet_ids?: number[];
}

export interface User {
    id: number;
    name: string;
    email: string;
    role: 'admin' | 'user';
    created_at?: string;
    company_name?: string;
    wallet_access?: string[]; // Names for display
    wallet_ids?: number[];    // IDs for edit form
    status?: boolean;
}

// ... existing interfaces ...

export const userApi = {
    getCurrentUser() {
        return apiClient.get<User>('/user');
    },
    getMembers() {
        return apiClient.get<User[]>('/members');
    },
    getMember(id: number) {
        return apiClient.get<{ user: User }>(`/members/${id}`); // Backend wraps in 'user' key? Check Controller.
    },
    createMember(payload: CreateMemberPayload) {
        return apiClient.post<{ member: User; message: string }>('/members', payload);
    },
    updateMember(id: number, payload: UpdateMemberPayload) {
        return apiClient.put<{ user: User; message: string }>(`/members/${id}`, payload);
    },
    deleteMember(id: number) {
        return apiClient.delete(`/members/${id}`);
    }
};
