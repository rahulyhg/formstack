<?php
/**
 * Formstack plugin for Craft CMS 3.x
 *
 * Integrate Formstack into Craft CMS
 *
 * @link      https://trendyminds.com
 * @copyright Copyright (c) 2019 TrendyMinds
 */

namespace trendyminds\formstack\controllers;

use trendyminds\formstack\Formstack;

use Craft;
use craft\web\Controller;
use yii\base\Exception;

/**
 * @author    TrendyMinds
 * @package   Formstack
 * @since     2.0.0
 */
class DefaultController extends Controller
{
    protected $allowAnonymous = ['send-form'];
    
    /**
     * @return mixed
     */
    public function actionGetForm($id)
    {
        return Formstack::$plugin->formstackService->getForm($id);
    }

    public function actionSendForm()
    {
        // Only allow post requests through.
        $this->requirePostRequest();

        // If there's no form input field to determine the correct Formstack form
        if (!isset($_POST['form'])) {
            throw new Exception(403, 'An unknown error occurred.');
        }

        // If there is a form input field but it isn't all numbers
        if (!ctype_digit($_POST['form'])) {
            throw new Exception(403, 'An unknown error occurred.');
        }

        // Grab the token
        $token = Craft::parseEnv(Formstack::$plugin->getSettings()->oauthToken);

        // Set the data array.
        $postItems = [];

        // Loop through each field from the POST.
        foreach ($_POST as $key => $value) {
            /*
            * Check if it's a multipart field,
            * i.e. First Name and Last name as
            * separate fields but combines to Name on post.
            */
            if (strpos($key, '-') != 0) {
                // Grab the location of hyphen in the field name.
                $fieldName = substr($key, 0, strpos($key, '-'));

                /*
                * Transform the field name to have an underscore
                * between `field` and the field ID since
                * Formstack JSON submission requires that format.
                */
                $fieldName = 'field_'.substr($fieldName, 5);

                // Grab the name after the hypen.
                $fieldSubName = substr($key, strpos($key, '-') + 1);

                // Add data to the array transforming the hypen name.
                $postItems[$fieldName . '[' . $fieldSubName . ']'] = $value;
            } else {
                // Same as above but for regular fields.
                $fieldName = 'field_'.substr($key, 5);
                $postItems[$fieldName] = $value;
            }
        }

        // File Uploads
        foreach ($_FILES as $key => $value) {
            // Make sure there's a file.
            if (!empty($_FILES[$key]['tmp_name'])) {
                // Get the Field ID set by Formstack.
                $field = key((array)$_FILES);
                // See above for explanation.
                $field = 'field_'.substr($field, 5);
                // Get the path.
                $path = $_FILES[$key]['tmp_name'];
                // Get the name.
                $name = $_FILES[$key]['name'];
                // Get the file.
                $data = file_get_contents($path);
                // Base64 encode the data to send to Formstack.
                $data = $name . ';' . base64_encode($data);
                // Add the file to the main data array.
                $postItems[$field] = $data;
            }
        }

        // Get Form ID.
        $formId = $_POST['form'];

        // Build POST url.
        $postUrl = 'https://www.formstack.com/api/v2/form/' . $formId . '/submission.json';

        $curlConn = curl_init();
        $hedr = [];
        $hedr[] = 'Accept: multipart/form-data';
        $hedr[] = 'Content-Type: multipart/form-data';
        $hedr[] = 'Authorization: Bearer ' . $token;

        curl_setopt($curlConn, CURLOPT_HTTPHEADER, $hedr);
        curl_setopt($curlConn, CURLOPT_URL, $postUrl);
        curl_setopt($curlConn, CURLOPT_POST, count($_POST));
        curl_setopt($curlConn, CURLOPT_POSTFIELDS, Formstack::$plugin->formstackService->buildPostFields($postItems));

        $results = curl_exec($curlConn);
        curl_close($curlConn);

        return $this->asJson($results);
    }
}
