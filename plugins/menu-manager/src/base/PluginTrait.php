<?php
namespace mycompany\menumanager\base;

use mycompany\menumanager\MenuManager;
use mycompany\menumanager\services\Breadcrumbs;
use mycompany\menumanager\services\Elements;
use mycompany\menumanager\services\Navs;
use mycompany\menumanager\services\Nodes;
use mycompany\menumanager\services\NodeTypes;

use mycompany\menumanager\base\LogTrait;
use mycompany\menumanager\helpers\Plugin;

trait PluginTrait
{
    // Properties
    // =========================================================================

    public static ?MenuManager $plugin = null;


    // Traits
    // =========================================================================

    use LogTrait;
    

    // Static Methods
    // =========================================================================

    public static function config(): array
    {
        Plugin::bootstrapPlugin('menu-manager');

        return [
            'components' => [
                'breadcrumbs' => Breadcrumbs::class,
                'elements' => Elements::class,
                'navs' => Navs::class,
                'nodes' => Nodes::class,
                'nodeTypes' => NodeTypes::class,
            ],
        ];
    }


    // Public Methods
    // =========================================================================

    public function getBreadcrumbs(): Breadcrumbs
    {
        return $this->get('breadcrumbs');
    }

    public function getElements(): Elements
    {
        return $this->get('elements');
    }

    public function getNavs(): Navs
    {
        return $this->get('navs');
    }

    public function getNodes(): Nodes
    {
        return $this->get('nodes');
    }

    public function getNodeTypes(): NodeTypes
    {
        return $this->get('nodeTypes');
    }

}