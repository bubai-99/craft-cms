<?php

namespace mycompany\simplemenu\migrations;

use craft\db\Migration;

class m241206_000002_create_nav_structure extends Migration
{
    public function safeUp(): bool
    {
        // Drop old tables if they exist
        $this->dropTableIfExists('{{%simplemenu_nodes}}');
        
        // Rename menus table to navs
        if ($this->db->tableExists('{{%simplemenu_menus}}')) {
            $this->renameTable('{{%simplemenu_menus}}', '{{%simplemenu_navs}}');
        } else {
            $this->createTable('{{%simplemenu_navs}}', [
                'id' => $this->primaryKey(),
                'name' => $this->string(255)->notNull(),
                'handle' => $this->string(255)->notNull(),
                'instructions' => $this->text(),
                'sortOrder' => $this->integer()->defaultValue(1),
                'maxLevels' => $this->integer(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
            ]);
        }

        // Add new columns to navs table if they don't exist
        if (!$this->db->columnExists('{{%simplemenu_navs}}', 'instructions')) {
            $this->addColumn('{{%simplemenu_navs}}', 'instructions', $this->text()->after('handle'));
        }
        
        if (!$this->db->columnExists('{{%simplemenu_navs}}', 'sortOrder')) {
            $this->addColumn('{{%simplemenu_navs}}', 'sortOrder', $this->integer()->defaultValue(1)->after('instructions'));
        }

        // Create nodes table as element table
        $this->createTable('{{%simplemenu_nodes}}', [
            'id' => $this->integer()->notNull(),
            'navId' => $this->integer()->notNull(),
            'elementId' => $this->integer(),
            'elementSiteId' => $this->integer(),
            'type' => $this->string(50)->notNull()->defaultValue('custom'),
            'classes' => $this->string(255),
            'customUrl' => $this->string(255),
            'newWindow' => $this->boolean()->defaultValue(false),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'PRIMARY KEY(id)',
        ]);

        // Add indexes and foreign keys
        $this->createIndex(null, '{{%simplemenu_navs}}', 'handle', true);
        $this->createIndex(null, '{{%simplemenu_navs}}', 'sortOrder');
        
        $this->createIndex(null, '{{%simplemenu_nodes}}', 'navId');
        $this->createIndex(null, '{{%simplemenu_nodes}}', 'elementId');
        $this->createIndex(null, '{{%simplemenu_nodes}}', 'type');
        
        $this->addForeignKey(null, '{{%simplemenu_nodes}}', 'id', '{{%elements}}', 'id', 'CASCADE');
        $this->addForeignKey(null, '{{%simplemenu_nodes}}', 'navId', '{{%simplemenu_navs}}', 'id', 'CASCADE');

        return true;
    }

    public function safeDown(): bool
    {
        $this->dropTableIfExists('{{%simplemenu_nodes}}');
        
        if ($this->db->tableExists('{{%simplemenu_navs}}')) {
            $this->renameTable('{{%simplemenu_navs}}', '{{%simplemenu_menus}}');
            
            // Remove new columns
            if ($this->db->columnExists('{{%simplemenu_menus}}', 'instructions')) {
                $this->dropColumn('{{%simplemenu_menus}}', 'instructions');
            }
            
            if ($this->db->columnExists('{{%simplemenu_menus}}', 'sortOrder')) {
                $this->dropColumn('{{%simplemenu_menus}}', 'sortOrder');
            }
        }

        return true;
    }
}