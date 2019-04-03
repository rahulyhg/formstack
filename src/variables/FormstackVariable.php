<?php
/**
 * Formstack plugin for Craft CMS 3.x
 *
 * Integrate Formstack into Craft CMS
 *
 * @link      https://trendyminds.com
 * @copyright Copyright (c) 2019 TrendyMinds
 */

namespace trendyminds\formstack\variables;

use trendyminds\formstack\Formstack;

use Craft;

/**
 * @author    TrendyMinds
 * @package   Formstack
 * @since     2.0.0
 */
class FormstackVariable
{
    // Public Methods
    // =========================================================================

    /**
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
}
