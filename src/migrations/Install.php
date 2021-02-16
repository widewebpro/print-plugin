<?php
/**
 * Social media posts plugin for Craft CMS 3.x
 *
 * Social media posts
 *
 * @link      WideWeb.pro
 * @copyright Copyright (c) 2020 WideWeb
 */

namespace wideweb\printplugin\migrations;

use wideweb\printplugin\PrintPlugin;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * Social media posts Install Migration
 *
 * If your plugin needs to create any custom database tables when it gets installed,
 * create a migrations/ folder within your plugin folder, and save an Install.php file
 * within it using the following template:
 *
 * If you need to perform any additional actions on install/uninstall, override the
 * safeUp() and safeDown() methods.
 *
 * @author    WideWeb
 * @package   SocialMediaPosts
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * This method contains the logic to be executed when applying this migration.
     * This method differs from [[up()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[up()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     * This method differs from [[down()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[down()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates the tables needed for the Records used by the plugin
     *
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

    // socialmediaposts_facebook table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%print_pdfs}}');
        $tableSchema2 = Craft::$app->db->schema->getTableSchema('{{%print_fields}}');
        $tableSchema3 = Craft::$app->db->schema->getTableSchema('{{%print_static}}');

        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%print_pdfs}}',
                [
                    'id' => $this->primaryKey(),
                    'title' => $this->string()->notNull(),
                    'type' => $this->string()->notNull(),
                    'settings' => $this->string()->notNull(),
                    'outputs' => $this->longText(),
                    'enabled' => $this->integer()->notNull()->defaultValue(1),
                    'userGroup' => $this->string()->defaultValue('all'),
                    'fields' => $this->longText(),
                    'file' => $this->string(),
                    'fileType' => $this->string()->defaultValue('pdf'),
                    'previewImage' => $this->string(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                ]
            );
        }

        if ($tableSchema2 === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%print_fields}}',
                [
                    'id' => $this->primaryKey(),
                    'type' => $this->string()->notNull(),
                    'handle' => $this->string()->notNull()->unique(),
                    'title' => $this->string()->notNull()->unique(),
                    'enabled' => $this->integer()->notNull()->defaultValue(1),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                ]
            );
        }

        if ($tableSchema3 === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%print_static}}',
                [
                    'id' => $this->primaryKey(),
                    'title' => $this->string()->notNull(),
//                    'type' => $this->string()->notNull(),
//                    'settings' => $this->string()->notNull(),
//                    'outputs' => $this->longText(),
                    'enabled' => $this->integer()->notNull()->defaultValue(1),
                    'userGroup' => $this->string()->defaultValue('all'),
//                    'fields' => $this->string(),
                    'file' => $this->string(),
//                    'fileType' => $this->string(),
                    'previewImage' => $this->string(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * Creates the indexes needed for the Records used by the plugin
     *
     * @return void
     */
    protected function createIndexes()
    {
    // socialmediaposts_facebook table
//        $this->createIndex(
//            $this->db->getIndexName(
//                '{{%socialmediaposts_facebook}}',
//                'some_field',
//                true
//            ),
//            '{{%socialmediaposts_facebook}}',
//            'some_field',
//            true
//        );
//        // Additional commands depending on the db driver
//        switch ($this->driver) {
//            case DbConfig::DRIVER_MYSQL:
//                break;
//            case DbConfig::DRIVER_PGSQL:
//                break;
//        }
    }

    /**
     * Creates the foreign keys needed for the Records used by the plugin
     *
     * @return void
     */
    protected function addForeignKeys()
    {
    // socialmediaposts_facebook table
//        $this->addForeignKey(
//            $this->db->getForeignKeyName('{{%socialmediaposts_facebook}}', 'siteId'),
//            '{{%socialmediaposts_facebook}}',
//            'siteId',
//            '{{%sites}}',
//            'id',
//            'CASCADE',
//            'CASCADE'
//        );
    }

    /**
     * Populates the DB with the default data.
     *
     * @return void
     */
    protected function insertDefaultData()
    {
    }

    /**
     * Removes the tables needed for the Records used by the plugin
     *
     * @return void
     */
    protected function removeTables()
    {
    // socialmediaposts_facebook table
        $this->dropTableIfExists('{{%print_pdfs}}');
        $this->dropTableIfExists('{{%print_fields}}');
        $this->dropTableIfExists('{{%print_static}}');
    }
}
