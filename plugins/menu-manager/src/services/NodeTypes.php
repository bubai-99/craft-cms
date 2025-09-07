<?php
namespace mycompany\menumanager\services;

use mycompany\menumanager\base\NodeTypeInterface;
use mycompany\menumanager\events\RegisterNodeTypeEvent;
use mycompany\menumanager\nodetypes\CustomType;
use mycompany\menumanager\nodetypes\PassiveType;
use mycompany\menumanager\nodetypes\SiteType;

use Craft;
use craft\base\Component;
use craft\helpers\Component as ComponentHelper;

class NodeTypes extends Component
{
    // Constants
    // =========================================================================

    public const EVENT_REGISTER_NODE_TYPES = 'registerNodeTypes';


    // Public Methods
    // =========================================================================

    public function init(): void
    {
        parent::init();

        $this->getRegisteredNodeTypes();
    }

    public function getRegisteredNodeTypes(): array
    {
        $nodeTypes = [
            PassiveType::class,
        ];

        if (Craft::$app->getIsMultiSite()) {
            $nodeTypes[] = SiteType::class;
        }

        $event = new RegisterNodeTypeEvent([
            'types' => $nodeTypes,
        ]);

        $this->trigger(self::EVENT_REGISTER_NODE_TYPES, $event);

        $nodeTypes = $event->types;

        // Always add custom node at the end
        $nodeTypes[] = CustomType::class;

        $types = [];

        foreach ($nodeTypes as $type) {
            $types[] = ComponentHelper::createComponent([
                'type' => $type,
            ], NodeTypeInterface::class);
        }

        return $types;
    }

}