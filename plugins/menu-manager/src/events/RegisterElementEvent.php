<?php
namespace mycompany\menumanager\events;

use yii\base\Event;

class RegisterElementEvent extends Event
{
    // Properties
    // =========================================================================

    public array $elements = [];
}
