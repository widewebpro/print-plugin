<?php

namespace wideweb\printplugin\migrations;

use Craft;
use craft\db\Migration;

/**
 * m210823_113956_categories_pdf_static migration.
 */
class m210823_113956_categories_pdf_static extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%print_pdfs}}', 'category', 'integer');
        $this->addColumn('{{%print_static}}', 'category', 'integer');    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m210823_113956_categories_pdf_static cannot be reverted.\n";
        return false;
    }
}
