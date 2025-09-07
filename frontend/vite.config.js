import { defineConfig } from 'vite'

export default defineConfig({
  base: process.env.NODE_ENV === 'development' ? '/' : '/dist/',
  build: {
    outDir: '../web/dist',
    rollupOptions: {
      input: {
        main: './src/main.js',
        style: './src/style.css',
        navigation: './src/navigation.css'
      }
    },
    manifest: '.vite/manifest.json'
  },
  server: {
    host: '0.0.0.0',
    port: 5173,
    strictPort: true,
    hmr: {
      port: 5173,
      host: 'localhost'
    }
  }
})