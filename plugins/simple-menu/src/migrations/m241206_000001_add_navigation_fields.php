<?php

namespace mycompany\simplemenu\migrations;

use craft\db\Migration;

class m241206_000001_add_navigation_fields extends Migration
{
    public function safeUp(): bool
    {
        // Add new columns to existing simplemenu_menus table
        $this->addColumn('{{%simplemenu_menus}}', 'nodes', $this->longText()->after('items'));
        $this->addColumn('{{%simplemenu_menus}}', 'maxLevels', $this->integer()->null()->after('nodes'));
        $this->addColumn('{{%simplemenu_menus}}', 'settings', $this->text()->after('maxLevels'));

        // Create navigation nodes table
        $this->createTable('{{%simplemenu_nodes}}', [
            'id' => $this->primaryKey(),
            'navigationId' => $this->integer()->notNull(),
            'parentId' => $this->integer(),
            'elementId' => $this->integer(),
            'elementSiteId' => $this->integer(),
            'type' => $this->string(50)->notNull()->defaultValue('custom'),
            'title' => $this->string(255),
            'url' => $this->string(255),
            'customUrl' => $this->string(255),
            'classes' => $this->string(255),
            'newWindow' => $this->boolean()->defaultValue(false),
            'enabled' => $this->boolean()->defaultValue(true),
            'lft' => $this->integer()->notNull(),
            'rgt' => $this->integer()->notNull(),
            'level' => $this->smallInteger()->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        // Add indexes
        $this->createIndex(null, '{{%simplemenu_nodes}}', 'navigationId');
        $this->createIndex(null, '{{%simplemenu_nodes}}', 'parentId');
        $this->createIndex(null, '{{%simplemenu_nodes}}', 'elementId');
        $this->createIndex(null, '{{%simplemenu_nodes}}', ['lft', 'rgt']);

        // Add foreign keys
        $this->addForeignKey(null, '{{%simplemenu_nodes}}', 'navigationId', '{{%simplemenu_menus}}', 'id', 'CASCADE');
        $this->addForeignKey(null, '{{%simplemenu_nodes}}', 'parentId', '{{%simplemenu_nodes}}', 'id', 'CASCADE');

        return true;
    }

    public function safeDown(): bool
    {
        $this->dropTableIfExists('{{%simplemenu_nodes}}');
        $this->dropColumn('{{%simplemenu_menus}}', 'settings');
        $this->dropColumn('{{%simplemenu_menus}}', 'maxLevels');
        $this->dropColumn('{{%simplemenu_menus}}', 'nodes');

        return true;
    }
}