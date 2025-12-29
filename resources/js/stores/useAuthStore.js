import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
    // State
    const user = ref(null)
    const token = ref(localStorage.getItem('token'))
    const loading = ref(false)

    // Getters
    const isAuthenticated = computed(() => !!token.value && !!user.value)
    const isAdmin = computed(() => user.value?.role === 'admin')
    const isAgent = computed(() => user.value?.role === 'agent')
    const isCustomer = computed(() => user.value?.role === 'customer')
    const canManageTickets = computed(() => isAdmin.value || isAgent.value)

    // Actions
    async function login(email, password) {
        loading.value = true

        try {
            const response = await api.post('/login', { email, password })

            token.value = response.data.token
            user.value = response.data.user

            localStorage.setItem('token', token.value)
            api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`

            return response.data
        } finally {
            loading.value = false
        }
    }

    async function register(name, email, password, passwordConfirmation) {
        loading.value = true

        try {
            const response = await api.post('/register', {
                name,
                email,
                password,
                password_confirmation: passwordConfirmation,
            })

            token.value = response.data.token
            user.value = response.data.user

            localStorage.setItem('token', token.value)
            api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`

            return response.data
        } finally {
            loading.value = false
        }
    }

    async function logout() {
        try {
            await api.post('/logout')
        } catch (err) {
            // Ignore errors on logout
        } finally {
            token.value = null
            user.value = null
            localStorage.removeItem('token')
            delete api.defaults.headers.common['Authorization']
        }
    }

    async function fetchUser() {
        if (!token.value) return null

        try {
            api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
            const response = await api.get('/user')
            user.value = response.data
            return user.value
        } catch (err) {
            // Token invalid, logout
            await logout()
            throw err
        }
    }

    function initAuth() {
        if (token.value) {
            api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
        }
    }

    return {
        // State
        user,
        token,
        loading,

        // Getters
        isAuthenticated,
        isAdmin,
        isAgent,
        isCustomer,
        canManageTickets,

        // Actions
        login,
        register,
        logout,
        fetchUser,
        initAuth,
    }
})
