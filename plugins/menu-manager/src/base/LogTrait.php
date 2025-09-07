<?php
namespace mycompany\menumanager\base;

use Craft;
use craft\helpers\StringHelper;

trait LogTrait
{
    public static function log(string $message, array $params = [], string $level = 'info'): void
    {
        $category = static::class;
        
        if (!empty($params)) {
            $message = Craft::t('app', $message, $params);
        }

        switch ($level) {
            case 'error':
                Craft::error($message, $category);
                break;
            case 'warning':
                Craft::warning($message, $category);
                break;
            case 'info':
            default:
                Craft::info($message, $category);
                break;
        }
    }

    public static function error(string $message, array $params = []): void
    {
        static::log($message, $params, 'error');
    }

    public static function warning(string $message, array $params = []): void
    {
        static::log($message, $params, 'warning');
    }

    public static function info(string $message, array $params = []): void
    {
        static::log($message, $params, 'info');
    }
}