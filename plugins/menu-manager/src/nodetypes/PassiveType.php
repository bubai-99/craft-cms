<?php
namespace mycompany\menumanager\nodetypes;

use mycompany\menumanager\base\NodeType;

use Craft;

class PassiveType extends NodeType
{
    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('menu-manager', 'Passive');
    }

    public static function hasTitle(): bool
    {
        return true;
    }

    public static function hasUrl(): bool
    {
        return false;
    }

    public static function hasNewWindow(): bool
    {
        return false;
    }

    public static function getColor(): string
    {
        return '#fe7d02';
    }
}
