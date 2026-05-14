import { defineConfig } from 'vite'

export default defineConfig({
    root: '.',
    build: {
        outDir: 'public/dist',
        assetsDir: 'assets',
        manifest: 'manifest.json',
        rollupOptions: {
            input: 'index.html',
        },
    },
})
