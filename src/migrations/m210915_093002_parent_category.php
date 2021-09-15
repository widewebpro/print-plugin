<?php

namespace wideweb\printplugin\migrations;

use Craft;
use craft\db\Migration;

/**
 * m210915_093002_parent_category migration.
 */
class m210915_093002_parent_category extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%print_categories}}', 'parent', 'integer');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m210915_093002_parent_category cannot be reverted.\n";
        return false;
    }
}
