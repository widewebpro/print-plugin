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
use craft\records\VolumeFolder;
use Craft;
use craft\web\Controller;
use yii\db\Query;
use craft\commerce\Plugin;


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
class MarketingBuilderController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['create-marketing-builder', 'delete-marketing-builder-by-id', 'enable-marketing-builder', 'disable-marketing-builder',
        'update-marketing-builder', 'get-all-marketing-builder', 'pay-for-custom-marketing-builder', 'get-all-personal-marketing-builder',
        'make-personal-marketing-builder'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/print-plugin/marketing
     *
     * @return mixed
     */
//    public function actionMakePersonalMarketingBuilder()
//    {
//        $user = Craft::$app->user->getIdentity();
//        $contactId = $user->contactId;
//        $curl = curl_init();
//
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => 'https://rest.gohighlevel.com/v1/contacts/'.$contactId,
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'GET',
//            CURLOPT_HTTPHEADER => array(
//                'Authorization: Bearer 992e43da-4ff3-45a1-9057-973895668662'
//            ),
//        ));
//
//        $response = @json_decode(curl_exec($curl));
//
//        curl_close($curl);
//        if ($response and isset($response->contact->tags) and !in_array('custom marketing', $response->contact->tags)){
//            $tags = $response->contact->tags;
//            $tags[] = 'custom marketing';
//            $curl = curl_init();
//
//            curl_setopt_array($curl, array(
//                CURLOPT_URL => 'https://rest.gohighlevel.com/v1/contacts/'.$contactId,
//                CURLOPT_RETURNTRANSFER => true,
//                CURLOPT_ENCODING => '',
//                CURLOPT_MAXREDIRS => 10,
//                CURLOPT_TIMEOUT => 0,
//                CURLOPT_FOLLOWLOCATION => true,
//                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//                CURLOPT_CUSTOMREQUEST => 'PUT',
//                CURLOPT_POSTFIELDS => json_encode([
//                    'tags' => $tags
//                ]),
//                CURLOPT_HTTPHEADER => array(
//                    'Content-Type: application/json',
//                    'Authorization: Bearer 992e43da-4ff3-45a1-9057-973895668662'
//                ),
//            ));
//
//            $response = curl_exec($curl);
//
//            curl_close($curl);
//            $emails = Craft::$app->globals->getSetByHandle('customMarketingNotifications');
//            if (!$emails){
//                return 'Global set "customMarketingNotifications" not exist';
//            }
//            $emails = $emails->emails;
//            foreach ($emails as $email){
//                $email = $email['email'];
//                Craft::$app
//                    ->getMailer()
//                    ->compose()
//                    ->setTo($email)
//                    ->setSubject($user->email . '- This user wants personalized "Custom Marketing"')
//                    ->setHtmlBody("This user wants personalized \"Custom Marketing\"
//<br>Email: $user->email
//<br>First Name: $user->firstName
//<br>Last Name: $user->lastName
//<br>Phone: $user->contactPhoneNumber
//<br>Company: $user->companyName")
//                    ->send();
//            }
//            return $response;
//        }
//        return 'User already has this tag';
//    }


    public function actionUpdateMarketingBuilder()
    {
        $height = Craft::$app->request->getBodyParam('height');
        $width = Craft::$app->request->getBodyParam('width');
        $typeOfSize = Craft::$app->request->getBodyParam('type');
        $size = Craft::$app->request->getBodyParam('size');
        if  ($typeOfSize and $typeOfSize == 'a' and $size){
            $settings = json_encode([
                'height' => null,
                'width' => null,
                'format' => $size
            ]);
        }else{
            $settings = json_encode([
                'height' => $height,
                'width' => $width,
                'format' => null
            ]);
        }
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
        $owner = Craft::$app->request->getBodyParam('owner');
//        $pdf = $_FILES['pdf'];
//        $jpg = $_FILES['jpg'];
        $html = $_FILES['html'];
        $previewImage = $_FILES['previewImage'];
        $query = [];
        $query = $this->addToQuery('type', $typeOfSize, $query);
        $query = $this->addToQuery('settings', $settings, $query);
        $query = $this->addToQuery('vendor', $vendor, $query);
        $query = $this->addToQuery('userId', $owner, $query);
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
//        $query = $this->addFileToQuery('pdf', $pdf, $query);
//        $query = $this->addFileToQuery('jpg', $jpg, $query);
        $query = $this->addFileToQuery('html', $html, $query);
        $query = $this->addFilesToQuery('previewImage', $previewImage, $query);
        Craft::$app->db->createCommand()->update('{{%print_marketing_builder}}', $query, ['id' => $id])->execute();
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('/' . Craft::$app->config->general->cpTrigger .'/print-plugin/marketing-builder/' . $id));

    }


    public function actionDeleteMarketingBuilderById($id)
    {
        Craft::$app->db->createCommand()->delete('{{%print_marketing_builder}}', ['id' => $id])->execute();
        return Craft::$app->getResponse()->redirect('/' . Craft::$app->config->general->cpTrigger .'/print-plugin/marketing-builder');
    }

    public function actionDisableMarketingBuilder()
    {
        $id = Craft::$app->request->getParam('id');
        if ($id){
            Craft::$app->db->createCommand()->update('{{%print_marketing_builder}}',
                [
                    'enabled' => 0
                ], ['id' => $id])->execute();
        }
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('/' . Craft::$app->config->general->cpTrigger .'/print-plugin/marketing-builder'));
    }

    public function actionEnableMarketingBuilder()
    {
        $id = Craft::$app->request->getParam('id');
        if ($id){
            Craft::$app->db->createCommand()->update('{{%print_marketing_builder}}',
                [
                    'enabled' => 1
                ], ['id' => $id])->execute();
        }
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('/' . Craft::$app->config->general->cpTrigger .'/print-plugin/marketing-builder'));
    }


    public function actionCreateMarketingBuilder()
    {
        $height = Craft::$app->request->getBodyParam('height');
        $width = Craft::$app->request->getBodyParam('width');
        $typeOfSize = Craft::$app->request->getBodyParam('type');
        $size = Craft::$app->request->getBodyParam('size');
        if  ($typeOfSize and $typeOfSize == 'a' and $size){
            $settings = json_encode([
                    'height' => null,
                    'width' => null,
                    'format' => $size
                ]);
        }else{
            $settings = json_encode([
                    'height' => $height,
                    'width' => $width,
                    'format' => null
                ]);
        }
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
        $owner = Craft::$app->request->getBodyParam('owner');
//        $pdf = $_FILES['pdf'];
//        $jpg = $_FILES['jpg'];
        $html = $_FILES['html'];
        $previewImage = $_FILES['previewImage'];
        $query = [];
        $query = $this->addToQuery('vendor', $vendor, $query);
        $query = $this->addToQuery('type', $typeOfSize, $query);
        $query = $this->addToQuery('settings', $settings, $query);
        $query = $this->addToQuery('userId', $owner, $query);
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
//        $query = $this->addFileToQuery('pdf', $pdf, $query);
//        $query = $this->addFileToQuery('jpg', $jpg, $query);
        $query = $this->addFileToQuery('html', $html, $query);
        $query = $this->addFilesToQuery('previewImage', $previewImage, $query);
        Craft::$app->db->createCommand()->insert('{{%print_marketing_builder}}', $query)->execute();
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('/' . Craft::$app->config->general->cpTrigger .'/print-plugin/marketing-builder/'));
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

    public function actionGetAllMarketingBuilder($status = 'any')
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
            $marketing = (new Query())->select("*")->from('{{%print_marketing_builder}}')->where(['userGroup' => $groups, 'userId' => 'all'])->all();
        }elseif($status == 1){
            $marketing = (new Query())->select("*")->from('{{%print_marketing_builder}}')->where(['enabled' => 1, 'userGroup' => $groups, 'userId' => 'all'])->all();
        }else{
            $marketing = [];
        }
        return \GuzzleHttp\json_encode($marketing);
    }

