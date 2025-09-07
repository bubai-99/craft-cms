<?php

namespace mycompany\simplemenu\models;

use craft\base\Model;
use craft\elements\Entry;
use craft\elements\Asset;
use craft\elements\Category;

class Node extends Model
{
    public ?int $id = null;
    public ?int $navigationId = null;
    public ?int $parentId = null;
    public ?int $elementId = null;
    public ?int $elementSiteId = null;
    public string $type = 'custom';
    public ?string $title = null;
    public ?string $url = null;
    public ?string $customUrl = null;
    public ?string $classes = null;
    public bool $newWindow = false;
    public bool $enabled = true;
    public int $lft = 0;
    public int $rgt = 0;
    public int $level = 0;
    public ?\DateTime $dateCreated = null;
    public ?\DateTime $dateUpdated = null;
    public ?string $uid = null;

    // Node types
    const TYPE_CUSTOM = 'custom';
    const TYPE_ENTRY = 'entry';
    const TYPE_ASSET = 'asset';
    const TYPE_CATEGORY = 'category';

    public function rules(): array
    {
        return [
            [['navigationId', 'type'], 'required'],
            [['title'], 'required', 'when' => function($model) {
                return $model->type === self::TYPE_CUSTOM;
            }],
            [['customUrl'], 'required', 'when' => function($model) {
                return $model->type === self::TYPE_CUSTOM;
            }],
            [['elementId'], 'required', 'when' => function($model) {
                return in_array($model->type, [self::TYPE_ENTRY, self::TYPE_ASSET, self::TYPE_CATEGORY]);
            }],
            [['type'], 'in', 'range' => [self::TYPE_CUSTOM, self::TYPE_ENTRY, self::TYPE_ASSET, self::TYPE_CATEGORY]],
            [['newWindow', 'enabled'], 'boolean'],
            [['navigationId', 'parentId', 'elementId', 'elementSiteId', 'lft', 'rgt', 'level'], 'integer'],
            [['title', 'url', 'customUrl', 'classes'], 'string', 'max' => 255],
        ];
    }

    public function getElement()
    {
        if (!$this->elementId) {
            return null;
        }

        switch ($this->type) {
            case self::TYPE_ENTRY:
                return Entry::find()->id($this->elementId)->siteId($this->elementSiteId)->one();
            case self::TYPE_ASSET:
                return Asset::find()->id($this->elementId)->one();
            case self::TYPE_CATEGORY:
                return Category::find()->id($this->elementId)->siteId($this->elementSiteId)->one();
            default:
                return null;
        }
    }

    public function getUrl(): ?string
    {
        if ($this->type === self::TYPE_CUSTOM) {
            return $this->customUrl;
        }

        $element = $this->getElement();
        return $element ? $element->getUrl() : null;
    }

    public function getTitle(): ?string
    {
        if ($this->type === self::TYPE_CUSTOM) {
            return $this->title;
        }

        $element = $this->getElement();
        return $element ? $element->title : $this->title;
    }

    public function attributeLabels(): array
    {
        return [
            'title' => 'Title',
            'type' => 'Link Type',
            'customUrl' => 'URL',
            'elementId' => 'Link To',
            'classes' => 'CSS Classes',
            'newWindow' => 'Open in new window',
            'enabled' => 'Enabled',
        ];
    }
}