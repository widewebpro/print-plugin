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
class CategoriesVariable
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
    public function getAllCategories($type)
    {
        return PrintPlugin::$plugin->categories->getAllCategories($type);
    }

    public function getCategoryById($id)
    {
        $result = PrintPlugin::$plugin->categories->getCategoryById($id);
        $result['userGroup'] = \Opis\Closure\unserialize($result['userGroup']);
        return $result;
    }
}