//    public function actionGetAllPersonalMarketingBuilder($status = 'any')
//    {
//        $comingSoon = Craft::$app->plugins->getPlugin('print-plugin')->getSettings()->comingSoon;
//        if ($comingSoon){
//            $status = 'any';
//        }else{
//            $status = 1;
//        }
//        $currentUser = Craft::$app->user->getIdentity();
//        $currentUserGroups = Craft::$app->user->getIdentity()->getGroups();
//        $groups = ['all'];
//        foreach ($currentUserGroups as $currentUserGroup){
//            array_push($groups, $currentUserGroup->handle);
//        }
//        if ($status == 'any'){
//            $marketing = (new Query())->select("*")->from('{{%print_marketing_builder}}')->where(['userGroup' => $groups, 'userId' => $currentUser->id])->all();
//        }elseif($status == 1){
//            $marketing = (new Query())->select("*")->from('{{%print_marketing_builder}}')->where(['enabled' => 1, 'userGroup' => $groups, 'userId' => $currentUser->id])->all();
//        }else{
//            $marketing = [];
//        }
//        return \GuzzleHttp\json_encode($marketing);
//    }

    public function actionPayForMarketingBuilder($marketingId, $count = 1)
    {
        $this->requireLogin();
        $marketing = (new Query())->select("*")->from('{{%print_marketing_builder}}')->where(['id' => $marketingId])->one();
        $secretKey = Plugin::getInstance()->gateways->getGatewayByHandle('sripe')->settings['apiKey'];
        $stripe = new \Stripe\StripeClient(
            $secretKey
        );
        $user = Craft::$app->getUser()->getIdentity();
        $customer = \craft\commerce\stripe\Plugin::getInstance()->getCustomers()->getCustomer(2, $user);
        if (!$customer){
            return false;
        }
        $customerId = $customer->reference;
        $fullPrice = $marketing['price'] * $count;
        $fullPrice = $fullPrice + $marketing['shipping_cost'];
        $fullPrice = $fullPrice * 100;
        $title = $marketing['title'];
        $html = "User with craftId $user->id and email $user->email, paid for Custom Marketing '$title' id=$marketingId count=$count";
        $payment = $stripe->paymentIntents->create([
            'amount' => $fullPrice,
            'currency' => 'usd',
            'customer' => $customerId,
            'payment_method_types' => ['card'],
            'description' => $html,
            'metadata' => ['payFor' => 'Custom Marketing']
        ]);
        $stripeResponce = $stripe->paymentIntents->confirm(
            $payment->id,
            ['payment_method' => 'pm_card_visa']
        );
        if ($stripeResponce->status == 'succeeded' and $marketing['vendor']) {
            $this->sendEmail($html, $user->email, $marketing['vendor']);
            return true;
        }elseif ($stripeResponce->status){
            return true;
        }
        return false;
    }

    public function sendEmail($html, $emailUser, $vendorEmail)
    {
        Craft::$app
            ->getMailer()
            ->compose()
            ->setTo($vendorEmail)
            ->setSubject($emailUser . ' - placed for Marketing Builder')
            ->setHtmlBody($html)
            ->send();
    }

    public function addFilesToQuery($name, $files, $query)
    {

        if ($files['size'][0] == 0){
            return $query;
        }
        $total = count($files['name']);
        $arrayOfImages = [];
        for( $i=0 ; $i < $total ; $i++ ) {

            $file_name = rand(0, 99999999) . $files['name'][$i];
            $folderId = Craft::$app->plugins->getPlugin('print-plugin')->getSettings()->choiceFolder;
            $asset = new Asset();
            $asset->tempFilePath = $files['tmp_name'][$i];
            $asset->filename = $file_name;
            $asset->title = $file_name;
            $asset->newFolderId = VolumeFolder::find()->where('volumeId = '.$folderId )->one()->id;
            $asset->volumeId = $folderId;
            $asset->avoidFilenameConflicts = true;
            $asset->setScenario(Asset::SCENARIO_CREATE);
            Craft::$app->getElements()->saveElement($asset);
            $arrayOfImages[] = $asset->getUrl();

        }
        $query[$name] = \GuzzleHttp\json_encode($arrayOfImages);

        return $query;
    }

}
