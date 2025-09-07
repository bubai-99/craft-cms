<?php
namespace mycompany\menumanager\fields;

use mycompany\menumanager\MenuManager;
use mycompany\menumanager\gql\arguments\NodeArguments;
use mycompany\menumanager\gql\interfaces\NodeInterface;
use mycompany\menumanager\gql\resolvers\NodeResolver;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Html;

use yii\db\Schema;

use GraphQL\Type\Definition\Type;

class NavigationField extends Field
{
    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('menu-manager', 'Navigation');
    }

    public static function icon(): string
    {
        return '@mycompany/menumanager/icon-mask.svg';
    }

    public static function defaultSelectionLabel(): string
    {
        return Craft::t('menu-manager', 'Select a navigation');
    }

    public static function dbType(): array|string
    {
        return Schema::TYPE_TEXT;
    }


    // Public Methods
    // =========================================================================

    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('menu-manager/_field/settings', [

        ]);
    }

    public function getContentGqlType(): Type|array
    {
        return [
            'name' => $this->handle,
            'type' => Type::listOf(NodeInterface::getType()),
            'args' => NodeArguments::getArguments(),
            'resolve' => NodeResolver::class . '::resolve',
        ];
    }
    

    // Protected Methods
    // =========================================================================

    protected function inputHtml(mixed $value, ?ElementInterface $element, bool $inline): string
    {
        $navs = MenuManager::$plugin->getNavs()->getAllNavs();

        $options = [
            '' => Craft::t('menu-manager', 'Select a navigation'),
        ];

        foreach ($navs as $nav) {
            $options[$nav->handle] = $nav->name;
        }

        $id = Html::id($this->handle);

        return Craft::$app->getView()->renderTemplate('menu-manager/_field/input', [
            'id' => $id,
            'name' => $this->handle,
            'value' => $value,
            'options' => $options,
        ]);
    }

    protected function optionsSettingLabel(): string
    {
        return Craft::t('menu-manager', 'Navigation Options');
    }
}
