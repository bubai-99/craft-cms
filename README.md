# Craft CMS Project

A modern CMS project built with Craft CMS 5.0, configured for development with DDEV.

## Requirements

- PHP 8.2+
- MySQL 8.0+
- Composer 2
- DDEV (for local development)

## Installation

### Using DDEV (Recommended)

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd craft-cms
   ```

2. Start the DDEV environment:
   ```bash
   ddev start
   ```

3. Install dependencies:
   ```bash
   ddev composer install
   ```

4. Run Craft CMS installation:
   ```bash
   ddev craft install
   ```

### Manual Installation

1. Install dependencies:
   ```bash
   composer install
   ```

2. Set up environment:
   ```bash
   cp .env.example.dev .env
   ```

3. Configure your database settings in `.env`

4. Run Craft CMS installation:
   ```bash
   php craft install
   ```

## Configuration

### Environment Files

- `.env.example.dev` - Development environment template
- `.env.example.staging` - Staging environment template  
- `.env.example.production` - Production environment template

Copy the appropriate example file to `.env` and configure your settings.

### DDEV Configuration

The project includes DDEV configuration with:
- PHP 8.3
- MySQL 8.0
- Nginx with PHP-FPM
- Project URL: https://craft-cms.ddev.site

## Development

### DDEV Commands

```bash
# Start the development environment
ddev start

# Stop the environment
ddev stop

# SSH into the web container
ddev ssh

# Run Craft console commands
ddev craft <command>

# Run Composer commands
ddev composer <command>

# Import database
ddev import-db --file=database.sql
```

### Craft Console Commands

```bash
# Clear caches
ddev craft clear-caches/all

# Run migrations
ddev craft migrate/all

# Create admin user
ddev craft users/create
```

## Project Structure

```
├── config/           # Craft CMS configuration files
├── storage/          # File storage, logs, and cache
├── templates/        # Twig templates
├── web/             # Web root directory
│   ├── index.php    # Entry point
│   └── assets/      # Public assets
├── .ddev/           # DDEV configuration
├── .env             # Environment variables
└── craft            # Craft console executable
```

## Deployment

1. Run production build:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

2. Set up production environment variables in `.env`

3. Run migrations:
   ```bash
   php craft migrate/all
   ```

4. Clear caches:
   ```bash
   php craft clear-caches/all
   ```

## Support

- [Craft CMS Documentation](https://craftcms.com/docs/)
- [DDEV Documentation](https://ddev.readthedocs.io/)