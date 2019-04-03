<?php
/**
 * Formstack plugin for Craft CMS 3.x
 *
 * Integrate Formstack into Craft CMS
 *
 * @link      https://trendyminds.com
 * @copyright Copyright (c) 2019 TrendyMinds
 */

namespace trendyminds\formstack\models;

use trendyminds\formstack\Formstack;

use Craft;
use craft\base\Model;

/**
 * @author    TrendyMinds
 * @package   Formstack
 * @since     2.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $oauthToken = '';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['oauthToken', 'string'],
            ['oauthToken', 'default', 'value' => ''],
        ];
    }
}
