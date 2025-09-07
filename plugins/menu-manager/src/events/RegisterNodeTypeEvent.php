<?php
namespace mycompany\menumanager\events;

use yii\base\Event;

class RegisterNodeTypeEvent extends Event
{
    // Properties
    // =========================================================================

    public array $types = [];
}
