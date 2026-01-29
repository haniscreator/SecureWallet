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
                path: 'wallets/:id',
                name: 'WalletDetails',
                component: () => import('@/modules/Wallet/views/WalletDetail.vue'),
            },
            // Placeholders for future modules
            {
                path: 'wallets',
                name: 'Wallets',
                // component: () => import('@/modules/Wallet/views/WalletList.vue')
                component: () => import('@/modules/Wallet/views/Dashboard.vue') // Temp
            },
            {
                path: 'members',
                name: 'Members',
                component: () => import('@/modules/User/views/Team.vue')
            }
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
