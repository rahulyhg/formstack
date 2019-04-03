<?php
/**
 * Formstack plugin for Craft CMS 3.x
 *
 * Integrate Formstack into Craft CMS
 *
 * @link      https://trendyminds.com
 * @copyright Copyright (c) 2019 TrendyMinds
 */

namespace trendyminds\formstack\assetbundles\formstackfield;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    TrendyMinds
 * @package   Formstack
 * @since     2.0.0
 */
class FormstackFieldAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@trendyminds/formstack/assetbundles/formstackfield";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/FormstackField.js',
        ];

        parent::init();
    }
}
