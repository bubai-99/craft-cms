<?php

namespace mycompany\simplemenu\services;

use craft\base\Component;
use craft\db\Query;
use craft\helpers\Db;
use craft\helpers\Json;
use craft\helpers\StringHelper;
use mycompany\simplemenu\models\Menu;

class MenuService extends Component
{
    public function getAllMenus(): array
    {
        // Try new table first, fallback to old
        $tableName = $this->getTableName();
        
        $results = (new Query())
            ->select(['*'])
            ->from([$tableName])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        $menus = [];
        foreach ($results as $result) {
            $menus[] = $this->createMenuFromResult($result);
        }

        return $menus;
    }

    public function getMenuById(int $id): ?Menu
    {
        $tableName = $this->getTableName();
        
        $result = (new Query())
            ->select(['*'])
            ->from([$tableName])
            ->where(['id' => $id])
            ->one();

        return $result ? $this->createMenuFromResult($result) : null;
    }

    public function getMenuByHandle(string $handle): ?Menu
    {
        $tableName = $this->getTableName();
        
        $result = (new Query())
            ->select(['*'])
            ->from([$tableName])
            ->where(['handle' => $handle])
            ->one();

        return $result ? $this->createMenuFromResult($result) : null;
    }

    public function saveMenu(Menu $menu): bool
    {
        if (!$menu->validate()) {
            return false;
        }

        // Auto-generate handle if empty
        if (empty($menu->handle) && !empty($menu->name)) {
            $menu->handle = StringHelper::camelCase($menu->name);
        }

        $tableName = $this->getTableName();
        
        $data = [
            'name' => $menu->name,
            'handle' => $menu->handle,
            'items' => Json::encode($menu->items),
            'dateUpdated' => Db::prepareDateForDb(new \DateTime()),
        ];

        // Add fields that exist in navs table
        if (strpos($tableName, 'navs') !== false) {
            $data['nodes'] = Json::encode($menu->nodes ?? []);
            $data['maxLevels'] = $menu->maxLevels;
            $data['settings'] = Json::encode($menu->settings ?? []);
        }

        if ($menu->id) {
            \Craft::$app->db->createCommand()
                ->update($tableName, $data, ['id' => $menu->id])
                ->execute();
        } else {
            $data['dateCreated'] = Db::prepareDateForDb(new \DateTime());
            $data['uid'] = StringHelper::UUID();

            \Craft::$app->db->createCommand()
                ->insert($tableName, $data)
                ->execute();

            $menu->id = (int)\Craft::$app->db->getLastInsertID();
        }

        return true;
    }

    public function deleteMenu(Menu $menu): bool
    {
        if (!$menu->id) {
            return false;
        }

        $tableName = $this->getTableName();
        
        \Craft::$app->db->createCommand()
            ->delete($tableName, ['id' => $menu->id])
            ->execute();

        return true;
    }

    public function renderMenu(string $handle, array $options = []): string
    {
        $menu = $this->getMenuByHandle($handle);
        if (!$menu) {
            return '';
        }

        // Get navigation nodes
        $nodes = \mycompany\simplemenu\Plugin::getInstance()->nodeService->getNodesAsTree($menu->id);
        
        $template = $options['template'] ?? 'simple-menu/_render_navigation.twig';
        
        return \Craft::$app->view->renderTemplate($template, [
            'menu' => $menu,
            'nodes' => $nodes,
            'options' => $options,
        ]);
    }

    public function getMenuNodes(Menu $menu): array
    {
        return \mycompany\simplemenu\Plugin::getInstance()->nodeService->getNodesAsTree($menu->id);
    }

    private function createMenuFromResult(array $result): Menu
    {
        $menu = new Menu();
        $menu->id = (int)$result['id'];
        $menu->name = $result['name'];
        $menu->handle = $result['handle'];
        $menu->items = Json::decode($result['items'] ?? '[]') ?: [];
        $menu->nodes = Json::decode($result['nodes'] ?? '[]') ?: [];
        $menu->maxLevels = $result['maxLevels'] ? (int)$result['maxLevels'] : null;
        $menu->settings = Json::decode($result['settings'] ?? '[]') ?: [];
        $menu->dateCreated = new \DateTime($result['dateCreated']);
        $menu->dateUpdated = new \DateTime($result['dateUpdated']);
        $menu->uid = $result['uid'];

        return $menu;
    }

    private function getTableName(): string
    {
        // Check if new navs table exists, otherwise use old menus table
        $db = \Craft::$app->db;
        if ($db->tableExists('{{%simplemenu_navs}}')) {
            return '{{%simplemenu_navs}}';
        }
        return '{{%simplemenu_menus}}';
    }
}