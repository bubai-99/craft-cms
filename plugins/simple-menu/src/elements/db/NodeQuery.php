<?php

namespace mycompany\simplemenu\elements\db;

use craft\elements\db\ElementQuery;
use craft\helpers\Db;

class NodeQuery extends ElementQuery
{
    public $navId;
    public $type;
    public $elementId;

    public function navId($value): self
    {
        $this->navId = $value;
        return $this;
    }

    public function type($value): self
    {
        $this->type = $value;
        return $this;
    }

    public function elementId($value): self
    {
        $this->elementId = $value;
        return $this;
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('simplemenu_nodes');

        $this->query->select([
            'simplemenu_nodes.navId',
            'simplemenu_nodes.elementId',
            'simplemenu_nodes.elementSiteId',
            'simplemenu_nodes.type',
            'simplemenu_nodes.classes',
            'simplemenu_nodes.customUrl',
            'simplemenu_nodes.newWindow',
        ]);

        if ($this->navId) {
            $this->subQuery->andWhere(Db::parseParam('simplemenu_nodes.navId', $this->navId));
        }

        if ($this->type) {
            $this->subQuery->andWhere(Db::parseParam('simplemenu_nodes.type', $this->type));
        }

        if ($this->elementId) {
            $this->subQuery->andWhere(Db::parseParam('simplemenu_nodes.elementId', $this->elementId));
        }

        return parent::beforePrepare();
    }

    protected function statusCondition(string $status): mixed
    {
        return match ($status) {
            'enabled' => [
                'elements.enabled' => true,
            ],
            'disabled' => [
                'elements.enabled' => false,
            ],
            default => parent::statusCondition($status),
        };
    }
}