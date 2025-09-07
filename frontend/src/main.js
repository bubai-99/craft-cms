import './style.css'
import './navigation.css'
import './blocks.css'
import './contact-form.js'
import javascriptLogo from './javascript.svg'
import viteLogo from '/vite.svg'
import { setupCounter } from './counter.js'

// Initialize the app when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
  const app = document.querySelector('#app')
  
  if (app) {
    // Add Vite-powered content to existing Craft template
    const viteContent = document.createElement('div')
    viteContent.className = 'vite-content'
    viteContent.innerHTML = `
      <div class="vite-section">
        <a href="https://vite.dev" target="_blank">
          <img src="${viteLogo}" class="logo" alt="Vite logo" />
        </a>
        <a href="https://developer.mozilla.org/en-US/docs/Web/JavaScript" target="_blank">
          <img src="${javascriptLogo}" class="logo vanilla" alt="JavaScript logo" />
        </a>
        <div class="card">
          <button id="counter" type="button"></button>
        </div>
        <p class="read-the-docs">
          This is powered by Vite - click the counter to test JavaScript!
        </p>
      </div>
    `
    
    app.appendChild(viteContent)
    setupCounter(document.querySelector('#counter'))
  }
})
