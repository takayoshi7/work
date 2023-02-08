import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'
import vuetify from "vite-plugin-vuetify"	// 追加

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        vue(),
        vuetify(),	// 追加
    ],

    server: {
        hmr: {
            host: 'localhost',
        },
    },
});
