<?php

namespace wideweb\printplugin\migrations;

use Craft;
use craft\db\Migration;

/**
 * m210922_132238_add_type_content_category migration.
 */
class m210922_132238_add_type_content_category extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%print_categories}}', 'staticType', 'string');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m210922_132238_add_type_content_category cannot be reverted.\n";
        return false;
    }
}
