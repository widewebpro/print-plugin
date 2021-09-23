<?php
/**
 * Print Plugin plugin for Craft CMS 3.x
 *
 * Print Plugin
 *
 * @link      WideWeb.pro
 * @copyright Copyright (c) 2020 WideWeb
 */

namespace wideweb\printplugin\controllers;


use craft\elements\Asset;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use wideweb\printplugin\PrintPlugin;

/**
 * Default Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    WideWeb
 * @package   PrintPlugin
 * @since     1.0.0
 */
class CategoriesController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['create', 'update', 'disable', 'enable'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/print-plugin/categories
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $this->requireAdmin();
        $type = \Craft::$app->request->getParam('type');
        PrintPlugin::$plugin->categories->createCategory();
        return $this->redirect(UrlHelper::cpUrl('/' . \Craft::$app->config->general->cpTrigger .'/print-plugin/categories/?type='.$type));
    }

    public function actionUpdate()
    {
        $this->requireAdmin();
        $type = \Craft::$app->request->getParam('type');
        PrintPlugin::$plugin->categories->updateCategory();
        return $this->redirect(UrlHelper::cpUrl('/' . \Craft::$app->config->general->cpTrigger .'/print-plugin/categories/?type='.$type));
    }

    public function actionDelete()
    {
        $this->requireAdmin();
        $type = \Craft::$app->request->getParam('type');
        $id = \Craft::$app->request->getRequiredParam('id');
        PrintPlugin::$plugin->categories->deleteCategory($id);
        return $this->redirect(UrlHelper::cpUrl('/' . \Craft::$app->config->general->cpTrigger .'/print-plugin/categories/?type='.$type));
    }

    public function actionDisable()
    {
        $this->requireAdmin();
        $type = \Craft::$app->request->getParam('type');
        $id = \Craft::$app->request->getRequiredParam('id');
        PrintPlugin::$plugin->categories->disableCategory($id);
        return $this->redirect(UrlHelper::cpUrl('/' . \Craft::$app->config->general->cpTrigger .'/print-plugin/categories/?type='.$type));
    }

    public function actionEnable()
    {
        $this->requireAdmin();
        $type = \Craft::$app->request->getParam('type');
        $id = \Craft::$app->request->getRequiredParam('id');
        PrintPlugin::$plugin->categories->enableCategory($id);
        return $this->redirect(UrlHelper::cpUrl('/' . \Craft::$app->config->general->cpTrigger .'/print-plugin/categories/?type='.$type));
    }


}
