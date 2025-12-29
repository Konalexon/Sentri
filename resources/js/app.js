import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import { useAuthStore } from './stores/useAuthStore'
import '../css/app.css'

// Routes
const routes = [
    {
        path: '/',
        redirect: '/dashboard',
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('./pages/Login.vue'),
        meta: { guest: true },
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('./pages/Register.vue'),
        meta: { guest: true },
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('./pages/Dashboard.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/tickets',
        name: 'tickets',
        component: () => import('./pages/Tickets.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/tickets/:id',
        name: 'ticket',
        component: () => import('./pages/Dashboard.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/inbox',
        name: 'inbox',
        component: () => import('./pages/Inbox.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/settings',
        name: 'settings',
        component: () => import('./pages/Settings.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/account',
        name: 'account',
        component: () => import('./pages/Account.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/team',
        name: 'team',
        component: () => import('./pages/Team.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/analytics',
        name: 'analytics',
        component: () => import('./pages/Analytics.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/knowledge-base',
        name: 'knowledge-base',
        component: () => import('./pages/KnowledgeBase.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/integrations',
        name: 'integrations',
        component: () => import('./pages/Integrations.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/activity-log',
        name: 'activity-log',
        component: () => import('./pages/ActivityLog.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/admin',
        name: 'admin',
        component: () => import('./pages/Admin.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

// Navigation guards
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore()

    // Initialize auth on first load
    if (!authStore.user && authStore.token) {
        try {
            await authStore.fetchUser()
        } catch (err) {
            // Token invalid
        }
    }

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next({ name: 'login', query: { redirect: to.fullPath } })
    } else if (to.meta.guest && authStore.isAuthenticated) {
        next({ name: 'dashboard' })
    } else {
        next()
    }
})

// Create app
const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

// Initialize auth
const authStore = useAuthStore(pinia)
authStore.initAuth()

app.mount('#app')
