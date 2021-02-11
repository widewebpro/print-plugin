<?php
/**
 * Print Plugin plugin for Craft CMS 3.x
 *
 * Print Plugin
 *
 * @link      WideWeb.pro
 * @copyright Copyright (c) 2020 WideWeb
 */

namespace wideweb\printplugin\variables;

use craft\elements\User;
use wideweb\printplugin\PrintPlugin;

use Craft;
use yii\db\Query;

/**
 * Print Plugin Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.printPlugin }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    WideWeb
 * @package   PrintPlugin
 * @since     1.0.0
 */
class PrintPluginVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Whatever you want to output to a Twig template can go into a Variable method.
     * You can have as many variable functions as you want.  From any Twig template,
     * call it like this:
     *
     *     {{ craft.printPlugin.exampleVariable }}
     *
     * Or, if your variable requires parameters from Twig:
     *
     *     {{ craft.printPlugin.exampleVariable(twigValue) }}
     *
     * @param null $optional
     * @return string
     */
    public function exampleVariable($optional = null)
    {
        $result = "And away we go to the Twig template...";
        if ($optional) {
            $result = "I'm feeling optional today...";
        }
        return $result;
    }

    public function getAllPdf()
    {
        $pdfs = (new Query())->select("*")->from('{{%print_pdfs}}')->all();
        return $pdfs;
    }

    public function getPdfById($id)
    {
        $pdfs = (new Query())->select("*")->from('{{%print_pdfs}}')->where('id =' . $id)->one();
        return $pdfs;
    }

    public function getFieldsFromUserModel()
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
        return $result;
    }

    public function getAllFields()
    {
        $fields = (new Query())->select("*")->from('{{%print_fields}}')->all();
        return $fields;
    }
}
