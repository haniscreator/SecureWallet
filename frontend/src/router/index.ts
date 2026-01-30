import { createRouter, createWebHistory } from 'vue-router';

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
            },
            {
                path: 'wallets/:id/edit',
                name: 'WalletEdit',
                component: () => import('@/modules/Wallet/views/WalletForm.vue'),
            },
            {
                path: 'wallet/:id',
                name: 'WalletDetails',
                component: () => import('@/modules/Wallet/views/WalletDetail.vue'),
                props: (route) => ({ id: Number(route.params.id) }),
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
            },
            {
                path: 'members/:id/edit',
                name: 'MemberEdit',
                component: () => import('@/modules/User/views/MemberForm.vue'),
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
            },
            {
                path: 'currencies/:id/edit',
                name: 'CurrencyEdit',
                component: () => import('@/modules/Currency/views/CurrencyForm.vue'),
            },
            {
                path: 'transactions',
                name: 'Transactions',
                component: () => import('@/modules/Transaction/views/TransactionList.vue'),
            },
        ]
    },
    {
        path: '/:pathMatch(.*)*',
        redirect: '/'
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
    const isGuest = to.matched.some(record => record.meta.guest);

    if (requiresAuth && !token) {
        next('/login');
    } else if (isGuest && token) {
        next('/dashboard');
    } else {
        next();
    }
});

export default router;
