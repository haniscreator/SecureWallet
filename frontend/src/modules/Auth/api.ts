import apiClient from '@/shared/http/client';

export interface LoginPayload {
    email: string;
    password?: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
    role: string;
    company_name?: string;
}

export interface LoginResponse {
    token: string;
    user: User;
}

export const authApi = {
    login(payload: LoginPayload) {
        return apiClient.post<{ token: string; user: User }>('/login', payload);
    },
    logout() {
        return apiClient.post('/logout');
    },
    getUser() {
        return apiClient.get<User>('/user');
    }
};
