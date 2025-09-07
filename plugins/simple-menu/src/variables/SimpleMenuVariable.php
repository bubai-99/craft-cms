<?php

namespace mycompany\simplemenu\variables;

use mycompany\simplemenu\Plugin;
use mycompany\simplemenu\models\Menu;

class SimpleMenuVariable
{
    public function getMenuByHandle(string $handle): ?Menu
    {
        return Plugin::getInstance()->menuService->getMenuByHandle($handle);
    }

    public function getAllMenus(): array
    {
        return Plugin::getInstance()->menuService->getAllMenus();
    }

    public function render(Menu $menu, array $options = []): string
    {
        return Plugin::getInstance()->menuService->renderMenu($menu->handle, $options);
    }

    public function renderNodes(array $nodes, array $options = []): string
    {
        $template = $options['template'] ?? 'simple-menu/_render_nodes.twig';
        
        return \Craft::$app->view->renderTemplate($template, [
            'nodes' => $nodes,
            'options' => $options,
        ]);
    }
}