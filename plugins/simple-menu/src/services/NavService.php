<?php

namespace mycompany\simplemenu\services;

use craft\base\Component;
use craft\db\Query;
use craft\helpers\Db;
use craft\helpers\StringHelper;
use mycompany\simplemenu\models\Nav;
use mycompany\simplemenu\Plugin;
use yii\base\Event;

class NavService extends Component
{
    // Events
    const EVENT_BEFORE_SAVE_NAV = 'beforeSaveNav';
    const EVENT_AFTER_SAVE_NAV = 'afterSaveNav';
    const EVENT_BEFORE_DELETE_NAV = 'beforeDeleteNav';
    const EVENT_AFTER_DELETE_NAV = 'afterDeleteNav';

    public function getAllNavs(): array
    {
        $results = (new Query())
            ->select(['*'])
            ->from(['{{%simplemenu_navs}}'])
            ->orderBy(['sortOrder' => SORT_ASC, 'name' => SORT_ASC])
            ->all();

        $navs = [];
        foreach ($results as $result) {
            $navs[] = $this->createNavFromResult($result);
        }

        return $navs;
    }

    public function getNavById(int $id): ?Nav
    {
        $result = (new Query())
            ->select(['*'])
            ->from(['{{%simplemenu_navs}}'])
            ->where(['id' => $id])
            ->one();

        return $result ? $this->createNavFromResult($result) : null;
    }

    public function getNavByHandle(string $handle): ?Nav
    {
        $result = (new Query())
            ->select(['*'])
            ->from(['{{%simplemenu_navs}}'])
            ->where(['handle' => $handle])
            ->one();

        return $result ? $this->createNavFromResult($result) : null;
    }

    public function saveNav(Nav $nav, bool $runValidation = true): bool
    {
        $isNew = !$nav->id;

        // Fire before save event
        if ($this->hasEventHandlers(self::EVENT_BEFORE_SAVE_NAV)) {
            $this->trigger(self::EVENT_BEFORE_SAVE_NAV, new NavEvent([
                'nav' => $nav,
                'isNew' => $isNew,
            ]));
        }

        if ($runValidation && !$nav->validate()) {
            return false;
        }

        // Auto-generate handle if empty
        if (empty($nav->handle) && !empty($nav->name)) {
            $nav->handle = StringHelper::camelCase($nav->name);
        }

        $data = [
            'name' => $nav->name,
            'handle' => $nav->handle,
            'instructions' => $nav->instructions,
            'maxLevels' => $nav->maxLevels,
            'sortOrder' => $nav->sortOrder ?? 1,
            'dateUpdated' => Db::prepareDateForDb(new \DateTime()),
        ];

        if ($nav->id) {
            \Craft::$app->db->createCommand()
                ->update('{{%simplemenu_navs}}', $data, ['id' => $nav->id])
                ->execute();
        } else {
            $data['dateCreated'] = Db::prepareDateForDb(new \DateTime());
            $data['uid'] = StringHelper::UUID();

            \Craft::$app->db->createCommand()
                ->insert('{{%simplemenu_navs}}', $data)
                ->execute();

            $nav->id = (int)\Craft::$app->db->getLastInsertID();
        }

        // Fire after save event
        if ($this->hasEventHandlers(self::EVENT_AFTER_SAVE_NAV)) {
            $this->trigger(self::EVENT_AFTER_SAVE_NAV, new NavEvent([
                'nav' => $nav,
                'isNew' => $isNew,
            ]));
        }

        return true;
    }

    public function deleteNav(Nav $nav): bool
    {
        if (!$nav->id) {
            return false;
        }

        // Fire before delete event
        if ($this->hasEventHandlers(self::EVENT_BEFORE_DELETE_NAV)) {
            $this->trigger(self::EVENT_BEFORE_DELETE_NAV, new NavEvent([
                'nav' => $nav,
            ]));
        }

        // Delete all nodes for this nav
        Plugin::getInstance()->nodeService->deleteNodesByNavId($nav->id);

        // Delete the nav
        \Craft::$app->db->createCommand()
            ->delete('{{%simplemenu_navs}}', ['id' => $nav->id])
            ->execute();

        // Fire after delete event
        if ($this->hasEventHandlers(self::EVENT_AFTER_DELETE_NAV)) {
            $this->trigger(self::EVENT_AFTER_DELETE_NAV, new NavEvent([
                'nav' => $nav,
            ]));
        }

        return true;
    }

    public function render(string $handle, array $options = []): string
    {
        $nav = $this->getNavByHandle($handle);
        if (!$nav) {
            return '';
        }

        $nodes = $nav->getNodesTree();
        
        $template = $options['template'] ?? 'simple-menu/_render';
        
        return \Craft::$app->view->renderTemplate($template, [
            'nav' => $nav,
            'nodes' => $nodes,
            'options' => $options,
        ]);
    }

    private function createNavFromResult(array $result): Nav
    {
        $nav = new Nav();
        $nav->id = (int)$result['id'];
        $nav->name = $result['name'];
        $nav->handle = $result['handle'];
        $nav->instructions = $result['instructions'];
        $nav->maxLevels = $result['maxLevels'] ? (int)$result['maxLevels'] : null;
        $nav->sortOrder = (int)$result['sortOrder'];
        $nav->dateCreated = new \DateTime($result['dateCreated']);
        $nav->dateUpdated = new \DateTime($result['dateUpdated']);
        $nav->uid = $result['uid'];

        return $nav;
    }
}

// Event class
class NavEvent extends Event
{
    public Nav $nav;
    public bool $isNew = false;
}