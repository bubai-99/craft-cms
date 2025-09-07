<?php

namespace mycompany\simplemenu;

use craft\base\Plugin as BasePlugin;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Elements;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use mycompany\simplemenu\elements\Node;
use yii\base\Event;

class Plugin extends BasePlugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSection = true;

    public function init(): void
    {
        parent::init();
        
        $this->setComponents([
            'navService' => services\NavService::class,
            'nodeService' => services\NodeService::class,
            'menuService' => services\MenuService::class, // Legacy support
        ]);

        // Register CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                // Test route
                $event->rules['simple-menu/test'] = ['template' => 'simple-menu/test'];
                
                // New Verbb-style routes
                $event->rules['simple-menu'] = 'simple-menu/nav/index';
                $event->rules['simple-menu/navs'] = 'simple-menu/nav/index';
                $event->rules['simple-menu/navs/new'] = 'simple-menu/nav/new';
                $event->rules['simple-menu/navs/<id:\d+>'] = 'simple-menu/nav/edit';
                $event->rules['simple-menu/navs/<id:\d+>/delete'] = 'simple-menu/nav/delete';
                $event->rules['simple-menu/navs/<navId:\d+>/nodes'] = 'simple-menu/nav/get-nodes';
                
                // Legacy routes
                $event->rules['simple-menu/new'] = 'simple-menu/menu/new';
                $event->rules['simple-menu/<id:\d+>'] = 'simple-menu/menu/edit';
                $event->rules['simple-menu/<id:\d+>/delete'] = 'simple-menu/menu/delete';
            }
        );

        // Register elements
        Event::on(
            Elements::class,
            Elements::EVENT_REGISTER_ELEMENT_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = Node::class;
            }
        );

        // Register Twig variable
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                $variable = $event->sender;
                $variable->set('navigation', variables\NavigationVariable::class);
                $variable->set('simpleMenu', variables\SimpleMenuVariable::class); // Legacy support
            }
        );
    }

    public function getCpNavItem(): ?array
    {
        return [
            'label' => 'Navigation',
            'url' => 'simple-menu',
        ];
    }

    public function installMigrations(): array
    {
        return [
            new migrations\Install(),
        ];
    }
}