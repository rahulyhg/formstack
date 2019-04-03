<?php
/**
 * Formstack plugin for Craft CMS 3.x
 *
 * Integrate Formstack into Craft CMS
 *
 * @link      https://trendyminds.com
 * @copyright Copyright (c) 2019 TrendyMinds
 */

namespace trendyminds\formstack\services;

use trendyminds\formstack\Formstack;

use Craft;
use craft\base\Component;
use GuzzleHttp\Client;

/**
 * @author    TrendyMinds
 * @package   Formstack
 * @since     2.0.0
 */
class FormstackService extends Component
{
    public function getForms()
    {
        if (Craft::$app->cache->exists("formstackForms")) {
            return Craft::$app->cache->get("formstackForms");
        }

        $url = "https://www.formstack.com/api/v2/form.json?oauth_token=" . $this->_getToken();

        $client = new Client();
        $response = $client->get($url);
        $output = json_decode($response->getBody());

        usort($output->forms, function($a, $b) {
            return strcmp(strtolower($a->name), strtolower($b->name));
        });

        Craft::$app->cache->set("formstackForms", $output->forms);

        return $output->forms;
    }

    public function getForm($id)
    {
        // Set and Get the endpoint by passing the ID and the oauth token.
        $url = "https://www.formstack.com/api/v2/form/$id?oauth_token=" . $this->_getToken();

        $client = new Client();
        $response = $client->get($url);
        $output = json_decode($response->getBody());

        $html = $output->html;

        $start = '<form';
        $end = '</form>';


        return $this->_extractForm($html, $start, $end);
    }

    public function buildPostFields($data, $existingKeys = '', &$returnArray = [])
    {
        if (($data instanceof CURLFile) or !(is_array($data) or is_object($data))) {
            $returnArray[$existingKeys ] =$data;
            return $returnArray;
        } else {
            foreach ($data as $key => $item) {
                $this->buildPostFields($item, $existingKeys?$existingKeys."[$key]":$key, $returnArray);
            }

            return $returnArray;
        }
    }

    private function _extractForm($data, $startEl, $endEl)
    {
        $start = strpos($data, $startEl);
        $end = strpos($data, $endEl);

        return substr($data, $start, ($end - $start) + strlen($endEl));
    }

    private function _getToken()
    {
        return Craft::parseEnv(Formstack::$plugin->getSettings()->oauthToken);
    }
}
