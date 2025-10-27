import { defineConfig } from 'vite'
import { resolve } from 'path'

export default defineConfig({
  base: process.env.NODE_ENV === 'development' ? '/' : '/dist/',
  build: {
    outDir: '../web/dist',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'src/main.js'),
      },
    },
  },
  server: {
    host: '0.0.0.0',
    port: 5173,
    strictPort: true,
    cors: true,
    origin: 'http://localhost:5173',
    allowedHosts: [
      'host.docker.internal',
      'craft-cms.ddev.site',
    ],
  },
})
