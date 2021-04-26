<?php
/**
 * Print Plugin plugin for Craft CMS 3.x
 *
 * Print Plugin
 *
 * @link      WideWeb.pro
 * @copyright Copyright (c) 2020 WideWeb
 */

namespace wideweb\printplugin;

use wideweb\printplugin\services\PrintPluginService as PrintPluginServiceService;
use wideweb\printplugin\variables\MarketingVariable;
use wideweb\printplugin\variables\PrintPluginVariable;
use wideweb\printplugin\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterUrlRulesEvent;
use craft\web\twig\variables\Cp;
use craft\events\RegisterCpNavItemsEvent;

use wideweb\printplugin\variables\StaticVariable;
use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    WideWeb
 * @package   PrintPlugin
 * @since     1.0.0
 *
 * @property  PrintPluginServiceService $printPluginService
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class PrintPlugin extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * PrintPlugin::$plugin
     *
     * @var PrintPlugin
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.7';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public $hasCpSettings = true;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public $hasCpSection = true;

    public function getCpNavItem()
    {
        $item = parent::getCpNavItem();
        $item['subnav'] = [
            'pdfs' => ['label' => 'PDFs', 'url' => 'print-plugin'],
            'fields' => ['label' => 'Fields Manager', 'url' => 'print-plugin/fields'],
            'static' => ['label' => 'Static Graphic', 'url' => 'print-plugin/static'],
            'marketing' => ['label' => 'Custom Marketing', 'url' => 'print-plugin/marketing'],
        ];
        return $item;
    }
    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * PrintPlugin::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Register our site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'print-plugin/default';
            }
        );

        // Register our CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['cpActionTrigger1'] = 'print-plugin/default/do-something';
                $event->rules['print-plugin/create-pdf'] = ['template' => 'print-plugin/create-pdf/index.twig'];
                $event->rules['print-plugin/outputs'] = ['template' => 'print-plugin/outputs/index.twig'];
//                $event->rules['print-plugin/list-pdfs'] = ['template' => 'print-plugin/list-pdfs/index.twig'];
                $event->rules['print-plugin/<widgetId:\d+>'] = ['template' => 'print-plugin/pdf-detail/index.twig'];
                $event->rules['print-plugin/fields/<widgetId:\d+>'] = ['template' => 'print-plugin/pdf-detail/fields-checking.twig'];
                $event->rules['print-plugin/outputs/<widgetId:\d+>'] = ['template' => 'print-plugin/outputs/edit-pdf.twig'];
                $event->rules['print-plugin/fields'] = ['template' => 'print-plugin/fields-mapping/index.twig'];
                $event->rules['print-plugin/fields/create'] = ['template' => 'print-plugin/fields-mapping/create.twig'];
                //static
                $event->rules['print-plugin/static'] = ['template' => 'print-plugin/static/index.twig'];
                $event->rules['print-plugin/static/create'] = ['template' => 'print-plugin/static/create.twig'];
                //marketing
                $event->rules['print-plugin/marketing'] = ['template' => 'print-plugin/custom-marketing/index.twig'];
                $event->rules['print-plugin/marketing/create'] = ['template' => 'print-plugin/custom-marketing/create.twig'];
                $event->rules['print-plugin/marketing/<widgetId:\d+>'] = ['template' => 'print-plugin/custom-marketing/edit.twig'];
            }
        );

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('printPlugin', PrintPluginVariable::class);
                $variable->set('printPluginStatic', StaticVariable::class);
                $variable->set('printPluginMarketing', MarketingVariable::class);
            }
        );

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );

        /**
         * Logging in Craft involves using one of the following methods:
         *
         * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
         * Craft::info(): record a message that conveys some useful information.
         * Craft::warning(): record a warning message that indicates something unexpected has happened.
         * Craft::error(): record a fatal error that should be investigated as soon as possible.
         *
         * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
         *
         * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
         * the category to the method (prefixed with the fully qualified class name) where the constant appears.
         *
         * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
         * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
         *
         * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
         */
        Craft::info(
            Craft::t(
                'print-plugin',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'print-plugin/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
