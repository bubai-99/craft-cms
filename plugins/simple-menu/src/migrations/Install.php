<?php

namespace mycompany\simplemenu\migrations;

use craft\db\Migration;

class Install extends Migration
{
    public function safeUp(): bool
    {
        $this->createTable('{{%simplemenu_menus}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'handle' => $this->string(255)->notNull(),
            'items' => $this->longText(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->createIndex(null, '{{%simplemenu_menus}}', 'handle', true);

        return true;
    }

    public function safeDown(): bool
    {
        $this->dropTableIfExists('{{%simplemenu_menus}}');
        return true;
    }
}