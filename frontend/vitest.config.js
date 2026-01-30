import { fileURLToPath, URL } from 'node:url'
import { defineConfig, mergeConfig } from 'vitest/config'
import viteConfig from './vite.config.js'

export default mergeConfig(viteConfig, defineConfig({
    test: {
        environment: 'jsdom',
        exclude: ['**/node_modules/**'],
        root: fileURLToPath(new URL('./', import.meta.url)),
        server: {
            deps: {
                inline: ['vuetify'],
            },
        },
        setupFiles: ['./src/test/setup.ts'],
        globals: true
    }
}))
