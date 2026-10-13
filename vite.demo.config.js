// Standalone build of the Vue app for a static demo (e.g. Netlify), with no
// Laravel backend. Run: npm run build:demo  →  dist-demo/
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { copyFileSync, writeFileSync } from 'node:fs';
import { resolve } from 'node:path';
import { fileURLToPath, URL } from 'node:url';

const path = (p) => fileURLToPath(new URL(p, import.meta.url));

const realEcho = path('./resources/js/utils/echo.js');
const echoStub = path('./demo/echo-stub.js');

// Only these files from public/ are used by the SPA; the rest is Laravel's
const publicAssets = ['image.webp', 'favicon.ico'];

export default defineConfig({
    root: path('./demo'),
    envDir: path('./demo'),
    publicDir: false,
    resolve: {
        alias: { '@': path('./resources/js') },
    },
    plugins: [
        vue(),
        {
            name: 'demo-echo-stub',
            enforce: 'pre',
            async resolveId(source, importer, options) {
                const resolved = await this.resolve(source, importer, { ...options, skipSelf: true });
                if (resolved?.id === realEcho) return echoStub;
            },
        },
        {
            name: 'demo-public-assets',
            writeBundle(options) {
                publicAssets.forEach((f) => copyFileSync(path(`./public/${f}`), resolve(options.dir, f)));
                // SPA fallback for Netlify, so refreshing /home/... serves the app.
                // Lives in the build output so drag-and-drop deploys get it too.
                writeFileSync(resolve(options.dir, '_redirects'), '/*    /index.html    200\n');
            },
        },
    ],
    css: {
        preprocessorOptions: {
            scss: {
                api: 'modern-compiler',
                loadPaths: [path('./resources/scss')],
                quietDeps: true,
                silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'if-function'],
            },
        },
    },
    build: {
        outDir: path('./dist-demo'),
        emptyOutDir: true,
    },
});
