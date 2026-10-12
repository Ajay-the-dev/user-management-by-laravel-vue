import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { fileURLToPath, URL } from 'node:url';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/scss/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                api: 'modern-compiler',
                // Lets every <style lang="scss"> block do `@use "abstracts" as *;`
                loadPaths: [fileURLToPath(new URL('./resources/scss', import.meta.url))],
                // Bootstrap 5.3 still uses @import internally; keep our own output clean
                quietDeps: true,
                silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'if-function'],
            },
        },
    },
});
