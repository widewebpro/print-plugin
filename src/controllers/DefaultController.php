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
class DefaultController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index', 'do-something', 'create-pdf', 'update-pdf', 'update-pdf-outputs',
        'print-pdf-by-id', 'create-field', 'delete-field-by-id', 'get-html', 'delete-pdf-by-id', 'set-fields-for-pdf',
        'enable-pdf', 'disable-pdf'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/print-plugin/default
     *
     * @return mixed
     */
//    public function actionIndex()
//    {
//        $folderId = Craft::$app->plugins->getPlugin('print-plugin')->getSettings()->choiceFolder;
//        $folderPath = Craft::$app->volumes->getVolumeById($folderId)->path;
//        $name = rand(0, 3000) . '_user_' . Craft::$app->user->getIdentity()->id;
//        $html = file_get_contents(Craft::$app->getView()->getTemplatesPath() . Craft::$app->plugins->getPlugin('print-plugin')->getSettings()->pathToTemplate);
//        $html = $this->outputs($html);
//        $html2pdf = new Pdf(['page-size' => Craft::$app->plugins->getPlugin('print-plugin')->getSettings()->choiceFormant,
//            'binary' => Craft::$app->plugins->getPlugin('print-plugin')->getSettings()->libPath]);
//        $html2pdf->addPage($html);
//        if (!$html2pdf->saveAs( $folderPath . $name)) {
//            return $html2pdf->getError();
//        }else{
//            $asset = new Asset();
//            $asset->tempFilePath = CRAFT_BASE_PATH . '/web/' . $folderPath . $name;
//            $asset->filename = $name;
//            $asset->title = $name;
//            $asset->newFolderId = VolumeFolder::find()->where('volumeId = '.$folderId )->one()->id;
//            $asset->volumeId = $folderId;
//            $asset->avoidFilenameConflicts = true;
//            $asset->setScenario(Asset::SCENARIO_CREATE);
//            Craft::$app->getElements()->saveElement($asset);
//
//            return Craft::$app->getResponse()->redirect($asset->getUrl());
//        }
//    }

    /**
     * Handle a request going to our plugin's actionDoSomething URL,
     * e.g.: actions/print-plugin/default/do-something
     *
     * @return mixed
     */
    public function actionDoSomething()
    {
        $result = Craft::$app->fields->getFieldsByElementType(User::class);

        return $result;
    }

    public function outputs($html)
    {

        $currentUser = Craft::$app->user->getIdentity();
        $siteLogo = $currentUser->siteLogo->one();
        if ($siteLogo){
            $siteLogo = $siteLogo->getUrl();
        }
        $userPhoto = $currentUser->photo;
        if ($userPhoto){
            $userPhoto = $userPhoto->getUrl();
        }

        $html = str_replace('{siteUrl}', Craft::$app->sites->currentSite->getBaseUrl(), $html);
        $html = str_replace('{user.name}', $currentUser->name, $html);
        $html = str_replace('{user.header}', $currentUser->header, $html);
        $html = str_replace('{user.aboutMe}', $currentUser->aboutMe, $html);
        $html = str_replace('{user.city}', $currentUser->city, $html);
        $html = str_replace('{user.state}', $currentUser->state, $html);
        $html = str_replace('{user.contactPhoneNumber}', $currentUser->contactPhoneNumber, $html);
        $html = str_replace('{user.additionalEmail}', $currentUser->additionalEmail, $html);
        $html = str_replace('{user.logo}', $siteLogo, $html);
        $html = str_replace('{avatar}', $userPhoto, $html);
        $certifications = $currentUser->certifications->all();
        $certificationsHtml = '';
        if ($certifications){
            foreach ($certifications as $certification){
                $certificationsHtml = $certificationsHtml . "<div><span> $certification->certificationTitle </span> <p> $certification->shortDescription </p></div>";
            }

        }
        $html = str_replace('{user.certifications}', $certificationsHtml, $html);
        return $html;
//
    }

    public function actionCreatePdf()
    {
        $height = Craft::$app->request->getBodyParam('height');
        $width = Craft::$app->request->getBodyParam('width');
        $typeOfSize = Craft::$app->request->getBodyParam('type');
        $size = Craft::$app->request->getBodyParam('size');
        $title = Craft::$app->request->getBodyParam('title');
        $userGroup = Craft::$app->request->getBodyParam('userGroup');
        $typeFile = Craft::$app->request->getBodyParam('typeFile');
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
        if ($typeOfSize and $typeOfSize == 'a' and $size){
            Craft::$app->db->createCommand()->insert('{{%print_pdfs}}',
                [
                    'title' => $title,
                    'type' => $typeOfSize,
                    'fileType' => $typeFile,
                    'userGroup' => $userGroup,
                    'category' => $category,
                    'settings' => json_encode(
                        [
                            'height' => null,
                            'width' => null,
                            'format' => $size
                        ]
                    ),
                    'file' => trim($url),
                    'previewImage' => trim($previewImage)
                ])->execute();
        }elseif($typeOfSize){
            Craft::$app->db->createCommand()->insert('{{%print_pdfs}}',
                [
                    'title' => $title,
                    'type' => $typeOfSize,
                    'fileType' => $typeFile,
                    'userGroup' => $userGroup,
                    'category' => $category,
                    'settings' => json_encode(
                        [
                            'height' => $height,
                            'width' => $width,
                            'format' => null
                        ]
                    ),
                    'file' => trim($url),
                    'previewImage' => trim($previewImage)
                ])->execute();
        }


        return Craft::$app->getResponse()->redirect('/' . Craft::$app->config->general->cpTrigger .'/print-plugin');


    }

    public function actionUpdatePdf()
    {
        $id =  Craft::$app->request->getBodyParam('id');
        $height = Craft::$app->request->getBodyParam('height');
        $width = Craft::$app->request->getBodyParam('width');
        $typeOfSize = Craft::$app->request->getBodyParam('type');
        $title = Craft::$app->request->getBodyParam('title');
        $size = Craft::$app->request->getBodyParam('size');
        $userGroup = Craft::$app->request->getBodyParam('userGroup');
        $typeFile = Craft::$app->request->getBodyParam('typeFile');
        $category = Craft::$app->request->getBodyParam('category');
        $previewImage = $_FILES['previewImage'];
        $file = $_FILES['file'];
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
            Craft::$app->db->createCommand()->update('{{%print_pdfs}}',
                [
                    'previewImage' => trim($previewImage),
                ], ['id' => $id])->execute();
        }
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
            $file = $asset->getUrl();
            Craft::$app->db->createCommand()->update('{{%print_pdfs}}',
                [
                    'file' => trim($file),
                ], ['id' => $id])->execute();
        }
        if ($typeOfSize and $typeOfSize == 'a' and $size and $id){
            Craft::$app->db->createCommand()->update('{{%print_pdfs}}',
                [
                    'title' => $title,
                    'type' => $typeOfSize,
                    'category' => $category,
                    'userGroup' => $userGroup,
                    'fileType' => $typeFile,
                    'settings' => json_encode(
                        [
                            'height' => null,
                            'width' => null,
                            'format' => $size
                        ]
                    )
                ], ['id' => $id])->execute();
        }elseif($typeOfSize and $id){
            Craft::$app->db->createCommand()->update('{{%print_pdfs}}',
                [
                    'title' => $title,
                    'type' => $typeOfSize,
                    'category' => $category,
                    'userGroup' => $userGroup,
                    'fileType' => $typeFile,
                    'settings' => json_encode(
                        [
                            'height' => $height,
                            'width' => $width,
                            'format' => null
                        ]
                    )
                ], ['id' => $id])->execute();
        }
        return Craft::$app->getResponse()->redirect('/' . Craft::$app->config->general->cpTrigger . '/print-plugin');
    }

    public function actionUpdatePdfOutputs()
    {
        $id =  Craft::$app->request->getBodyParam('id');
        $outputs = [];
        $fields = new PrintPluginVariable();
        $fields = $fields->getAllFields();
        foreach ($fields as $field){
            $fieldFromForm =  Craft::$app->request->getBodyParam($field['handle']);
            if ($fieldFromForm){
                if ($fieldFromForm != 'custom'){
                    $outputs[$field['handle']] = ['type' => 'default', 'content' => $fieldFromForm];
                }else{
                    $fieldFromForm =  Craft::$app->request->getBodyParam('custom_'.$field['handle']);
                    $outputs[$field['handle']] = ['type' => 'custom', 'content' => $fieldFromForm];
                }
            }
        }
        Craft::$app->db->createCommand()->update('{{%print_pdfs}}',
            [
                'outputs' => json_encode($outputs)
            ], ['id' => $id])->execute();


        return Craft::$app->getResponse()->redirect('/' . Craft::$app->config->general->cpTrigger .'/print-plugin');
    }

    public function actionPrintPdfById()
    {
        $id =  Craft::$app->request->getBodyParam('id');
        if (!$id){
            return false;
        }
        $pdf = new PrintPluginVariable;
        $pdf = $pdf->getPdfById($id);
        if (!$pdf){
            return false;
        }
        $folderId = Craft::$app->plugins->getPlugin('print-plugin')->getSettings()->choiceFolder;
        $folderPath = Craft::$app->volumes->getVolumeById($folderId)->path;
        $name = rand(0, 3000) . '_user_' . Craft::$app->user->getIdentity()->id. '.pdf';
//        $html = file_get_contents(Craft::$app->getView()->getTemplatesPath() . Craft::$app->plugins->getPlugin('print-plugin')->getSettings()->pathToTemplate);
        $html = $this->outputPdf( Craft::$app->plugins->getPlugin('print-plugin')->getSettings()->pathToTemplate, $pdf);
        $html2pdf = new Pdf(['page-size' => Craft::$app->plugins->getPlugin('print-plugin')->getSettings()->choiceFormant,
            'binary' => Craft::$app->plugins->getPlugin('print-plugin')->getSettings()->libPath]);
        $html2pdf->addPage($html);
        if (!$html2pdf->saveAs( $folderPath . $name)) {
            return $html2pdf->getError();
        }else{
            $asset = new Asset();
            $asset->tempFilePath = CRAFT_BASE_PATH . '/web/' . $folderPath . $name;
            $asset->filename = $name;
            $asset->title = $name;
            $asset->newFolderId = VolumeFolder::find()->where('volumeId = '.$folderId )->one()->id;
            $asset->volumeId = $folderId;
            $asset->avoidFilenameConflicts = true;
            $asset->setScenario(Asset::SCENARIO_CREATE);
            Craft::$app->getElements()->saveElement($asset);


            return Craft::$app->getResponse()->redirect($asset->getUrl());
        }
    }

    public function actionGetHtml($id)
    {
        $pdf = new PrintPluginVariable;
        $pdf = $pdf->getPdfById($id);
        $checkOutputs = Craft::$app->request->getParam('outputs');
        if ($pdf){
            if ($checkOutputs){
            $html = $this->outputPdf($pdf, $checkOutputs);
                }else{
            $html = $this->outputPdf($pdf);
            }
            if ($html){
                return json_encode(['html' => $html, 'settings' => ['format' => $pdf['type'], 'size' => $pdf['settings'] ]]);
            }

        }
    }

    public function outputPdf($pdf, $customOutputs = '')
    {
        $pdfOutput = json_decode($pdf['outputs']);
        $currentUser = Craft::$app->user->getIdentity();
        $outputs = [];
        if ($customOutputs){
            $customOutputs = \GuzzleHttp\json_decode($customOutputs);
            foreach ($customOutputs as $key => $value){
                $field = (new Query())->select('*')->from('{{%print_fields}}')->where(['handle' => $key])->one();
                if ($field['type'] == 'text' and $value!= 'custom'){
                    $outputs[$key] = $currentUser->$value;
                }elseif ($field['type'] == 'asset' and $value!= 'custom') {
                    if ($value != 'photo') {
                        $asset = $currentUser->$value->one() ?? $currentUser->$value;
                        if ($asset) {
                            $outputs[$key] = $asset->getUrl();
                        } else {
                            $outputs[$key] = $asset;
                        }
                    } else {
                        $asset = $currentUser->$value;
                        if ($asset) {
                            $outputs[$key] = $asset->getUrl();
                        }
                    }
                }
            }
        }else{
            if ($pdfOutput){
                foreach ($pdfOutput as $key => $value){
                    $field = (new Query())->select('*')->from('{{%print_fields}}')->where(['handle' => $key])->one();
                    if ($field['type'] == 'text'){
                        if ($value->type != 'custom'){
                        $f = $value->content;
                        $outputs[$key] = $currentUser->$f;
                        }else{
                        $outputs[$key] = $value->content;
                        }
                    }elseif ($field['type'] == 'asset'){
                        $f = $value->content;
                        if ($f != 'photo'){
                            $fType = $value->type;
                            if ($fType == 'default'){
                            $asset = $currentUser->$f->one();
                                if ($asset){
                                    $outputs[$key] = $asset->getUrl();
                                }else{
                                    $outputs[$key] = $asset;
                                }
                            }else{
                                $outputs[$key] = $f;
                            }
                        }else{
                            $f = $value->content;
                            $fType = $value->type;
                            if ($fType == 'default'){
                            $asset = $currentUser->$f;
                                if ($asset){
                                    $outputs[$key] = $asset->getUrl();
                                }
                            }else{
                                $outputs[$key] = $f;
                            }
                        }
                    }
                }
                }else{
                $outputs = [];
            }
        }
        $file = file_get_contents($pdf['file']);
        $file_array = explode('/',$pdf['file']);
        $file_name = end($file_array);
        file_put_contents(CRAFT_BASE_PATH.'/web/img/files/'.$file_name, $file);
        $loader = new \Twig\Loader\FilesystemLoader(CRAFT_BASE_PATH.'/web/img/files/');
        $twig = new \Twig\Environment($loader);
        $template = $twig->load($file_name);
        $template = $template->render($outputs);
        unlink(CRAFT_BASE_PATH.'/web/img/files/'.$file_name);
        return $template;
    }

    public function actionCreateField()
    {
        $type = Craft::$app->request->getBodyParam('type');
        $handle = Craft::$app->request->getBodyParam('handle');
        $title = Craft::$app->request->getBodyParam('title');
        Craft::$app->db->createCommand()->insert('{{%print_fields}}',
            [
                'title' => $title,
                'type' => $type,
                'handle' => $handle
            ])->execute();
        return Craft::$app->getResponse()->redirect('/' . Craft::$app->config->general->cpTrigger .'/print-plugin/fields');

    }

    public function actionDeleteFieldById($id)
    {
        Craft::$app->db->createCommand()->delete('{{%print_fields}}', ['id' => $id])->execute();
        return Craft::$app->getResponse()->redirect('/' . Craft::$app->config->general->cpTrigger .'/print-plugin/fields');
    }

    public function actionDeletePdfById($id)
        {
            Craft::$app->db->createCommand()->delete('{{%print_pdfs}}', ['id' => $id])->execute();
            return Craft::$app->getResponse()->redirect('/' . Craft::$app->config->general->cpTrigger .'/print-plugin/');
        }

    public function actionSetFieldsForPdf()
    {
        $fields = Craft::$app->request->getParam('fields');
        $id = Craft::$app->request->getParam('id');
        if ($fields){
            $fieldsEnabled = [];
            foreach ($fields as $field => $value){
                array_push($fieldsEnabled, $field);
            }
            $fieldsEnabled = json_encode($fieldsEnabled);
        }else{
            $fieldsEnabled = null;
        }
        Craft::$app->db->createCommand()->update('{{%print_pdfs}}',
            [
                'fields' => $fieldsEnabled
            ], ['id' => $id])->execute();
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('/' . Craft::$app->config->general->cpTrigger .'/print-plugin/outputs/'. $id));
    }

    public function actionDisablePdf()
    {
        $id = Craft::$app->request->getParam('id');
        if ($id){
            Craft::$app->db->createCommand()->update('{{%print_pdfs}}',
                [
                    'enabled' => 0
                ], ['id' => $id])->execute();
        }
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('/' . Craft::$app->config->general->cpTrigger .'/print-plugin'));
    }

    public function actionEnablePdf()
    {
        $id = Craft::$app->request->getParam('id');
        if ($id){
            Craft::$app->db->createCommand()->update('{{%print_pdfs}}',
                [
                    'enabled' => 1
                ], ['id' => $id])->execute();
        }
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('/' . Craft::$app->config->general->cpTrigger .'/print-plugin'));
    }

}
