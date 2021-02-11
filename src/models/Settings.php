<?php
/**
 * Print Plugin plugin for Craft CMS 3.x
 *
 * Print Plugin
 *
 * @link      WideWeb.pro
 * @copyright Copyright (c) 2020 WideWeb
 */

namespace wideweb\printplugin\models;

use craft\services\Fields;
use wideweb\printplugin\PrintPlugin;

use Craft;
use craft\base\Model;

/**
 * PrintPlugin Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    WideWeb
 * @package   PrintPlugin
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Some field model attribute
     *
     * @var string
     */
    public $choiceFolder = 0;
    public $choiceFormat = 0;
    public $choiceUserGroup = 0;
    public $comingSoon = 0;
    public $choiceFormatFile = 0;

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['choiceFolder', 'default'],
            ['choiceFormat', 'default'],
            ['choiceUserGroup', 'default'],
            ['comingSoon', 'default'],
            ['choiceFormatFile', 'default'],
        ];
    }
}
