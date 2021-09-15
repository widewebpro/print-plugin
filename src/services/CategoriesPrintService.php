<?php
/**
 * Print Plugin plugin for Craft CMS 3.x
 *
 * Print Plugin
 *
 * @link      WideWeb.pro
 * @copyright Copyright (c) 2020 WideWeb
 */

namespace wideweb\printplugin\services;

use craft\db\Query;


use Craft;
use craft\base\Component;

/**
 * CategoriesPrintService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    WideWeb
 * @package   PrintPlugin
 * @since     1.0.0
 */
class CategoriesPrintService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     PrintPlugin::$plugin->categoriesPrintService->exampleService()
     *
     * @return mixed
     */
    public function getAllCategories($type)
    {
        $result = (new Query())->select('*')->from('{{%print_categories}}')->where(['type' => $type])->all();
        return $result;
    }

    public function createCategory()
    {
        $title = Craft::$app->request->getRequiredParam('title');
        $type = Craft::$app->request->getRequiredParam('type');
        $userGroup = Craft::$app->request->getRequiredParam('userGroup');
        $parent = Craft::$app->request->getRequiredParam('parent');


        $result = Craft::$app->db->createCommand()->insert('{{%print_categories}}', [
            'title' => $title,
            'type' => $type,
            'parent' => $parent,
            'userGroup' => serialize($userGroup)
        ])->execute();
        return $result;
    }

    public function updateCategory()
    {
        $title = Craft::$app->request->getRequiredParam('title');
        $userGroup = Craft::$app->request->getRequiredParam('userGroup');
        $type = Craft::$app->request->getRequiredParam('type');
        $parent = Craft::$app->request->getRequiredParam('parent');
        $id = Craft::$app->request->getRequiredParam('id');

        $result = Craft::$app->db->createCommand()->update('{{%print_categories}}', [
            'title' => $title,
            'type' => $type,
            'parent' => $parent,
            'userGroup' => serialize($userGroup)
        ], ['id' => $id])->execute();
        return $result;
    }

    public function deleteCategory($id)
    {
        $result = Craft::$app->db->createCommand()->delete('{{%print_categories}}', ['id' => $id])->execute();
        return $result;
    }

    public function disableCategory($id)
    {
        $result = Craft::$app->db->createCommand()->update('{{%print_categories}}', ['status' => 0], ['id' => $id])->execute();
        return $result;
    }

    public function enableCategory($id)
    {
        $result = Craft::$app->db->createCommand()->update('{{%print_categories}}', ['status' => 1], ['id' => $id])->execute();
        return $result;
    }

    public function getCategoryById($id)
    {
        $result = (new Query())->select('*')->from('{{%print_categories}}')->where(['id' => $id])->one();
        return $result;
    }

    public function getAllParentCategories($type)
    {
        $result = (new Query())->select('*')->from('{{%print_categories}}')->where(['parent' => null, 'type' => $type])->all();
        return $result;
    }

    public function getChildCategoriesByCategory($id, $type)
    {
        $result = (new Query())->select('*')->from('{{%print_categories}}')->where(['parent' => $id, 'type' => $type])->all();
        return $result;
    }
}
