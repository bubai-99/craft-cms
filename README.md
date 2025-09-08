# Craft CMS 5 + Vite + Tailwind CSS Project

A modern CMS project built with **Craft CMS 5**, **Vite**, and **Tailwind CSS v3**, configured for development with DDEV.

## 🚀 Features

- **Craft CMS 5.0** - Latest version with Matrix blocks as entries
- **Vite Integration** - Modern frontend build tool with Hot Module Replacement (HMR)
- **Tailwind CSS v3** - Utility-first CSS framework with automatic class detection
- **Matrix Blocks** - Text, Heading, Image, Quote, Gallery content blocks
- **Menu Manager Plugin** - Professional navigation system
- **Responsive Design** - Mobile-first approach with modern CSS Grid and Flexbox
- **Professional Template Structure** - Modular, maintainable architecture

## 📋 Requirements

- PHP 8.2+
- MySQL 8.0+
- Node.js 20.19+ or 22.12+
- Composer 2
- DDEV (for local development)

## ⚡ Quick Start

### Using DDEV (Recommended)

1. **Clone and start the environment:**
   ```bash
   git clone <repository-url>
   cd craft-cms
   ddev start
   ```

2. **Install PHP dependencies:**
   ```bash
   ddev composer install
   ```

3. **Install Node.js dependencies and build frontend:**
   ```bash
   cd frontend
   npm install
   npm run build
   cd ..
   ```

4. **Run Craft CMS installation:**
   ```bash
   ddev craft install
   ```

5. **Access your site:**
   - **Website**: https://craft-cms.ddev.site
   - **Control Panel**: https://craft-cms.ddev.site/admin

## 🎨 Frontend Development

### Development Workflow

1. **Start Vite dev server:**
   ```bash
   cd frontend
   npm run dev
   ```

2. **Available scripts:**
   ```bash
   npm run dev      # Start development server with HMR
   npm run build    # Build for production
   npm run preview  # Preview production build
   ```

### Frontend Architecture

```
frontend/
├── src/
│   ├── assets/          # Static assets (images, icons)
│   ├── scripts/         # JavaScript modules
│   │   ├── index.js     # Main scripts export
│   │   ├── counter.js   # Example component
│   │   └── contact-form.js # Form handling
│   ├── styles/          # CSS stylesheets
│   │   ├── index.css    # Main stylesheet
│   │   └── style.css    # Tailwind CSS + custom styles
│   ├── main.js          # Application entry point
│   └── README.md        # Frontend documentation
├── tailwind.config.js   # Tailwind CSS configuration
├── postcss.config.js    # PostCSS configuration
├── vite.config.js       # Vite configuration
└── package.json         # Node dependencies
```

### Tailwind CSS Integration

- **Automatic class detection** from Twig templates
- **No manual maintenance required** - just use classes in templates
- **Custom primary color palette** configured
- **Responsive utilities** with mobile-first approach
- **Optimized production builds** with purged unused styles

## 🏗️ Project Structure

```
├── config/              # Craft CMS configuration
├── frontend/            # Vite + Tailwind CSS frontend
│   ├── src/             # Source files (organized by type)
│   ├── tailwind.config.js
│   └── vite.config.js
├── storage/             # File storage, logs, cache
├── templates/           # Twig templates
│   ├── _layouts/        # Base layouts
│   ├── _partials/       # Reusable partials
│   ├── _components/     # UI components
│   └── index.twig       # Homepage template
├── web/                 # Web root
│   ├── dist/            # Built assets (auto-generated)
│   └── index.php        # Entry point
├── .ddev/               # DDEV configuration
└── .env                 # Environment variables
```

## 🔧 Development Commands

### DDEV Commands

```bash
# Environment management
ddev start               # Start development environment
ddev stop                # Stop environment
ddev ssh                 # SSH into web container

# Craft CMS commands
ddev craft install       # Install Craft CMS
ddev craft clear-caches/all # Clear caches
ddev craft migrate/all   # Run migrations
ddev craft users/create  # Create admin user

# Composer commands
ddev composer install    # Install PHP dependencies
ddev composer update     # Update dependencies
```

### Frontend Commands

```bash
cd frontend

# Development
npm run dev              # Start dev server (http://localhost:5173)
npm run build           # Production build
npm run preview         # Preview production build

# Maintenance
npm install             # Install dependencies
npm update              # Update dependencies
```

## 🎯 Configuration

### Environment Files

- `.env.example.dev` - Development environment template
- `.env.example.staging` - Staging environment template  
- `.env.example.production` - Production environment template

Copy the appropriate example file to `.env` and configure your settings.

### DDEV Configuration

The project includes DDEV configuration with:
- **PHP 8.3** with required extensions
- **MySQL 8.0** database
- **Nginx** with PHP-FPM
- **Node.js** for frontend development
- **Project URL**: https://craft-cms.ddev.site

### Vite Configuration

- **HMR** enabled for development
- **Asset optimization** for production
- **Tailwind CSS** processing via PostCSS
- **Build output** to `web/dist/`

## 🚀 Deployment

### Production Build

1. **Install dependencies:**
   ```bash
   composer install --no-dev --optimize-autoloader
   cd frontend && npm ci && npm run build
   ```

2. **Configure environment:**
   ```bash
   cp .env.example.production .env
   # Edit .env with production settings
   ```

3. **Run migrations and setup:**
   ```bash
   php craft migrate/all
   php craft clear-caches/all
   ```

### Frontend Assets

The Vite build process automatically:
- Generates optimized CSS and JS bundles
- Creates asset manifest for Craft CMS
- Outputs to `web/dist/` directory
- Enables proper cache busting

## 📖 Key Features Explained

### Matrix Blocks System

Craft CMS 5 uses **Matrix blocks as entries**, providing:
- Flexible content modeling
- Reusable content components
- Professional template organization
- Easy content management

### Tailwind CSS Integration

- **Automatic class detection** from Twig templates
- **No safelist required** - classes are found automatically
- **Custom primary color palette**
- **Production optimization** with unused style removal

### Vite Development Experience

- **Hot Module Replacement** for instant updates
- **Modern ES modules** for better development
- **Optimized production builds**
- **Asset processing** and optimization

## 🆘 Troubleshooting

### Frontend Issues

```bash
# Clear node modules and reinstall
rm -rf frontend/node_modules frontend/package-lock.json
npm install

# Rebuild assets
npm run build
```

### Craft CMS Issues

```bash
# Clear all caches
ddev craft clear-caches/all

# Check system status
ddev craft help
```

## 📚 Documentation

- [Craft CMS 5 Documentation](https://craftcms.com/docs/5.x/)
- [Vite Documentation](https://vite.dev/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [DDEV Documentation](https://ddev.readthedocs.io/)

## 🎉 Getting Started Tips

1. **Start with the Control Panel** - Create your first pages and content
2. **Use Matrix blocks** - Build flexible page layouts
3. **Customize navigation** - Set up your menu structure
4. **Modify templates** - Use Tailwind classes directly in Twig files
5. **Add custom styles** - Use `frontend/src/styles/style.css` for any custom CSS needed

---

**Happy building! 🚀**