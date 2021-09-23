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
class FrontController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['get-all-pdf', 'get-all-pdf-by-tabs', 'get-all-static', 'get-pdf-by-id', 'get-fields-from-user-model', 'get-all-fields'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/print-plugin/default
     *
     * @return mixed
     */
    public function actionGetAllPdf($status = 'any')
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
            $pdfs = (new Query())->select("*")->from('{{%print_pdfs}}')->where(['userGroup' => $groups])->all();
        }elseif($status == 1){
            $pdfs = (new Query())->select("*")->from('{{%print_pdfs}}')->where(['enabled' => 1, 'userGroup' => $groups])->all();
        }else{
            $pdfs = [];
        }
        return \GuzzleHttp\json_encode($pdfs);
    }

    public function actionGetAllPdfByTabs($status = 'any')
    {
        $categoryGroups = (new Query())->select('*')->from('{{%print_categories}}')->where(['type' => 'pdf'])->all();
        $allowedCategories = [];

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
        foreach ($categoryGroups as $categoryGroup){
            $array = unserialize($categoryGroup['userGroup']);
            if (array_intersect($groups, $array)){
                $allowedCategories[] = $categoryGroup;
            }
        }


        $pdfs = [];
        if ($status == 'any'){
            foreach ($allowedCategories as $allowedCategory){
                $childs = PrintPlugin::$plugin->categories->getChildCategoriesByCategory($allowedCategory['id'], 'pdf');
                if ($childs){
                    foreach ($childs as $child){
                    $pdfs[$allowedCategory['title']][$child['title']] = (new Query())->select("*")->from('{{%print_pdfs}}')->where(['userGroup' => $groups, 'category' => 3])->all();
                    }
                }
            }
        }elseif($status == 1){
            foreach ($allowedCategories as $allowedCategory){
                $childs = PrintPlugin::$plugin->categories->getChildCategoriesByCategory($allowedCategory['id'], 'pdf');
                if ($childs){
                    foreach ($childs as $child){
                        $pdfs[$allowedCategory['title']][$child['title']] = (new Query())->select("*")->from('{{%print_pdfs}}')->where(['enabled' => 1,'userGroup' => $groups, 'category' => $child['id']])->all();
                    }
                }
            }
        }else{
            $pdfs = [];
        }
        return $this->asJson($pdfs);
    }

    public function actionGetPdfById($id)
    {
        $pdfs = (new Query())->select("*")->from('{{%print_pdfs}}')->where('id =' . $id)->one();
        return \GuzzleHttp\json_encode($pdfs);
    }

    public function actionGetFieldsFromUserModel()
    {
        $result = Craft::$app->fields->getFieldsByElementType(User::class);
        $additionalFields = [
            [
                'name' => 'First Name',
                'handle' => 'firstName',
                'className' => 'craft\fields\PlainText',
            ],
            [
                'name' => 'Last Name',
                'handle' => 'lastName',
                'className' => 'craft\fields\PlainText',
            ],
            [
                'name' => 'Email',
                'handle' => 'email',
                'className' => 'craft\fields\PlainText',
            ],
            [
                'name' => 'Photo',
                'handle' => 'photo',
                'className' => 'craft\fields\Assets',
            ]
        ];
        foreach ($additionalFields as $additionalField){
            array_push($result, $additionalField);
        }
        return \GuzzleHttp\json_encode($result);
    }

    public function actionGetAllFields()
    {
        $fields = (new Query())->select("*")->from('{{%print_fields}}')->all();
        return \GuzzleHttp\json_encode($fields);
    }

    public function actionGetAllStatic($status = 'any')
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
            $pdfs = (new Query())->select("*")->from('{{%print_static}}')->where(['userGroup' => $groups])->all();
        }elseif($status == 1){
            $pdfs = (new Query())->select("*")->from('{{%print_static}}')->where(['enabled' => 1, 'userGroup' => $groups])->all();
        }else{
            $pdfs = [];
        }
        return \GuzzleHttp\json_encode($pdfs);
    }

    public function actionGetAllStaticByTabs($status = 'any')
    {
        $categoryGroups = (new Query())->select('*')->from('{{%print_categories}}')->where(['type' => 'static'])->all();

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
        $allowedCategories = [];
        foreach ($categoryGroups as $categoryGroup){
            $array = unserialize($categoryGroup['userGroup']);
            if (array_intersect($groups, $array)){
                $allowedCategories[] = $categoryGroup;
            }
        }
        $pdfs = [];
        if ($status == 'any'){
            foreach ($allowedCategories as $allowedCategory) {
                if ($allowedCategory['staticType'] == 'file') {
                    $selectedFields = 'id, title, enabled, userGroup, file, previewImage, dateCreated, dateUpdated';
                    $pdfs[$allowedCategory['title']] = [$allowedCategory['staticType'] => (new Query())
                        ->select($selectedFields)->from('{{%print_static}}')
                        ->where(['userGroup' => $groups, 'category' => $allowedCategory['id']])->all()];
                }elseif($allowedCategory['staticType'] == 'video'){
                    $selectedFields = 'id, title, enabled, userGroup, videoIframe, videoTitle, dateCreated, dateUpdated';
                    $pdfs[$allowedCategory['title']] = [$allowedCategory['staticType'] => (new Query())
                        ->select($selectedFields)->from('{{%print_static}}')
                        ->where(['userGroup' => $groups, 'category' => $allowedCategory['id']])->all()];
                }elseif($allowedCategory['staticType'] == 'assetFolder'){
                    $selectedFields = 'id, title, enabled, userGroup, dateCreated, dateUpdated, assetFolder';
                    $pdfsAsset = (new Query())
                        ->select($selectedFields)->from('{{%print_static}}')
                        ->where(['userGroup' => $groups, 'category' => $allowedCategory['id']])->all();
                    $arrayOfImages = [];
                    foreach ($pdfsAsset as $folder){
                    $subFolder = (new Query())->select('*')->from('{{%volumefolders}}')->where(
                        [
                            'parentId'=> $folder['assetFolder'],
                            'name' => 'photos'
                        ]
                    )->one();
                    if ($subFolder){
                        $assets =Asset::find()->folderId($subFolder['id'])->all();
                        foreach ($assets as $asset){
                        $arrayOfImages[] = $asset->url;
                        }
                    }

                    }
                    $pdfs[$allowedCategory['title']] = [$allowedCategory['staticType'] => $arrayOfImages ];
                }
            }
        }elseif($status == 1){
            foreach ($allowedCategories as $allowedCategory) {
                if ($allowedCategory['staticType'] == 'file') {
                    $selectedFields = 'id, title, enabled, userGroup, file, previewImage, dateCreated, dateUpdated';
                    $pdfs[$allowedCategory['title']] = [$allowedCategory['staticType'] => (new Query())
                        ->select($selectedFields)->from('{{%print_static}}')
                        ->where(['userGroup' => $groups, 'enabled' => 1, 'category' => $allowedCategory['id']])->all()];
                }elseif($allowedCategory['staticType'] == 'video'){
                    $selectedFields = 'id, title, enabled, userGroup, videoIframe, videoTitle, dateCreated, dateUpdated';
                    $pdfs[$allowedCategory['title']] = [$allowedCategory['staticType'] => (new Query())
                        ->select($selectedFields)->from('{{%print_static}}')
                        ->where(['userGroup' => $groups,'enabled' => 1, 'category' => $allowedCategory['id']])->all()];
                }elseif($allowedCategory['staticType'] == 'assetFolder'){
                    $selectedFields = 'id, title, enabled, userGroup, dateCreated, dateUpdated, assetFolder';
                    $pdfsAsset = (new Query())
                        ->select($selectedFields)->from('{{%print_static}}')
                        ->where(['userGroup' => $groups,'enabled' => 1, 'category' => $allowedCategory['id']])->all();
                    $arrayOfImages = [];
                    foreach ($pdfsAsset as $folder){
                        $subFolder = (new Query())->select('*')->from('{{%volumefolders}}')->where(
                            [
                                'parentId'=> $folder['assetFolder'],
                                'name' => 'photos'
                            ]
                        )->one();
                        if ($subFolder){
                            $assets =Asset::find()->folderId($subFolder['id'])->all();
                            foreach ($assets as $asset){
                                $arrayOfImages[] = $asset->url;
                            }
                        }

                    }
                    $pdfs[$allowedCategory['title']] = [$allowedCategory['staticType'] => $arrayOfImages ];
                }
            }
        }
        return \GuzzleHttp\json_encode($pdfs);
    }


}
