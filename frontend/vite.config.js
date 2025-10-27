// frontend/vite.config.js
import { defineConfig } from 'vite'
import fs from 'fs'
import path from 'path'

/**
 * Auto-detect all .js files in src/scripts/pages/
 * so each one becomes its own entry.
 */
const inputs = { main: './src/main.js' }
const pagesDir = path.resolve('src/scripts/pages')

if (fs.existsSync(pagesDir)) {
  fs.readdirSync(pagesDir).forEach(file => {
    if (file.endsWith('.js')) {
      const name = file.replace('.js', '')
      inputs[name] = `./src/scripts/pages/${file}`
    }
  })
}

export default defineConfig({
  base: process.env.NODE_ENV === 'development' ? '/' : '/dist/',
  build: {
    outDir: '../web/dist',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: inputs,
      output: {
        entryFileNames: 'assets/[name]-[hash].js',
        chunkFileNames: 'assets/[name]-[hash].js',
        assetFileNames: 'assets/[name]-[hash].[ext]',
      },
    },
  },
  css: {
    preprocessorOptions: {
      scss: {
        includePaths: ['node_modules'],
        silenceDeprecations: ['import', 'global-builtin', 'color-functions'],
      },
    },
  },
  server: {
    host: '0.0.0.0',
    port: 5173,
    strictPort: true,
    cors: true,
    origin: 'http://localhost:5173',
    allowedHosts: ['host.docker.internal', 'craft-cms.ddev.site'],
  },
})
