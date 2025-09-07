<?php
namespace mycompany\menumanager\events;

use mycompany\menumanager\elements\Node;

use yii\base\Event;

class NodeEvent extends Event
{
    // Properties
    // =========================================================================

    public Node $node;
    public bool $isNew = false;
}
