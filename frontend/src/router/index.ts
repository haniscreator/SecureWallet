import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/modules/Auth/store';

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: () => import('@/modules/Auth/views/Login.vue'),
        meta: { guest: true }
    },
    {
        path: '/',
        component: () => import('@/shared/layouts/DashboardLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                redirect: '/dashboard'
            },
            {
                path: 'dashboard',
                name: 'Dashboard',
                component: () => import('@/modules/Wallet/views/Dashboard.vue'),
            },
            {
                path: 'wallets',
                name: 'Wallets',
                // Redirect to dashboard for now as it lists wallets, or maybe create a dedicated list view later
                component: () => import('@/modules/Wallet/views/WalletList.vue'),
            },
            {
                path: 'wallets/create',
                name: 'WalletCreate',
                component: () => import('@/modules/Wallet/views/WalletForm.vue'),
                meta: { roles: ['admin'] }
            },
            {
                path: 'wallets/:id/edit',
                name: 'WalletEdit',
                component: () => import('@/modules/Wallet/views/WalletForm.vue'),
                meta: { roles: ['admin'] }
            },
            {
                path: 'wallet/:id',
                name: 'WalletDetails',
                component: () => import('@/modules/Wallet/views/WalletDetail.vue'),
                props: (route: any) => ({ id: Number(route.params.id) }),
            },
            {
                path: 'members',
                name: 'Members',
                component: () => import('@/modules/User/views/MemberList.vue'),
            },
            {
                path: 'members/create',
                name: 'MemberCreate',
                component: () => import('@/modules/User/views/MemberForm.vue'),
                meta: { roles: ['admin'] }
            },
            {
                path: 'members/:id/edit',
                name: 'MemberEdit',
                component: () => import('@/modules/User/views/MemberForm.vue'),
                meta: { roles: ['admin'] }
            },
            {
                path: 'currencies',
                name: 'Currencies',
                component: () => import('@/modules/Currency/views/CurrencyList.vue'),
            },
            {
                path: 'currencies/create',
                name: 'CurrencyCreate',
                component: () => import('@/modules/Currency/views/CurrencyForm.vue'),
                meta: { roles: ['admin'] }
            },
            {
                path: 'currencies/:id/edit',
                name: 'CurrencyEdit',
                component: () => import('@/modules/Currency/views/CurrencyForm.vue'),
                meta: { roles: ['admin'] }
            },
            {
                path: 'transactions',
                name: 'Transactions',
                component: () => import('@/modules/Transaction/views/TransactionList.vue'),
            },
            {
                path: 'transfer',
                name: 'Transfer',
                component: () => import('@/modules/Transaction/views/TransferForm.vue'),
            },
            {
                path: 'transactions/:id',
                name: 'TransactionDetails',
                component: () => import('@/modules/Transaction/views/TransactionForm.vue'),
            },
            {
                path: 'approvals',
                name: 'PendingApprovals',
                component: () => import('@/modules/Transaction/views/PendingApprovals.vue'),
                meta: { roles: ['admin', 'manager'] }
            },
            {
                path: 'approvals/:id',
                name: 'ApprovalDetail',
                component: () => import('@/modules/Transaction/views/ApprovalDetail.vue'),
                meta: { roles: ['admin', 'manager'] }
            },
            {
                path: 'settings',
                name: 'Settings',
                component: () => import('@/modules/Setting/views/SettingForm.vue'),
                meta: { requiresAuth: true, roles: ['admin'] } // Only admin
            },
        ]
    },
    {
        path: '/changelog',
        name: 'Changelog',
        component: () => import('@/modules/Changelog/views/Changelog.vue'),
    },
    {
        path: '/:pathMatch(.*)*',
        redirect: '/dashboard'
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, _from, next) => {
    const token = localStorage.getItem('token');
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
    const isGuest = to.matched.some(record => record.meta.guest);
    const requiredRoles = to.meta.requiresRole ? [to.meta.requiresRole as string] : (to.meta.roles as string[] | undefined);

    if (requiresAuth && !token) {
        next('/login');
        return;
    }

    if (isGuest && token) {
        next('/dashboard');
        return;
    }

    if (token) {
        // Ensure user is loaded for role checks
        const authStore = useAuthStore();
        if (!authStore.user) {
            try {
                await authStore.fetchUser();
            } catch (error) {
                // If fetch fails (token invalid), redirect to login
                next('/login');
                return;
            }
        }

        // Check Roles
        if (requiredRoles && requiredRoles.length > 0) {
            const userRole = authStore.user?.role;
            if (!userRole || !requiredRoles.includes(userRole)) {
                // Unauthorized - redirect to dashboard
                next('/dashboard');
                return;
            }
        }
    }

    next();
});

export default router;
