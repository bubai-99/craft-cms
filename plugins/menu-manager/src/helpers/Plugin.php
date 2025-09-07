<?php
namespace mycompany\menumanager\helpers;

use Craft;
use craft\helpers\App;

class Plugin
{
    public static function bootstrapPlugin(string $handle): void
    {
        // Set plugin configuration
        if ($handle === 'menu-manager') {
            // Any specific bootstrap logic can go here
            Craft::info("Bootstrapping {$handle} plugin", __METHOD__);
        }
    }

    public static function getPlugin(string $handle = null): ?\craft\base\PluginInterface
    {
        if ($handle) {
            return Craft::$app->getPlugins()->getPlugin($handle);
        }
        
        // Return current plugin instance if no handle specified
        return null;
    }

    public static function isPluginInstalled(string $handle): bool
    {
        return Craft::$app->getPlugins()->isPluginInstalled($handle);
    }

    public static function isPluginEnabled(string $handle): bool
    {
        return Craft::$app->getPlugins()->isPluginEnabled($handle);
    }
}
