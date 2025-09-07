<?php
namespace mycompany\menumanager\helpers;

use mycompany\menumanager\MenuManager;

use Craft;
use craft\models\Structure;

class ProjectConfigData
{
    // Static Methods
    // =========================================================================

    public static function rebuildProjectConfig(): array
    {
        $configData = [];

        $configData['navs'] = self::_getNavsData();

        return array_filter($configData);
    }

    
    // Private Methods
    // =========================================================================

    private static function _getNavsData(): array
    {
        $data = [];

        foreach (MenuManager::$plugin->getNavs()->getAllNavs() as $nav) {
            $data[$nav->uid] = $nav->getConfig();
        }

        return $data;
    }
}