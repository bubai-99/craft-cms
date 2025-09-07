<?php

namespace mycompany\simplemenu\elements;

use craft\base\Element;
use craft\elements\Entry;
use craft\elements\Asset;
use craft\elements\Category;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\UrlHelper;
use craft\web\CpScreenResponseBehavior;
use mycompany\simplemenu\elements\db\NodeQuery;
use mycompany\simplemenu\Plugin;
use yii\web\Response;

class Node extends Element
{
    public ?int $navId = null;
    public ?int $elementId = null;
    public ?int $elementSiteId = null;
    public string $type = 'custom';
    public ?string $classes = null;
    public ?string $customUrl = null;
    public bool $newWindow = false;
    public bool $enabled = true;

    // Node types
    const TYPE_CUSTOM = 'custom';
    const TYPE_ENTRY = 'entry';
    const TYPE_ASSET = 'asset';
    const TYPE_CATEGORY = 'category';

    public static function displayName(): string
    {
        return \Craft::t('simple-menu', 'Navigation Node');
    }

    public static function lowerDisplayName(): string
    {
        return \Craft::t('simple-menu', 'navigation node');
    }

    public static function pluralDisplayName(): string
    {
        return \Craft::t('simple-menu', 'Navigation Nodes');
    }

    public static function pluralLowerDisplayName(): string
    {
        return \Craft::t('simple-menu', 'navigation nodes');
    }

    public static function refHandle(): string
    {
        return 'navigationnode';
    }

    public static function hasContent(): bool
    {
        return true;
    }

    public static function hasTitles(): bool
    {
        return true;
    }

    public static function hasUris(): bool
    {
        return false;
    }

    public static function isLocalized(): bool
    {
        return false;
    }

    public static function hasStatuses(): bool
    {
        return true;
    }

    public static function find(): ElementQueryInterface
    {
        return new NodeQuery(static::class);
    }

    public function rules(): array
    {
        $rules = parent::rules();
        
        $rules[] = [['navId', 'type'], 'required'];
        $rules[] = [['title'], 'required', 'when' => function($model) {
            return $model->type === self::TYPE_CUSTOM;
        }];
        $rules[] = [['customUrl'], 'required', 'when' => function($model) {
            return $model->type === self::TYPE_CUSTOM;
        }];
        $rules[] = [['elementId'], 'required', 'when' => function($model) {
            return in_array($model->type, [self::TYPE_ENTRY, self::TYPE_ASSET, self::TYPE_CATEGORY]);
        }];
        $rules[] = [['type'], 'in', 'range' => [self::TYPE_CUSTOM, self::TYPE_ENTRY, self::TYPE_ASSET, self::TYPE_CATEGORY]];
        $rules[] = [['newWindow', 'enabled'], 'boolean'];
        $rules[] = [['navId', 'elementId', 'elementSiteId'], 'integer'];
        $rules[] = [['classes', 'customUrl'], 'string'];

        return $rules;
    }

    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), [
            'navId' => \Craft::t('simple-menu', 'Navigation'),
            'type' => \Craft::t('simple-menu', 'Link Type'),
            'elementId' => \Craft::t('simple-menu', 'Link To'),
            'customUrl' => \Craft::t('simple-menu', 'URL'),
            'classes' => \Craft::t('simple-menu', 'CSS Classes'),
            'newWindow' => \Craft::t('simple-menu', 'Open in new window'),
        ]);
    }

    public function getLinkedElement()
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

        $element = $this->getLinkedElement();
        return $element ? $element->getUrl() : null;
    }

    public function getTitle(): ?string
    {
        if ($this->type === self::TYPE_CUSTOM || !$this->elementId) {
            return parent::getTitle();
        }

        $element = $this->getLinkedElement();
        return $element ? $element->title : parent::getTitle();
    }

    public function getNav()
    {
        if (!$this->navId) {
            return null;
        }

        return Plugin::getInstance()->navService->getNavById($this->navId);
    }

    public function getCpEditUrl(): ?string
    {
        $nav = $this->getNav();
        if (!$nav) {
            return null;
        }

        return UrlHelper::cpUrl('simple-menu/' . $nav->id . '/nodes/' . $this->id);
    }

    public function getFieldLayout(): ?\craft\models\FieldLayout
    {
        return null;
    }

    protected function defineRules(): array
    {
        return $this->rules();
    }

    public function beforeSave(bool $isNew): bool
    {
        return parent::beforeSave($isNew);
    }

    public function afterSave(bool $isNew): void
    {
        if (!$isNew) {
            $record = NodeRecord::findOne($this->id);
        } else {
            $record = new NodeRecord();
            $record->id = (int)$this->id;
        }

        $record->navId = (int)$this->navId;
        $record->elementId = $this->elementId;
        $record->elementSiteId = $this->elementSiteId;
        $record->type = $this->type;
        $record->classes = $this->classes;
        $record->customUrl = $this->customUrl;
        $record->newWindow = $this->newWindow;

        $record->save(false);

        parent::afterSave($isNew);
    }

    public function beforeDelete(): bool
    {
        return parent::beforeDelete();
    }

    public function afterDelete(): void
    {
        NodeRecord::deleteAll(['id' => $this->id]);
        parent::afterDelete();
    }
}

// Node Record
use craft\db\ActiveRecord;

class NodeRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%simplemenu_nodes}}';
    }
}