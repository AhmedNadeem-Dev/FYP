import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            '@': '/resources/js',
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    // Vendor chunks
                    vendor: ['vue', 'vue-router'],
                    ui: ['vue-toastification', 'bootstrap'],
                    
                    // Component chunks
                    auth: [
                        'resources/js/components/Login.vue',
                        'resources/js/components/Signup.vue',
                        'resources/js/components/ResetPassword.vue',
                        'resources/js/components/ForgetPassword.vue'
                    ],
                    dashboard: [
                        'resources/js/components/AdminDashboard.vue',
                        'resources/js/components/ArtistDashboard.vue',
                        'resources/js/components/ScrapSellerDashboard.vue'
                    ],
                    shop: [
                        'resources/js/components/BrowseScrap.vue',
                        'resources/js/components/ProductDetails.vue',
                        'resources/js/components/AddCart.vue',
                        'resources/js/components/Checkout.vue'
                    ]
                }
            }
        },
        // Optimize bundle size
        chunkSizeWarningLimit: 1000,
        sourcemap: process.env.NODE_ENV === 'development',
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: process.env.NODE_ENV === 'production',
                drop_debugger: process.env.NODE_ENV === 'production',
            },
        },
    },
    server: {
        host: '0.0.0.0',
        port: 3000,
        hmr: {
            host: 'localhost',
        },
    },
    optimizeDeps: {
        include: ['vue', 'vue-router', 'vue-toastification', 'axios'],
    },
});
