<?php

namespace wideweb\printplugin\migrations;

use Craft;
use craft\db\Migration;

/**
 * m210705_073415_color_fields_campaign_builder migration.
 */
class m210705_073415_color_fields_campaign_builder extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%print_campaign_builder}}', 'primaryColor', 'text');
        $this->addColumn('{{%print_campaign_builder}}', 'secondaryColor', 'text');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m210705_073415_color_fields_campaign_builder cannot be reverted.\n";
        return false;
    }
}
