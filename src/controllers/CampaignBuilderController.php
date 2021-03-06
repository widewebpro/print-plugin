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
use wideweb\printplugin\variables\MarketingCampaignVariable;
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
class CampaignBuilderController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['create-campaign', 'get-html-campaign', 'get-campaigns-enable-by-user', 'update-campaign',
        'get-campaign-by-id', 'delete-campaign-by-id', 'get-campaigns-by-user-any-status', 'pay-for-campaign', 'get-layout-by-id'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/print-plugin/marketing
     *
     * @return mixed
     */

    public function actionPayForCampaign($id, $count = 1)
    {
        $this->requireLogin();
        $campaign = $this->actionGetCampaignById($id);

        if ($campaign){
            $layoutId = Craft::$app->request->getParam('layoutId');
            $layout = $this->getLayoutById($layoutId);
            if ($layout){
                $secretKey = Plugin::getInstance()->gateways->getGatewayById(2)->settings['apiKey'];
                $stripe = new \Stripe\StripeClient(
                    $secretKey
                );
                $user = Craft::$app->getUser()->getIdentity();
                $customer = \craft\commerce\stripe\Plugin::getInstance()->getCustomers()->getCustomer(2, $user);
                if (!$customer){
                    return false;
                }
                $layoutId = $layout['id'];
                $campaignId = $campaign['id'];
                $customerId = $customer->reference;
                $fullPrice = $layout['price'] * $count;
                $fullPriceForEmail = $fullPrice + $layout['shipping_cost'];
                $fullPrice = $fullPriceForEmail * 100;
                $title = $campaign['title'];
                $titleLayout = $layout['title'];
                $firstName = Craft::$app->request->getParam('firstName');
                $lastName = Craft::$app->request->getParam('lastName');
                $email = Craft::$app->request->getParam('email');
                $phone = Craft::$app->request->getParam('phone');
                $state = Craft::$app->request->getParam('state');
                $city = Craft::$app->request->getParam('city');
                $zip = Craft::$app->request->getParam('zip');
                $address1 = Craft::$app->request->getParam('address1');
                $address2 = Craft::$app->request->getParam('address2');
                $html = "User with craftId $user->id and email 
                $user->email, paid $fullPriceForEmail$ in the Campaign  '$title' for product '$titleLayout' (quantity: $count)";
                $html = $html."<br>
                First Name: $firstName<br>
                Last Name: $lastName<br>
                Email: $email<br>
                Phone: $phone<br>
                State: $state<br>
                City: $city<br>
                ZIP: $zip<br>
                Address1: $address1<br>
                Address2: $address2 <br>
                CompanyId:  $campaignId<br>
                ProductId:  $layoutId<br>
                Product name:  $titleLayout<br>
                ";
                $payment = $stripe->paymentIntents->create([
                    'amount' => $fullPrice,
                    'currency' => 'usd',
                    'customer' => $customerId,
                    'payment_method_types' => ['card'],
                    'description' => $html,
                    'metadata' => ['payFor' => 'Campaign Marketing Builder']
                ]);
                $stripeResponce = $stripe->paymentIntents->confirm(
                    $payment->id,
                    ['payment_method' => 'pm_card_visa']
                );
                if ($stripeResponce->status == 'succeeded' and $layout['vendor']) {
                    $this->sendEmail($html, $user->email, $layout['vendor']);
                    return true;
                }elseif ($stripeResponce->status == 'succeeded'){
                    return true;
                }elseif ($stripeResponce->last_payment_error){
                    return $this->asJson(['error' => $stripeResponce->last_payment_error->message]);
                }
            }else{
                return $this->asJson(['error' => '404 - layout not found']);
            }
        }else{
            return $this->asJson(['error' => '404 - campaign not found']);
        }

    }

    public function sendEmail($html, $emailUser, $vendorEmail)
    {
        Craft::$app
            ->getMailer()
            ->compose()
            ->setTo($vendorEmail)
            ->setSubject($emailUser . ' - placed for Campaign Marketing Builder')
            ->setHtmlBody($html)
            ->send();
    }

    public function actionCreateCampaign()
    {
        $userId = Craft::$app->user->getIdentity();
        if (!$userId){
            return $this->asJson(['error' => '403']);
        }
        $userId = $userId->id;

        $layoutId = Craft::$app->request->getParam('layoutId');
        $title = Craft::$app->request->getParam('title');
        $promotionFocus = Craft::$app->request->getParam('promotionFocus');
        $month = Craft::$app->request->getParam('month');
        $campaignHeadline = Craft::$app->request->getParam('campaignHeadline');
        $heroImage = Craft::$app->request->getParam('heroImage');
        $promoDiscount = Craft::$app->request->getParam('promoDiscount');
        $promoOffer = Craft::$app->request->getParam('promoOffer');
        $blurb = Craft::$app->request->getParam('blurb');
        $expires = Craft::$app->request->getParam('expires');
        $supportImages = Craft::$app->request->getParam('supportImages');
        $primaryCoupon = Craft::$app->request->getParam('primaryCoupon');
        $secondaryCoupon = Craft::$app->request->getParam('secondaryCoupon');
        $site = Craft::$app->request->getParam('site');
        $phone = Craft::$app->request->getParam('phone');
        $address = Craft::$app->request->getParam('address');
        $aptSuite = Craft::$app->request->getParam('aptSuite');
        $hoursOther = Craft::$app->request->getParam('hoursOther');
        $primaryColor = Craft::$app->request->getParam('primaryColor');
        $secondaryColor = Craft::$app->request->getParam('secondaryColor');
        $status = Craft::$app->request->getParam('status') ?? 1;
//        if ($supportImages){
//            $supportImages = $supportImages;
//        }
        $query = [];
        $query = $this->addToQuery('primaryColor', $primaryColor, $query);
        $query = $this->addToQuery('secondaryColor', $secondaryColor, $query);
        $query = $this->addToQuery('userId', $userId, $query);
        $query = $this->addToQuery('site', $site, $query);
        $query = $this->addToQuery('phone', $phone, $query);
        $query = $this->addToQuery('address', $address, $query);
        $query = $this->addToQuery('status', $status, $query);
        $query = $this->addToQuery('layoutId', $layoutId, $query);
        $query = $this->addToQuery('title', $title, $query);
        $query = $this->addToQuery('promotionFocus', $promotionFocus, $query);
        $query = $this->addToQuery('month', $month, $query);
        $query = $this->addToQuery('campaignHeadline', $campaignHeadline, $query);
        $query = $this->addToQuery('heroImage', $heroImage, $query);
        $query = $this->addToQuery('promoDiscount', $promoDiscount, $query);
        $query = $this->addToQuery('promoOffer', $promoOffer, $query);
        $query = $this->addToQuery('aptSuite', $aptSuite, $query);
        $query = $this->addToQuery('hoursOther', $hoursOther, $query);
        $query = $this->addToQuery('blurb', $blurb, $query);
        $query = $this->addToQuery('expires', $expires, $query);
        $query = $this->addToQuery('supportImages', $supportImages, $query);
        $query = $this->addToQuery('primaryCoupon', $primaryCoupon, $query);
        $query = $this->addToQuery('secondaryCoupon', $secondaryCoupon, $query);
        $query = $this->addToQuery('layoutId', $layoutId, $query);
        Craft::$app->db->createCommand()->insert('{{%print_campaign_builder}}', $query)->execute();
        return true;
    }

    public function actionUpdateCampaign($id)
    {
        $userId = Craft::$app->user->getIdentity();
        if (!$userId){
            return $this->asJson(['error' => '403']);
        }
        $layoutId = Craft::$app->request->getParam('layoutId');
        $title = Craft::$app->request->getParam('title');
        $status = Craft::$app->request->getParam('status') ?? 1;
        $promotionFocus = Craft::$app->request->getParam('promotionFocus');
        $month = Craft::$app->request->getParam('month');
        $campaignHeadline = Craft::$app->request->getParam('campaignHeadline');
        $heroImage = Craft::$app->request->getParam('heroImage');
        $promoDiscount = Craft::$app->request->getParam('promoDiscount');
        $promoOffer = Craft::$app->request->getParam('promoOffer');
        $blurb = Craft::$app->request->getParam('blurb');
        $expires = Craft::$app->request->getParam('expires');
        $supportImages = Craft::$app->request->getParam('supportImages');
        $primaryCoupon = Craft::$app->request->getParam('primaryCoupon');
        $secondaryCoupon = Craft::$app->request->getParam('secondaryCoupon');
        $site = Craft::$app->request->getParam('site');
        $phone = Craft::$app->request->getParam('phone');
        $address = Craft::$app->request->getParam('address');
        $aptSuite = Craft::$app->request->getParam('aptSuite');
        $hoursOther = Craft::$app->request->getParam('hoursOther');
        $primaryColor = Craft::$app->request->getParam('primaryColor');
        $secondaryColor = Craft::$app->request->getParam('secondaryColor');
//        if ($supportImages){
//            $supportImages = json_encode($supportImages);
//        }
        $query = [];
        $query = $this->addToQuery('primaryColor', $primaryColor, $query);
        $query = $this->addToQuery('secondaryColor', $secondaryColor, $query);
        $query = $this->addToQuery('layoutId', $layoutId, $query);
        $query = $this->addToQuery('site', $site, $query);
        $query = $this->addToQuery('phone', $phone, $query);
        $query = $this->addToQuery('address', $address, $query);
        $query = $this->addToQuery('status', $status, $query);
        $query = $this->addToQuery('title', $title, $query);
        $query = $this->addToQuery('promotionFocus', $promotionFocus, $query);
        $query = $this->addToQuery('month', $month, $query);
        $query = $this->addToQuery('campaignHeadline', $campaignHeadline, $query);
        $query = $this->addToQuery('heroImage', $heroImage, $query);
        $query = $this->addToQuery('promoDiscount', $promoDiscount, $query);
        $query = $this->addToQuery('promoOffer', $promoOffer, $query);
        $query = $this->addToQuery('aptSuite', $aptSuite, $query);
        $query = $this->addToQuery('hoursOther', $hoursOther, $query);
        $query = $this->addToQuery('blurb', $blurb, $query);
        $query = $this->addToQuery('expires', $expires, $query);
        $query = $this->addToQuery('supportImages', $supportImages, $query);
        $query = $this->addToQuery('primaryCoupon', $primaryCoupon, $query);
        $query = $this->addToQuery('secondaryCoupon', $secondaryCoupon, $query);
        Craft::$app->db->createCommand()->update('{{%print_campaign_builder}}', $query, ['id' => $id])->execute();
        return true;
    }

    public function actionGetEnableCampaignsByUser()
    {
        $userId = Craft::$app->user->getIdentity();
        if (!$userId){
            return $this->asJson(['error' => '403']);
        }
        $result = (new Query())->select('*')
            ->from('{{%print_campaign_builder}}')
            ->where(['userId' => $userId->id])->all();
        return $this->asJson(['data' => $result, 'status' => 1]);
    }

    public function actionGetCampaignsByUserAnyStatus()
    {
        $userId = Craft::$app->user->getIdentity();
        if (!$userId){
            return $this->asJson(['error' => '403']);
        }
        $result = (new Query())->select('*')
            ->from('{{%print_campaign_builder}}')
            ->where(['userId' => $userId->id])->all();
        return $this->asJson(['data' => $result]);
    }

    public function actionDeleteCampaignById($id)
    {
        $userId = Craft::$app->user->getIdentity();
        if (!$userId){
            return $this->asJson(['error' => '403']);
        }
        $result = Craft::$app->db
            ->createCommand()
            ->delete('{{%print_campaign_builder}}', ['id' => $id])
            ->execute();
        return $result;
    }

    public function actionGetCampaignById($id)
    {
        $result = (new Query())->select('*')
            ->from('{{%print_campaign_builder}}')
            ->where(['id' => $id])->one();
        $this->asJson(['data' => $result]);
        return $result;
    }

    public function addToQuery($name, $variable, $query)
    {
        if (!empty($variable) or $variable == 0){
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

    public function actionGetHtmlCampaign($id, $layout)
    {
        $campaign = new MarketingCampaignVariable();
        $campaign = $campaign->getMarketingById($id);
        $heroImage = $campaign['heroImage'];
        $supportImages = $campaign['supportImages'];
        $userId = $campaign['userId'];
        $logo = '';
        $whiteLogo = '';
        $colorPicker = '';
        $headline = '';
        $supportText = '';
        $secondaryColor = '';
        $storeHeader = '';
        $clubPromoSelectValue = '';
        if  ($userId){
            $user = Craft::$app->users->getUserById($userId);
            if ($user){
                $logoAsset = $user->siteLogo->one();
                if ($logoAsset){
                    $logo = $logoAsset->getUrl();
                }
                $whiteLogoAsset = $user->whiteLogo->one();
                if ($whiteLogoAsset){
                    $whiteLogo = $whiteLogoAsset->getUrl();
                }
                $clubPromoSelectValueAsset = $user->clubPromoSelectValue->one();
                if ($clubPromoSelectValueAsset){
                    $clubPromoSelectValue = $clubPromoSelectValueAsset->getUrl();
                }
                $colorPicker = $user->colorPicker;
                $headline = $user->headline;
                $supportText = $user->supportText;
                $secondaryColor = $user->secondaryColor;
                $storeHeaderAsset = $user->storeHeader->one();
                if ($storeHeaderAsset){
                    $storeHeader = $storeHeaderAsset->getUrl();
                }
            }
        }
        if ($heroImage){
            $heroImage = Craft::$app->assets->getAssetById($heroImage)->getUrl();
        }
        $supportImagesNew = [];
        if ($supportImages){
            $supportImages = \GuzzleHttp\json_decode($supportImages);
            foreach ($supportImages as $supportImage){
                $supportImagesNew[] = Craft::$app->assets->getAssetById($supportImage)->getUrl();
            }
        }
        $outputs = [
            'title' => $campaign['title'],
            'promotionFocus' => $campaign['promotionFocus'],
            'month' => $campaign['month'],
            'campaignHeadline' => $campaign['campaignHeadline'],
            'heroImage' => $heroImage,
            'supportImages' => $supportImagesNew,
            'promoDiscount' => $campaign['promoDiscount'],
            'promoOffer' => $campaign['promoOffer'],
            'blurb' => $campaign['blurb'],
            'expires' => $campaign['expires'],
            'primaryCoupon' => $campaign['primaryCoupon'],
            'secondaryCoupon' => $campaign['secondaryCoupon'],
            'phone' => $campaign['phone'],
            'site' => $campaign['site'],
            'address' => $campaign['address'],
            'aptSuite' => $campaign['aptSuite'],
            'hoursOther' => $campaign['hoursOther'],
            'logo' => $logo,
            'whiteLogo' => $whiteLogo,
            'clubPromoSelectValue' => $clubPromoSelectValue,
            'storeHeader' => $storeHeader,
            'secondaryColor' => $secondaryColor,
            'supportText' => $supportText,
            'headline' => $headline,
            'colorPicker' => $colorPicker,
            'campaignPrimaryColor' => $campaign['primaryColor'],
            'campaignSecondaryColor' => $campaign['secondaryColor'],
        ];
        $layout = $this->getLayoutById($layout);
        if ($layout){
            $file = file_get_contents($layout['html']);
            $file_array = explode('/',$layout['html']);
            $file_name = end($file_array);
            file_put_contents(CRAFT_BASE_PATH.'/web/img/files/'.$file_name, $file);
            $loader = new \Twig\Loader\FilesystemLoader(CRAFT_BASE_PATH.'/web/img/files/');
            $twig = new \Twig\Environment($loader);
            $template = $twig->load($file_name);
            $template = $template->render($outputs);
            unlink(CRAFT_BASE_PATH.'/web/img/files/'.$file_name);
            $templateBack = '';
            if ($layout['backHtml']){
                $file = file_get_contents($layout['backHtml']);
                $file_array = explode('/',$layout['backHtml']);
                $file_name = end($file_array);
                file_put_contents(CRAFT_BASE_PATH.'/web/img/files/'.$file_name, $file);
                $loader = new \Twig\Loader\FilesystemLoader(CRAFT_BASE_PATH.'/web/img/files/');
                $twig = new \Twig\Environment($loader);
                $templateBack = $twig->load($file_name);
                $templateBack = $templateBack->render($outputs);
                unlink(CRAFT_BASE_PATH.'/web/img/files/'.$file_name);
            }
            if ($template){
                $result = \GuzzleHttp\json_encode(['html' => htmlspecialchars_decode($template),
                    'backHtml' => htmlspecialchars_decode($templateBack),
                    'settings' => ['format' => $layout['type'], 'size' => $layout['settings'] ]],
                    JSON_UNESCAPED_SLASHES);
                return $result;
            }
        }
        return false;
    }

    public function getLayoutByCampaign($campaign)
    {
        $result = (new Query())->select('*')
            ->from('{{%print_marketing_builder}}')
            ->where(['id' => $campaign['layoutId']])
            ->one();
        return $result;
    }

    public function actionGetLayoutById($id)
    {
        $result = (new Query())->select('*')
            ->from('{{%print_marketing_builder}}')
            ->where(['id' => $id])
            ->one();
        return $this->asJson($result);
    }

    public function getLayoutById($id)
    {
        $result = (new Query())->select('*')
            ->from('{{%print_marketing_builder}}')
            ->where(['id' => $id])
            ->one();
        return $result;
    }

}
