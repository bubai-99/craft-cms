# Menu Manager Plugin Development Guide

Complete guide for developing, testing, and deploying the Menu Manager plugin for Craft CMS.

## 📁 Project Structure

```
/home/binu/my-project/
├── craft-cms/                    # Template Craft CMS project
│   ├── vendor/mycompany/menu-manager/  # Plugin copy for testing
│   ├── dev-sync.sh              # Development sync script
│   └── SETUP.md                 # Template project setup instructions
└── menu-manager-plugin/         # Plugin development repository
    ├── src/                     # Plugin source code
    ├── docs/                    # Documentation
    ├── composer.json            # Plugin metadata
    └── CHANGELOG.md             # Version history
```

## 🚀 Initial Setup (Already Done)

### 1. Plugin Repository Created
- **GitHub URL**: https://github.com/bubai-99/menu-manager
- **Version**: 1.0.0 (tagged release)
- **Composer Package**: `mycompany/menu-manager`

### 2. Template Project Configured
- Pre-configured Craft CMS project with Menu Manager
- Development sync script for testing
- Ready for cloning new projects

## 🛠️ Development Workflow

### 1. Making Changes to Plugin

```bash
# Navigate to plugin development directory
cd /home/binu/my-project/menu-manager-plugin

# Make your changes
vim src/MenuManager.php
# or edit any plugin file

# Check changes
git status
git diff
```

### 2. Testing Changes

```bash
# Navigate to Craft CMS template project
cd /home/binu/my-project/craft-cms

# Sync plugin changes to vendor directory
./dev-sync.sh

# Test with DDEV
ddev craft plugin/list
ddev craft plugin/install menu-manager  # if not installed
# Test your changes in the Craft CMS admin panel
```

### 3. Committing Changes

```bash
# Back to plugin development directory
cd /home/binu/my-project/menu-manager-plugin

# Stage changes
git add .

# Commit with descriptive message
git commit -m "Add new navigation feature: custom CSS classes"

# Push to GitHub
git push origin main
```

## 📦 Release Process

### 1. Update Version Information

#### Update `composer.json`:
```json
{
  "version": "1.0.1"
}
```

#### Update `CHANGELOG.md`:
```markdown
# Changelog

## 1.0.1 - 2025-09-08

### Added
- New feature: Custom CSS classes for navigation items
- Improved breadcrumb generation

### Fixed
- Fixed issue with multi-site navigation rendering

### Changed
- Updated documentation with new examples

## 1.0.0 - 2025-09-07
- Initial release
```

### 2. Create Git Tag and Release

```bash
# Navigate to plugin repository
cd /home/binu/my-project/menu-manager-plugin

# Commit version updates
git add .
git commit -m "Release version 1.0.1"

# Create and push tag
git tag -a v1.0.1 -m "Release version 1.0.1 - Custom CSS classes and bug fixes"
git push origin main
git push origin v1.0.1
```

### 3. Version Types

- **1.0.1** (Patch) - Bug fixes, no breaking changes
- **1.1.0** (Minor) - New features, backwards compatible
- **2.0.0** (Major) - Breaking changes, may require code updates

## 🏗️ Creating New Craft CMS Projects

### Method 1: Clone Template Project

```bash
# Clone your configured template
git clone <your-craft-cms-repo-url> new-project-name
cd new-project-name

# Install dependencies (will pull Menu Manager from GitHub)
composer install

# Setup environment
cp .env.example.dev .env
# Edit .env with your database settings

# Install Craft CMS
php craft install

# Install plugins
php craft plugin/install menu-manager
php craft plugin/install seo
php craft plugin/install contact-form
```

### Method 2: Fresh Project + Menu Manager

```bash
# Create new Craft project
composer create-project craftcms/craft new-project
cd new-project

# Add Menu Manager to composer.json
```

Add to `composer.json`:
```json
{
  "require": {
    "mycompany/menu-manager": "^1.0"
  },
  "repositories": [
    {
      "type": "vcs",
      "url": "git@github.com:bubai-99/menu-manager.git"
    }
  ]
}
```

```bash
# Install and setup
composer install
php craft plugin/install menu-manager
```

## 📖 Using Menu Manager in Projects

### Template Usage

#### Simple Navigation Render
```twig
{# Render complete navigation #}
{{ craft.menuManager.render('mainMenu', {
    ulClass: 'nav-items',
    liClass: 'nav-item',
    aClass: 'nav-link',
    activeClass: 'nav-active'
}) }}
```

