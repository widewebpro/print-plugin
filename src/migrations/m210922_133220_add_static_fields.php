<?php

namespace wideweb\printplugin\migrations;

use Craft;
use craft\db\Migration;

/**
 * m210922_133220_add_static_fields migration.
 */
class m210922_133220_add_static_fields extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%print_static}}', 'videoIframe', 'string');
        $this->addColumn('{{%print_static}}', 'videoTitle', 'string');
        $this->addColumn('{{%print_static}}', 'assetFolder', 'integer');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m210922_133220_add_static_fields cannot be reverted.\n";
        return false;
    }
}
