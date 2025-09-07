<?php
namespace mycompany\menumanager;

use mycompany\menumanager\base\PluginTrait;
use mycompany\menumanager\elements\Node;
use mycompany\menumanager\fields\NavigationField;
use mycompany\menumanager\fieldlayoutelements\ClassesField;
use mycompany\menumanager\fieldlayoutelements\CustomAttributesField;
use mycompany\menumanager\fieldlayoutelements\NewWindowField;
use mycompany\menumanager\fieldlayoutelements\NodeTypeElements;
use mycompany\menumanager\fieldlayoutelements\UrlSuffixField;
use mycompany\menumanager\gql\interfaces\NodeInterface;
use mycompany\menumanager\gql\queries\NodeQuery;
use mycompany\menumanager\helpers\Gql as GqlHelper;
use mycompany\menumanager\helpers\ProjectConfigData;
use mycompany\menumanager\integrations\NodeFeedMeElement;
use mycompany\menumanager\models\Settings;
use mycompany\menumanager\services\Navs;
use mycompany\menumanager\twigextensions\Extension;
use mycompany\menumanager\variables\NavigationVariable;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\console\Application as ConsoleApplication;
use craft\console\Controller as ConsoleController;
use craft\console\controllers\ResaveController;
use craft\events\ConfigEvent;
use craft\events\DefineConsoleActionsEvent;
use craft\events\DefineFieldLayoutFieldsEvent;
use craft\events\RebuildConfigEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterGqlQueriesEvent;
use craft\events\RegisterGqlSchemaComponentsEvent;
use craft\events\RegisterGqlTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\fieldlayoutelements\TitleField;
use craft\helpers\Cp;
use craft\helpers\UrlHelper;
use craft\models\FieldLayout;
use craft\services\Elements;
use craft\services\Fields;
use craft\services\Gql;
use craft\services\ProjectConfig;
use craft\services\Sites;
use craft\services\Structures;
use craft\services\UserPermissions;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;

use craft\feedme\events\RegisterFeedMeElementsEvent;
use craft\feedme\services\Elements as FeedMeElements;

use craft\gatsbyhelper\events\RegisterSourceNodeTypesEvent;
use craft\gatsbyhelper\services\SourceNodes;

class MenuManager extends Plugin
{
    // Properties
    // =========================================================================

    public bool $hasCpSection = true;
    public bool $hasCpSettings = true;
    public string $schemaVersion = '2.1.1';
    public string $minVersionRequired = '1.4.24';


    // Traits
    // =========================================================================

    use PluginTrait;


    // Public Methods
    // =========================================================================

    public function init(): void
    {
        parent::init();

        self::$plugin = $this;

        $this->_registerVariables();
        $this->_registerEventHandlers();
        $this->_registerProjectConfigEventHandlers();
        $this->_registerFieldTypes();
        $this->_registerElementTypes();
        $this->_registerGraphQl();
        $this->_registerFeedMeSupport();

        if (Craft::$app->getRequest()->getIsCpRequest()) {
            $this->_registerCpRoutes();
            $this->_registerFieldLayoutListener();
        }

        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->_registerResaveCommand();
        }

