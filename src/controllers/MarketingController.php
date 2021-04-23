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
class MarketingController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['create-marketing', 'delete-marketing-by-id', 'enable-marketing', 'disable-marketing',
        'update-marketing', 'get-all-marketing'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/print-plugin/marketing
     *
     * @return mixed
     */
    public function actionUpdateMarketing()
    {
        $vendor = Craft::$app->request->getBodyParam('vendor');
        $id = Craft::$app->request->getBodyParam('id');
        $delivery_time = Craft::$app->request->getBodyParam('delivery_time');
        $title = Craft::$app->request->getBodyParam('title');
        $price = Craft::$app->request->getBodyParam('price');
        $description = Craft::$app->request->getBodyParam('description');
        $product_size = Craft::$app->request->getBodyParam('product_size');
        $specs = Craft::$app->request->getBodyParam('specs');
        $order_min = Craft::$app->request->getBodyParam('order_min');
        $shipping_cost = Craft::$app->request->getBodyParam('shipping_cost');
        $type = Craft::$app->request->getBodyParam('type');
        $enabled = Craft::$app->request->getBodyParam('enabled');
        $userGroup = Craft::$app->request->getBodyParam('userGroup');
        $pdf = $_FILES['pdf'];
        $jpg = $_FILES['jpg'];
        $html = $_FILES['html'];
        $previewImage = $_FILES['previewImage'];
        $query = [];
        $query = $this->addToQuery('vendor', $vendor, $query);
        $query = $this->addToQuery('delivery_time', $delivery_time, $query);
        $query = $this->addToQuery('title', $title, $query);
        $query = $this->addToQuery('price', $price, $query);
        $query = $this->addToQuery('description', $description, $query);
        $query = $this->addToQuery('product_size', $product_size, $query);
        $query = $this->addToQuery('specs', $specs, $query);
        $query = $this->addToQuery('order_min', $order_min, $query);
        $query = $this->addToQuery('shipping_cost', $shipping_cost, $query);
        $query = $this->addToQuery('type', $type, $query);
        $query = $this->addToQuery('enabled', $enabled, $query);
        $query = $this->addToQuery('userGroup', $userGroup, $query);
        $query = $this->addFileToQuery('pdf', $pdf, $query);
        $query = $this->addFileToQuery('jpg', $jpg, $query);
        $query = $this->addFileToQuery('html', $html, $query);
        $query = $this->addFileToQuery('previewImage', $previewImage, $query);
        Craft::$app->db->createCommand()->update('{{%print_marketing}}', $query, ['id' => $id])->execute();
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('/' . Craft::$app->config->general->cpTrigger .'/print-plugin/marketing/' . $id));

    }


    public function actionDeleteMarketingById($id)
    {
        Craft::$app->db->createCommand()->delete('{{%print_marketing}}', ['id' => $id])->execute();
        return Craft::$app->getResponse()->redirect('/' . Craft::$app->config->general->cpTrigger .'/print-plugin/marketing');
    }

    public function actionDisableMarketing()
    {
        $id = Craft::$app->request->getParam('id');
        if ($id){
            Craft::$app->db->createCommand()->update('{{%print_marketing}}',
                [
                    'enabled' => 0
                ], ['id' => $id])->execute();
        }
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('/' . Craft::$app->config->general->cpTrigger .'/print-plugin/marketing'));
    }

    public function actionEnableMarketing()
    {
        $id = Craft::$app->request->getParam('id');
        if ($id){
            Craft::$app->db->createCommand()->update('{{%print_marketing}}',
                [
                    'enabled' => 1
                ], ['id' => $id])->execute();
        }
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('/' . Craft::$app->config->general->cpTrigger .'/print-plugin/marketing'));
    }


    public function actionCreateMarketing()
    {
        $vendor = Craft::$app->request->getBodyParam('vendor');
        $delivery_time = Craft::$app->request->getBodyParam('delivery_time');
        $title = Craft::$app->request->getBodyParam('title');
        $price = Craft::$app->request->getBodyParam('price');
        $description = Craft::$app->request->getBodyParam('description');
        $product_size = Craft::$app->request->getBodyParam('product_size');
        $specs = Craft::$app->request->getBodyParam('specs');
        $order_min = Craft::$app->request->getBodyParam('order_min');
        $shipping_cost = Craft::$app->request->getBodyParam('shipping_cost');
        $type = Craft::$app->request->getBodyParam('type');
        $enabled = Craft::$app->request->getBodyParam('enabled') ?? 1;
        $userGroup = Craft::$app->request->getBodyParam('userGroup');
        $pdf = $_FILES['pdf'];
        $jpg = $_FILES['jpg'];
        $html = $_FILES['html'];
        $previewImage = $_FILES['previewImage'];
        $query = [];
        $query = $this->addToQuery('vendor', $vendor, $query);
        $query = $this->addToQuery('delivery_time', $delivery_time, $query);
        $query = $this->addToQuery('title', $title, $query);
        $query = $this->addToQuery('price', $price, $query);
        $query = $this->addToQuery('description', $description, $query);
        $query = $this->addToQuery('product_size', $product_size, $query);
        $query = $this->addToQuery('specs', $specs, $query);
        $query = $this->addToQuery('order_min', $order_min, $query);
        $query = $this->addToQuery('shipping_cost', $shipping_cost, $query);
        $query = $this->addToQuery('type', $type, $query);
        $query = $this->addToQuery('enabled', $enabled, $query);
        $query = $this->addToQuery('userGroup', $userGroup, $query);
        $query = $this->addFileToQuery('pdf', $pdf, $query);
        $query = $this->addFileToQuery('jpg', $jpg, $query);
        $query = $this->addFileToQuery('html', $html, $query);
        $query = $this->addFileToQuery('previewImage', $previewImage, $query);
        Craft::$app->db->createCommand()->insert('{{%print_marketing}}', $query)->execute();
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('/' . Craft::$app->config->general->cpTrigger .'/print-plugin/marketing/'));
    }

    public function addToQuery($name, $variable, $query)
    {
        if (!empty($variable)){
            $query[$name] = $variable;
        }
        return $query;
    }

    public function addFileToQuery($name, $file, $query)
    {
        if ($file and $file['size'] != 0){
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
            $query[$name] = $asset->getUrl();
        }
        return $query;
    }

    public function actionGetAllMarketing($status = 'any')
    {
        $comingSoon = Craft::$app->plugins->getPlugin('print-plugin')->getSettings()->comingSoon;
        if ($comingSoon){
            $status = 'any';
        }else{
            $status = 1;
        }
        $currentUserGroups = Craft::$app->user->getIdentity()->getGroups();
        $groups = ['all'];
        foreach ($currentUserGroups as $currentUserGroup){
            array_push($groups, $currentUserGroup->handle);
        }
        if ($status == 'any'){
            $marketing = (new Query())->select("*")->from('{{%print_marketing}}')->where(['userGroup' => $groups])->all();
        }elseif($status == 1){
            $marketing = (new Query())->select("*")->from('{{%print_marketing}}')->where(['enabled' => 1, 'userGroup' => $groups])->all();
        }else{
            $marketing = [];
        }
        return \GuzzleHttp\json_encode($marketing);
    }


}
