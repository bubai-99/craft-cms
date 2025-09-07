<?php

namespace mycompany\simplemenu\variables;

use mycompany\simplemenu\Plugin;
use mycompany\simplemenu\models\Nav;
use mycompany\simplemenu\elements\Node;

class NavigationVariable
{
    /**
     * Get navigation by handle
     */
    public function getNavByHandle(string $handle): ?Nav
    {
        return Plugin::getInstance()->navService->getNavByHandle($handle);
    }

    /**
     * Get all navigations
     */
    public function getAllNavs(): array
    {
        return Plugin::getInstance()->navService->getAllNavs();
    }

    /**
     * Render navigation
     */
    public function render(string $handle, array $options = []): string
    {
        return Plugin::getInstance()->navService->render($handle, $options);
    }

    /**
     * Get nodes for navigation
     */
    public function getNodes(Nav $nav): array
    {
        return $nav->getNodes();
    }

    /**
     * Get nodes tree for navigation
     */
    public function getTree(Nav $nav): array
    {
        return $nav->getNodesTree();
    }

    /**
     * Get node query
     */
    public function nodes(): \craft\elements\db\ElementQueryInterface
    {
        return Node::find();
    }

    // Legacy methods for backward compatibility
    public function getMenuByHandle(string $handle): ?Nav
    {
        return $this->getNavByHandle($handle);
    }

    public function getAllMenus(): array
    {
        return $this->getAllNavs();
    }
}