#### Custom Navigation Rendering
```twig
{# Get navigation nodes #}
{% set nodes = craft.menuManager.nodes('mainMenu').all() %}

{# Custom render with full control #}
<ul class="main-navigation">
    {% nav node in nodes %}
        <li class="{{ node.hasChildren ? 'has-children' : '' }}">
            <a href="{{ node.url }}" 
               {{ node.newWindow ? 'target="_blank"' : '' }}
               class="{{ node.classes }}">
                {{ node.title }}
            </a>
            
            {% ifchildren %}
                <ul class="sub-menu">
                    {% children %}
                </ul>
            {% endifchildren %}
        </li>
    {% endnav %}
</ul>
```

#### Breadcrumbs
```twig
{# Generate breadcrumbs #}
{% set breadcrumbs = craft.menuManager.breadcrumbs() %}

<nav class="breadcrumb">
    {% for crumb in breadcrumbs %}
        {% if not loop.last %}
            <a href="{{ crumb.url }}">{{ crumb.title }}</a>
            <span class="separator">/</span>
        {% else %}
            <span class="current">{{ crumb.title }}</span>
        {% endif %}
    {% endfor %}
</nav>
```

### Admin Panel Usage

1. **Create Navigation**: Go to Menu Manager → Create New Navigation
2. **Add Nodes**: 
   - Link to Craft entries, categories, assets
   - Add custom URLs
   - Configure CSS classes and attributes
3. **Multi-site**: Configure different navigation per site
4. **Field Integration**: Add Navigation fields to entry types

## 🔧 Development Tools

### Development Sync Script (`dev-sync.sh`)

```bash
#!/bin/bash
# Syncs changes from plugin development repo to vendor for testing

SOURCE_DIR="/home/binu/my-project/menu-manager-plugin"
TARGET_DIR="vendor/mycompany/menu-manager"

echo "🔄 Syncing Menu Manager plugin..."
rm -rf "$TARGET_DIR"
cp -r "$SOURCE_DIR" "$TARGET_DIR"
rm -rf "$TARGET_DIR/.git"
echo "✅ Menu Manager plugin synced!"
```

Usage:
```bash
cd /home/binu/my-project/craft-cms
./dev-sync.sh
```

### Useful Commands

```bash
# List all plugins
ddev craft plugin/list

# Install plugin
ddev craft plugin/install menu-manager

# Uninstall plugin
ddev craft plugin/uninstall menu-manager

# Clear caches
ddev craft clear-caches/all

# Check plugin status
ddev craft plugin/list | grep menu

# Update composer packages
composer update mycompany/menu-manager
```

## 📋 Update Checklist

When updating the plugin:

- [ ] Make code changes in `/menu-manager-plugin/`
- [ ] Run `./dev-sync.sh` to test changes
- [ ] Test in Craft CMS admin panel
- [ ] Update version in `composer.json`
- [ ] Update `CHANGELOG.md`
- [ ] Commit changes with descriptive message
- [ ] Create and push git tag (`v1.x.x`)
- [ ] Test installation in fresh project
- [ ] Update documentation if needed

## 🔍 Troubleshooting

### Common Issues

#### Plugin Not Found
```bash
# Check composer.json repositories section
# Ensure GitHub repository is accessible
# Verify tag exists: git tag -l
```

#### DDEV Path Issues
```bash
# Always use the dev-sync.sh script for DDEV compatibility
# Symlinks don't work with DDEV containers
./dev-sync.sh
```

#### Version Conflicts
```bash
# Clear composer cache
composer clear-cache

# Update with specific version
composer require mycompany/menu-manager:^1.0.1
```

#### Database Issues
```bash
# Clear Craft caches
ddev craft clear-caches/all

# Reinstall plugin
ddev craft plugin/uninstall menu-manager
ddev craft plugin/install menu-manager
```

## 📚 Documentation

- **Plugin Documentation**: `/menu-manager-plugin/docs/`
- **GitHub Repository**: https://github.com/bubai-99/menu-manager
- **Template Setup**: `SETUP.md` in craft-cms project
- **Craft CMS Docs**: https://craftcms.com/docs/

## 🎯 Best Practices

### Code Standards
- Follow Craft CMS coding standards
- Use proper namespace: `mycompany\menumanager`
- Comment complex logic
- Write descriptive commit messages

### Testing
- Test all changes in development environment
- Verify plugin installation on fresh projects
- Test multi-site functionality if applicable
- Check backwards compatibility

### Documentation
- Update CHANGELOG.md for every release
- Keep README.md current
- Document new features in `/docs/`
- Provide template usage examples

### Version Management
- Use semantic versioning (MAJOR.MINOR.PATCH)
- Tag all releases properly
- Maintain backwards compatibility when possible
- Communicate breaking changes clearly

## 🤝 Support

- **Issues**: https://github.com/bubai-99/menu-manager/issues
- **Development**: Contact project maintainer
- **Craft CMS**: https://craftcms.com/docs/

---

*This documentation covers the complete development lifecycle for the Menu Manager plugin. Keep it updated as the project evolves.*