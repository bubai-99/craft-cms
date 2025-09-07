<?php
namespace mycompany\menumanager\events;

use mycompany\menumanager\models\Nav;

use yii\base\Event;

class NavEvent extends Event
{
    // Properties
    // =========================================================================

    public Nav $nav;
    public bool $isNew = false;
}
