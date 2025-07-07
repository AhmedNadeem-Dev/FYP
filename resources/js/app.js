// main.js or main.ts
import 'bootstrap/dist/css/bootstrap.min.css';

import './bootstrap';
import { createApp } from 'vue';
import App from './app.vue';
import router from './router';
import '../css/app.css';
import axios from 'axios';
import Toast, { POSITION } from 'vue-toastification'; // Correctly import Vue Toastification
import 'vue-toastification/dist/index.css'; // Import Vue Toastification CSS

// Global components
import ErrorBoundary from './components/ErrorBoundary.vue';
import LoadingSpinner from './components/LoadingSpinner.vue';

/**
 * Create a fresh Vue application instance.
 */
const app = createApp(App);

/**
 * Configure Axios globally
 */
axios.defaults.baseURL = 'http://127.0.0.1:8000'; // Replace with your actual API base URL

/**
 * Axios Interceptor to include Authorization header
 */
axios.interceptors.request.use(
  config => {
    const token = localStorage.getItem('access_token'); // Corrected key
    // Add authorization header if token exists
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
  },
  error => {
    return Promise.reject(error);
  }
);

/**
 * Configure Vue Toastification
 */
app.use(Toast, {
  position: POSITION.TOP_RIGHT,
  timeout: 3000,
  closeOnClick: true,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
});

// Register global components
app.component('ErrorBoundary', ErrorBoundary);
app.component('LoadingSpinner', LoadingSpinner);

// Global error handler
app.config.errorHandler = (error, instance, info) => {
    console.error('Global error:', error);
    console.error('Component info:', info);
    
    // Send error to logging service in production
    if (process.env.NODE_ENV === 'production') {
        // You can integrate with services like Sentry here
        // Sentry.captureException(error);
    }
};

// Global warning handler
app.config.warnHandler = (msg, instance, trace) => {
    if (process.env.NODE_ENV === 'development') {
        console.warn('Vue warning:', msg);
        console.warn('Component trace:', trace);
    }
};

// Performance monitoring
app.config.performance = process.env.NODE_ENV === 'development';

/**
 * Register Vue Router
 */
app.use(router);

/**
 * Mount the Vue application
 */
app.mount('#app');
