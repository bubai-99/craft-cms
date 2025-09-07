<?php

namespace mycompany\simplemenu\models;

use craft\base\Model;
use craft\helpers\Json;

class Menu extends Model
{
    public ?int $id = null;
    public ?string $name = null;
    public ?string $handle = null;
    public array $items = []; // Legacy support
    public array $nodes = [];
    public ?int $maxLevels = null;
    public array $settings = [];
    public ?\DateTime $dateCreated = null;
    public ?\DateTime $dateUpdated = null;
    public ?string $uid = null;

    public function rules(): array
    {
        return [
            [['name', 'handle'], 'required'],
            [['name', 'handle'], 'string', 'max' => 255],
            [['maxLevels'], 'integer', 'min' => 1],
            [['items', 'nodes', 'settings'], 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Name',
            'handle' => 'Handle',
            'maxLevels' => 'Max Levels',
            'items' => 'Menu Items',
            'nodes' => 'Navigation Nodes',
        ];
    }

    public function getItemsJson(): string
    {
        return Json::encode($this->items);
    }

    public function setItemsJson(string $json): void
    {
        $this->items = Json::decode($json) ?: [];
    }

    public function getNodesJson(): string
    {
        return Json::encode($this->nodes);
    }

    public function setNodesJson(string $json): void
    {
        $this->nodes = Json::decode($json) ?: [];
    }

    public function getSettingsJson(): string
    {
        return Json::encode($this->settings);
    }

    public function setSettingsJson(string $json): void
    {
        $this->settings = Json::decode($json) ?: [];
    }
}