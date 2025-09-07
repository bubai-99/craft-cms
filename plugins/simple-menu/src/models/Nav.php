<?php

namespace mycompany\simplemenu\models;

use craft\base\Model;
use craft\validators\HandleValidator;
use craft\validators\UniqueValidator;

class Nav extends Model
{
    public ?int $id = null;
    public ?string $name = null;
    public ?string $handle = null;
    public ?string $instructions = null;
    public ?int $sortOrder = null;
    public ?int $maxLevels = null;
    public ?string $uid = null;
    public ?\DateTime $dateCreated = null;
    public ?\DateTime $dateUpdated = null;

    public function rules(): array
    {
        return [
            [['name', 'handle'], 'required'],
            [['name', 'handle'], 'string', 'max' => 255],
            [['handle'], HandleValidator::class],
            [['handle'], UniqueValidator::class, 'targetClass' => self::class],
            [['instructions'], 'string'],
            [['maxLevels', 'sortOrder'], 'integer', 'min' => 1],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => \Craft::t('simple-menu', 'Name'),
            'handle' => \Craft::t('simple-menu', 'Handle'),
            'instructions' => \Craft::t('simple-menu', 'Instructions'),
            'maxLevels' => \Craft::t('simple-menu', 'Max Levels'),
            'sortOrder' => \Craft::t('simple-menu', 'Sort Order'),
        ];
    }

    public function getNodes(): array
    {
        if (!$this->id) {
            return [];
        }

        // For legacy support, get from menuService if available
        $legacyMenu = \mycompany\simplemenu\Plugin::getInstance()->menuService->getMenuById($this->id);
        if ($legacyMenu && !empty($legacyMenu->items)) {
            // Convert legacy items to node-like structure
            return array_map(function($item) {
                return (object)[
                    'title' => $item['label'] ?? 'Untitled',
                    'url' => $item['url'] ?? '',
                    'enabled' => $item['enabled'] ?? true,
                    'type' => 'custom'
                ];
            }, $legacyMenu->items);
        }

        return \mycompany\simplemenu\Plugin::getInstance()->nodeService->getNodesByNavId($this->id);
    }

    public function getNodesTree(): array
    {
        if (!$this->id) {
            return [];
        }

        return \mycompany\simplemenu\Plugin::getInstance()->nodeService->getNodesTreeByNavId($this->id);
    }
}