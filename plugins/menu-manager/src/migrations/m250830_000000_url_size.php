<?php
namespace mycompany\menumanager\migrations;

use mycompany\menumanager\MenuManager;
use mycompany\menumanager\elements\Node;

use craft\db\Query;
use craft\migrations\BaseContentRefactorMigration;

class m250830_000000_url_size extends BaseContentRefactorMigration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): bool
    {
        $this->alterColumn('{{%navigation_nodes}}', 'url', $this->text());

        return true;
    }

    public function safeDown(): bool
    {
        echo "m250830_000000_url_size cannot be reverted.\n";

        return false;
    }
}
