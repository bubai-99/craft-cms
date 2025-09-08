# Frontend Source Structure

This directory contains all the frontend source code for the Craft CMS project.

## Directory Structure

```
src/
├── assets/           # Static assets (images, icons, SVGs)
│   └── javascript.svg
├── scripts/          # JavaScript modules and utilities
│   ├── index.js      # Main scripts export file
│   ├── counter.js    # Counter component
│   └── contact-form.js # Contact form functionality
├── styles/           # CSS stylesheets
│   ├── index.css     # Main stylesheet (imports all others)
│   ├── style.css     # Core Tailwind CSS and base styles
│   ├── navigation.css # Navigation component styles
│   └── blocks.css    # Block component styles
├── main.js           # Application entry point
└── README.md         # This file
```

## Adding New Files

- **Styles**: Add new CSS files to `styles/` and import them in `styles/index.css`
- **Scripts**: Add new JS modules to `scripts/` and export them from `scripts/index.js`
- **Assets**: Place images, icons, and other static files in `assets/`

## Import Patterns

```javascript
// Import all styles
import './styles/index.css'

// Import specific utilities
import { setupCounter } from './scripts/index.js'

// Import assets
import logo from './assets/logo.svg'
```