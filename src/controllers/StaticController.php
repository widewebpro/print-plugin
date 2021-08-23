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
use craft\elements\User;
use craft\fields\Assets;
use craft\helpers\UrlHelper;
use craft\records\VolumeFolder;
use Dompdf\Dompdf;
use Dompdf\Options;
use function GuzzleHttp\uri_template;
use mikehaertl\wkhtmlto\Pdf;
use Mpdf\Mpdf;
use Spipu\Html2Pdf\Html2Pdf;
use wideweb\printplugin\PrintPlugin;
use Craft;
use craft\web\Controller;
use wideweb\printplugin\variables\PrintPluginVariable;
use yii\db\Query;

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
class StaticController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['create-static', 'update-static', 'enable-static', 'disable-static', 'download-static'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/print-plugin/default
     *
     * @return mixed
     */

    public function actionCreateStatic()
    {
        $title = Craft::$app->request->getBodyParam('title');
        $userGroup = Craft::$app->request->getBodyParam('userGroup');
        $category = Craft::$app->request->getBodyParam('category');
        $file = $_FILES['file'];
        $previewImage = $_FILES['previewImage'];
        if ($previewImage and $previewImage['size'] != 0){
            $file_name = rand(0, 99999999) . $previewImage['name'];
            $folderId = Craft::$app->plugins->getPlugin('print-plugin')->getSettings()->choiceFolder;
            $asset = new Asset();
            $asset->tempFilePath = $previewImage['tmp_name'];
            $asset->filename = $file_name;
            $asset->title = $file_name;
            $asset->newFolderId = VolumeFolder::find()->where('volumeId = '.$folderId )->one()->id;
            $asset->volumeId = $folderId;
            $asset->avoidFilenameConflicts = true;
            $asset->setScenario(Asset::SCENARIO_CREATE);
            Craft::$app->getElements()->saveElement($asset);
            $previewImage = $asset->getUrl();
        }else{
            $previewImage = null;
        }
        $url = null;
        if ($file){
            $file_name = rand(0, 99999999) . $file['name'];
            $folderId = Craft::$app->plugins->getPlugin('print-plugin')->getSettings()->choiceFolder;
            $asset = new Asset();
            $asset->tempFilePath = $file['tmp_name'];
            $asset->filename = $file_name;
            $asset->title = $file_name;
            $asset->newFolderId = VolumeFolder::find()->where('volumeId = '.$folderId )->one()->id;
            $asset->volumeId = $folderId;
            $asset->avoidFilenameConflicts = true;
            $asset->setScenario(Asset::SCENARIO_CREATE);
            Craft::$app->getElements()->saveElement($asset);

            $url = $asset->getUrl();
        }
            Craft::$app->db->createCommand()->insert('{{%print_static}}',
                [
                    'title' => $title,
                    'userGroup' => $userGroup,
                    'category' => $category,
                    'file' => trim($url),
                    'previewImage' => trim($previewImage)
                ])->execute();
        return Craft::$app->getResponse()->redirect('/' . Craft::$app->config->general->cpTrigger .'/print-plugin/static');

    }

    public function actionUpdateStatic()
    {
        $title = Craft::$app->request->getBodyParam('title');
        $userGroup = Craft::$app->request->getBodyParam('userGroup');
        $category = Craft::$app->request->getBodyParam('category');
        $id = Craft::$app->request->getBodyParam('id');
        $file = $_FILES['file'];
        $previewImage = $_FILES['previewImage'];
        if ($previewImage and $previewImage['size'] != 0){
            $file_name = rand(0, 99999999) . $previewImage['name'];
            $folderId = Craft::$app->plugins->getPlugin('print-plugin')->getSettings()->choiceFolder;
            $asset = new Asset();
            $asset->tempFilePath = $previewImage['tmp_name'];
            $asset->filename = $file_name;
            $asset->title = $file_name;
            $asset->newFolderId = VolumeFolder::find()->where('volumeId = '.$folderId )->one()->id;
            $asset->volumeId = $folderId;
            $asset->avoidFilenameConflicts = true;
            $asset->setScenario(Asset::SCENARIO_CREATE);
            Craft::$app->getElements()->saveElement($asset);
            $previewImage = $asset->getUrl();
        }else{
            $previewImage = null;
        }
        $url = null;
        if ($file){
            $file_name = rand(0, 99999999) . $file['name'];
            $folderId = Craft::$app->plugins->getPlugin('print-plugin')->getSettings()->choiceFolder;
            $asset = new Asset();
            $asset->tempFilePath = $file['tmp_name'];
            $asset->filename = $file_name;
            $asset->title = $file_name;
            $asset->newFolderId = VolumeFolder::find()->where('volumeId = '.$folderId )->one()->id;
            $asset->volumeId = $folderId;
            $asset->avoidFilenameConflicts = true;
            $asset->setScenario(Asset::SCENARIO_CREATE);
            Craft::$app->getElements()->saveElement($asset);

            $url = $asset->getUrl();
        }
        $data = [
            'title' => $title,
            'userGroup' => $userGroup,
            'category' => $category
        ];
        if ($previewImage){
            $data['previewImage'] = trim($previewImage);
        }
        if ($file){
            $data['file'] = trim($url);
        }
        Craft::$app->db->createCommand()->update('{{%print_static}}', $data, ['id' => $id])->execute();
        return Craft::$app->getResponse()->redirect('/' . Craft::$app->config->general->cpTrigger .'/print-plugin/static');

    }

    public function actionDisableStatic()
    {
        $id = Craft::$app->request->getParam('id');
        if ($id){
            Craft::$app->db->createCommand()->update('{{%print_static}}',
                [
                    'enabled' => 0
                ], ['id' => $id])->execute();
        }
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('/' . Craft::$app->config->general->cpTrigger .'/print-plugin/static'));
    }

    public function actionEnableStatic()
    {
        $id = Craft::$app->request->getParam('id');
        if ($id){
            Craft::$app->db->createCommand()->update('{{%print_static}}',
                [
                    'enabled' => 1
                ], ['id' => $id])->execute();
        }
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('/' . Craft::$app->config->general->cpTrigger .'/print-plugin/static'));
    }

    public function actionDeleteStaticById($id)
    {
        Craft::$app->db->createCommand()->delete('{{%print_static}}', ['id' => $id])->execute();
        return Craft::$app->getResponse()->redirect('/' . Craft::$app->config->general->cpTrigger .'/print-plugin/static');
    }

    public function actionDownloadStatic($id)
    {
        $static = (new Query())->select("file")->from('{{%print_static}}')->where(['id' => $id])->one();
        $filename = $static['file'];
        $name = basename($filename);
        header('Content-type: application/octet-stream');
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=".$name);
        while (ob_get_level()) {
            ob_end_clean();
        }
        readfile($filename);
    }

}
