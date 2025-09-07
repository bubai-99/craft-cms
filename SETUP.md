# Craft CMS Template Project

This is a template Craft CMS project that includes pre-configured plugins and is ready to clone for new projects.

## Included Plugins

- **Menu Manager** v1.0+ - Advanced navigation management
- **SEO** - Search engine optimization tools  
- **Contact Form** - Official Craft contact forms
- **CKEditor** - Rich text editing
- **Vite** - Modern asset bundling

## Quick Start

### Option 1: Clone this template project
```bash
git clone <this-repo-url> my-new-project
cd my-new-project
composer install
cp .env.example.dev .env
# Configure your database in .env
php craft install
php craft plugin/install menu-manager
php craft plugin/install seo
php craft plugin/install contact-form
```

### Option 2: Create fresh project with Menu Manager
```bash
composer create-project craftcms/craft my-new-project
cd my-new-project
```

Add to your `composer.json`:
```json
{
  "require": {
    "mycompany/menu-manager": "^1.0",
    "ether/seo": "^5.0",
    "craftcms/contact-form": "^3.1"
  },
  "repositories": [
    {
      "type": "vcs",
      "url": "git@github.com:bubai-99/menu-manager.git"
    }
  ]
}
```

Then run:
```bash
composer install
php craft plugin/install menu-manager
```

## Menu Manager Usage

After installation, you can:

1. **Create Navigations**: Go to Menu Manager in the CP
2. **Add Nodes**: Link to entries, categories, or custom URLs
3. **Template Usage**:
   ```twig
   {# Simple render #}
   {{ craft.menuManager.render('mainMenu') }}
   
   {# Custom render #}
   {% set nodes = craft.menuManager.nodes('mainMenu').all() %}
   <ul>
     {% nav node in nodes %}
       <li><a href="{{ node.url }}">{{ node.title }}</a>
         {% ifchildren %}<ul>{% children %}</ul>{% endifchildren %}
       </li>
     {% endnav %}
   </ul>
   ```

## Documentation

Full documentation is available at: https://github.com/bubai-99/menu-manager/tree/main/docs

## Development

- **DDEV**: Use `ddev start` for local development
- **Frontend**: Assets are managed with Vite in `/frontend/`
- **Templates**: Located in `/templates/`

## Support

- Menu Manager: https://github.com/bubai-99/menu-manager/issues
- Craft CMS: https://craftcms.com/docs/