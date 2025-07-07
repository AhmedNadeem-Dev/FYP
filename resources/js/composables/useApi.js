import { ref, computed } from 'vue';
import axios from 'axios';

// Create axios instance with base configuration
const api = axios.create({
    baseURL: '/api/v1',
    timeout: 10000,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

// Request interceptor to add auth token
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('access_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Response interceptor for error handling
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            // Token expired or invalid
            localStorage.removeItem('access_token');
            localStorage.removeItem('userSession');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

export function useApi() {
    const loading = ref(false);
    const error = ref(null);

    const isLoading = computed(() => loading.value);
    const hasError = computed(() => error.value !== null);

    const clearError = () => {
        error.value = null;
    };

    const handleRequest = async (requestFn) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await requestFn();
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || err.message || 'An error occurred';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Common API methods
    const get = (url, config = {}) => {
        return handleRequest(() => api.get(url, config));
    };

    const post = (url, data = {}, config = {}) => {
        return handleRequest(() => api.post(url, data, config));
    };

    const put = (url, data = {}, config = {}) => {
        return handleRequest(() => api.put(url, data, config));
    };

    const del = (url, config = {}) => {
        return handleRequest(() => api.delete(url, config));
    };

    // Specialized methods
    const uploadFile = (url, formData, onUploadProgress) => {
        return handleRequest(() => api.post(url, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            onUploadProgress,
        }));
    };

    return {
        // State
        loading: isLoading,
        error: computed(() => error.value),
        hasError,

        // Methods
        get,
        post,
        put,
        delete: del,
        uploadFile,
        clearError,

        // Direct access to axios instance
        api,
    };
} 