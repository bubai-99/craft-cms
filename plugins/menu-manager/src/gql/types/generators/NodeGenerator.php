<?php
namespace mycompany\menumanager\gql\types\generators;

use mycompany\menumanager\MenuManager;
use mycompany\menumanager\elements\Node;
use mycompany\menumanager\gql\interfaces\NodeInterface;
use mycompany\menumanager\gql\types\NodeType;

use Craft;
use craft\gql\base\Generator;
use craft\gql\base\GeneratorInterface;
use craft\gql\base\SingleGeneratorInterface;
use craft\gql\GqlEntityRegistry;
use craft\helpers\Gql as GqlHelper;

class NodeGenerator extends Generator implements GeneratorInterface, SingleGeneratorInterface
{
    // Static Methods
    // =========================================================================

    public static function generateTypes(mixed $context = null): array
    {
        $navs = MenuManager::$plugin->getNavs()->getAllNavs();
        $gqlTypes = [];

        foreach ($navs as $nav) {
            $requiredContexts = Node::gqlScopesByContext($nav);

            if (!GqlHelper::isSchemaAwareOf($requiredContexts)) {
                if (!GqlHelper::canSchema('navigationNavs.all')) {
                    continue;
                }
            }

            $type = static::generateType($nav);
            $gqlTypes[$type->name] = $type;
        }

        return $gqlTypes;
    }

    public static function generateType(mixed $context): mixed
    {
        $typeName = Node::gqlTypeNameByContext($context);

        if ($createdType = GqlEntityRegistry::getEntity($typeName)) {
            return $createdType;
        }

        $contentFieldGqlTypes = self::getContentFields($context);
        $navFields = Craft::$app->getGql()->prepareFieldDefinitions(array_merge(NodeInterface::getFieldDefinitions(), $contentFieldGqlTypes), $typeName);

        return GqlEntityRegistry::createEntity($typeName, new NodeType([
            'name' => $typeName,
            'fields' => function() use ($navFields) {
                return $navFields;
            },
        ]));
    }
}