        if (Craft::$app->getEdition() !== Craft::Solo) {
            $this->_registerPermissions();
        }
    }

    public function getPluginName(): string
    {
        return Craft::t('menu-manager', $this->getSettings()->pluginName);
    }

    public function getSettingsResponse(): mixed
    {
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('menu-manager/settings'));
    }

    public function getCpNavItem(): ?array
    {
        $nav = parent::getCpNavItem();
        $nav['label'] = $this->getPluginName();

        if (Craft::$app->getUser()->getIsAdmin() && Craft::$app->getConfig()->getGeneral()->allowAdminChanges) {
            $nav['subnav']['settings'] = [
                'label' => Craft::t('menu-manager', 'Settings'),
                'url' => 'menu-manager/settings',
            ];
        }

        return $nav;
    }


    // Protected Methods
    // =========================================================================

    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }


    // Private Methods
    // =========================================================================

    private function _registerCpRoutes(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules = array_merge($event->rules, [
                'menu-manager' => 'menu-manager/navs/index',
                'menu-manager/navs' => 'menu-manager/navs/index',
                'menu-manager/navs/new' => 'menu-manager/navs/edit-nav',
                'menu-manager/navs/edit/<navId:\d+>' => 'menu-manager/navs/edit-nav',
                'menu-manager/navs/build/<navId:\d+>' => 'menu-manager/navs/build-nav',
                'menu-manager/settings' => 'menu-manager/base/settings',
            ]);
        });
    }

    private function _registerVariables(): void
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            $event->sender->set('menuManager', NavigationVariable::class);
        });
    }

    private function _registerEventHandlers(): void
    {
        // Allow elements to update our nodes
        Event::on(Elements::class, Elements::EVENT_BEFORE_SAVE_ELEMENT, [$this->getNodes(), 'onSaveElement']);
        Event::on(Elements::class, Elements::EVENT_AFTER_DELETE_ELEMENT, [$this->getNodes(), 'onDeleteElement']);

        // Prune deleted fields from nav
        Event::on(Fields::class, Fields::EVENT_AFTER_DELETE_FIELD, [$this->getNavs(), 'pruneDeletedField']);

        // Prune deleted sites from site settings
        Event::on(Sites::class, Sites::EVENT_AFTER_DELETE_SITE, [$this->getNavs(), 'pruneDeletedSite']);

        // Handle validation of max levels when dragging items across levels in structure
        Event::on(Structures::class, Structures::EVENT_BEFORE_MOVE_ELEMENT, [$this->getNodes(), 'onMoveElement']);
    }

    private function _registerProjectConfigEventHandlers(): void
    {
        Craft::$app->getProjectConfig()
            ->onAdd(Navs::CONFIG_NAV_KEY . '.{uid}', [$this->getNavs(), 'handleChangedNav'])
            ->onUpdate(Navs::CONFIG_NAV_KEY . '.{uid}', [$this->getNavs(), 'handleChangedNav'])
            ->onRemove(Navs::CONFIG_NAV_KEY . '.{uid}', [$this->getNavs(), 'handleDeletedNav']);

        Event::on(ProjectConfig::class, ProjectConfig::EVENT_REBUILD, function(RebuildConfigEvent $event) {
            $event->config['navigation'] = ProjectConfigData::rebuildProjectConfig();
        });
    }

    private function _registerFieldTypes(): void
    {
        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = NavigationField::class;
        });
    }

    private function _registerElementTypes(): void
    {
        Event::on(Elements::class, Elements::EVENT_REGISTER_ELEMENT_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = Node::class;
        });
    }

    private function _registerPermissions(): void
    {
        Event::on(UserPermissions::class, UserPermissions::EVENT_REGISTER_PERMISSIONS, function(RegisterUserPermissionsEvent $event) {
            $navs = $this->getNavs()->getAllNavs();

            $navPermissions = [];

            $navPermissions['navigation-createNavs'] = [
                'label' => Craft::t('menu-manager', 'Create navigations'),
            ];

            foreach ($navs as $nav) {
                $navPermissions['navigation-manageNav:' . $nav->uid] = [
                    'label' => Craft::t('menu-manager', 'Manage “{type}”', ['type' => $nav->name]),
                    'nested' => [
                        'navigation-editNav:' . $nav->uid => [
                            'label' => Craft::t('menu-manager', 'Edit navigation settings'),
                        ],
                        'navigation-deleteNav:' . $nav->uid => [
                            'label' => Craft::t('menu-manager', 'Delete navigation'),
                        ],
                    ],
                ];
            }

            $event->permissions[] = [
                'heading' => Craft::t('menu-manager', 'Navigation'),
                'permissions' => $navPermissions,
            ];
        });
    }

    private function _registerGraphQl(): void
    {
        Event::on(Gql::class, Gql::EVENT_REGISTER_GQL_TYPES, function(RegisterGqlTypesEvent $event) {
            $event->types[] = NodeInterface::class;
        });

        Event::on(Gql::class, Gql::EVENT_REGISTER_GQL_QUERIES, function(RegisterGqlQueriesEvent $event) {
            $queries = NodeQuery::getQueries();

            foreach ($queries as $key => $value) {
                $event->queries[$key] = $value;
            }
        });

        Event::on(Gql::class, Gql::EVENT_REGISTER_GQL_SCHEMA_COMPONENTS, function(RegisterGqlSchemaComponentsEvent $event) {
            $navs = MenuManager::$plugin->getNavs()->getAllNavs();

            if (!empty($navs)) {
                $label = Craft::t('menu-manager', 'Navigation');
                $event->queries[$label]['navigationNavs.all:read'] = ['label' => Craft::t('menu-manager', 'View all navigations')];

                foreach ($navs as $nav) {
                    $suffix = 'navigationNavs.' . $nav->uid;

                    $event->queries[$label][$suffix . ':read'] = [
                        'label' => Craft::t('menu-manager', 'View navigation - {nav}', ['nav' => Craft::t('site', $nav->name)]),
                    ];
                }
            }
        });

        if (class_exists(SourceNodes::class)) {
            Event::on(SourceNodes::class, SourceNodes::EVENT_REGISTER_SOURCE_NODE_TYPES, function(RegisterSourceNodeTypesEvent $event) {
                if (GqlHelper::canQueryNavigation()) {
                    $event->types[NodeInterface::getName()] = [
                        'node' => 'navigationNode',
                        'list' => 'navigationNodes',
                        'filterArgument' => '',
                        'filterTypeExpression' => '(.+)_Node',
                        'targetInterface' => NodeInterface::getName(),
                    ];
                }
            });
        }
    }

    private function _registerFeedMeSupport(): void
    {
        if (class_exists(FeedMeElements::class)) {
            Event::on(FeedMeElements::class, FeedMeElements::EVENT_REGISTER_FEED_ME_ELEMENTS, function(RegisterFeedMeElementsEvent $event) {
                $event->elements[] = NodeFeedMeElement::class;
            });
        }
    }

    private function _registerResaveCommand()
    {
        if (!Craft::$app instanceof ConsoleApplication) {
            return;
        }
        
        Event::on(ResaveController::class, ConsoleController::EVENT_DEFINE_ACTIONS, function(DefineConsoleActionsEvent $event) {
            $event->actions['navigation-nodes'] = [
                'action' => function(): int {
                    $controller = Craft::$app->controller;

                    $criteria = [];

                    if ($controller->navId !== null) {
                        $criteria['navId'] = explode(',', $controller->navId);
                    }

                    return $controller->resaveElements(Node::class, $criteria);
                },
                'options' => ['navId'],
                'helpSummary' => 'Re-saves Navigation nodes.',
                'optionsHelp' => [
                    'type' => 'The nav ID of the nodes to resave.',
                ],
            ];
        });
    }

    private function _registerFieldLayoutListener(): void
    {
        Event::on(FieldLayout::class, FieldLayout::EVENT_DEFINE_NATIVE_FIELDS, function(DefineFieldLayoutFieldsEvent $event) {
            if ($event->sender->type === Node::class) {
                $event->fields[] = TitleField::class;
                $event->fields[] = UrlSuffixField::class;
                $event->fields[] = ClassesField::class;
                $event->fields[] = NewWindowField::class;
                $event->fields[] = CustomAttributesField::class;
                $event->fields[] = NodeTypeElements::class;
            }
        });
    }
}
