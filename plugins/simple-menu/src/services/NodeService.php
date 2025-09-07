<?php

namespace mycompany\simplemenu\services;

use craft\base\Component;
use craft\db\Query;
use craft\helpers\Db;
use craft\helpers\StringHelper;
use mycompany\simplemenu\models\Node;

class NodeService extends Component
{
    public function getNodesByNavId(int $navId): array
    {
        // For now, return nodes from legacy items data or empty array
        // This will be expanded when we implement full Node elements
        return [];
    }

    public function getNodesTreeByNavId(int $navId): array
    {
        $nodes = $this->getNodesByNavId($navId);
        return $this->buildTreeStructure($nodes);
    }

    public function deleteNodesByNavId(int $navId): bool
    {
        // Delete all nodes for this navigation
        return true; // Placeholder for now
    }

    public function getNodesByNavigationId(int $navigationId): array
    {
        $results = (new Query())
            ->select(['*'])
            ->from(['{{%simplemenu_nodes}}'])
            ->where(['navigationId' => $navigationId])
            ->orderBy(['lft' => SORT_ASC])
            ->all();

        $nodes = [];
        foreach ($results as $result) {
            $nodes[] = $this->createNodeFromResult($result);
        }

        return $nodes;
    }

    public function getNodeById(int $id): ?Node
    {
        $result = (new Query())
            ->select(['*'])
            ->from(['{{%simplemenu_nodes}}'])
            ->where(['id' => $id])
            ->one();

        return $result ? $this->createNodeFromResult($result) : null;
    }

    public function saveNode(Node $node): bool
    {
        if (!$node->validate()) {
            return false;
        }

        $data = [
            'navigationId' => $node->navigationId,
            'parentId' => $node->parentId,
            'elementId' => $node->elementId,
            'elementSiteId' => $node->elementSiteId,
            'type' => $node->type,
            'title' => $node->title,
            'url' => $node->url,
            'customUrl' => $node->customUrl,
            'classes' => $node->classes,
            'newWindow' => $node->newWindow ? 1 : 0,
            'enabled' => $node->enabled ? 1 : 0,
            'lft' => $node->lft,
            'rgt' => $node->rgt,
            'level' => $node->level,
            'dateUpdated' => Db::prepareDateForDb(new \DateTime()),
        ];

        if ($node->id) {
            \Craft::$app->db->createCommand()
                ->update('{{%simplemenu_nodes}}', $data, ['id' => $node->id])
                ->execute();
        } else {
            $data['dateCreated'] = Db::prepareDateForDb(new \DateTime());
            $data['uid'] = StringHelper::UUID();

            \Craft::$app->db->createCommand()
                ->insert('{{%simplemenu_nodes}}', $data)
                ->execute();

            $node->id = (int)\Craft::$app->db->getLastInsertID();
        }

        return true;
    }

    public function deleteNode(Node $node): bool
    {
        if (!$node->id) {
            return false;
        }

        \Craft::$app->db->createCommand()
            ->delete('{{%simplemenu_nodes}}', ['id' => $node->id])
            ->execute();

        return true;
    }

    public function buildTreeStructure(array $nodes, ?int $parentId = null): array
    {
        $tree = [];
        
        foreach ($nodes as $node) {
            if ($node->parentId == $parentId) {
                $node->children = $this->buildTreeStructure($nodes, $node->id);
                $tree[] = $node;
            }
        }
        
        return $tree;
    }

    public function getNodesAsTree(int $navigationId): array
    {
        $nodes = $this->getNodesByNavigationId($navigationId);
        return $this->buildTreeStructure($nodes);
    }

    private function createNodeFromResult(array $result): Node
    {
        $node = new Node();
        $node->id = (int)$result['id'];
        $node->navigationId = (int)$result['navigationId'];
        $node->parentId = $result['parentId'] ? (int)$result['parentId'] : null;
        $node->elementId = $result['elementId'] ? (int)$result['elementId'] : null;
        $node->elementSiteId = $result['elementSiteId'] ? (int)$result['elementSiteId'] : null;
        $node->type = $result['type'];
        $node->title = $result['title'];
        $node->url = $result['url'];
        $node->customUrl = $result['customUrl'];
        $node->classes = $result['classes'];
        $node->newWindow = (bool)$result['newWindow'];
        $node->enabled = (bool)$result['enabled'];
        $node->lft = (int)$result['lft'];
        $node->rgt = (int)$result['rgt'];
        $node->level = (int)$result['level'];
        $node->dateCreated = new \DateTime($result['dateCreated']);
        $node->dateUpdated = new \DateTime($result['dateUpdated']);
        $node->uid = $result['uid'];

        return $node;
    }
}