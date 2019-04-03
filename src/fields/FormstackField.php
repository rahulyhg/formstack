<?php
/**
 * Formstack plugin for Craft CMS 3.x
 *
 * Integrate Formstack into Craft CMS
 *
 * @link      https://trendyminds.com
 * @copyright Copyright (c) 2019 TrendyMinds
 */

namespace trendyminds\formstack\fields;

use trendyminds\formstack\Formstack;
use trendyminds\formstack\assetbundles\formstackfield\FormstackFieldAsset;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use yii\db\Schema;
use craft\helpers\Json;

/**
 * @author    TrendyMinds
 * @package   Formstack
 * @since     2.0.0
 */
class FormstackField extends Field
{
    // Public Properties
    // =========================================================================

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('formstack', 'Formstack Form');
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        if (gettype($value) === "array") {
            return $value;
        }

        return json_decode($value);
    }

    /**
     * @inheritdoc
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        return parent::serializeValue(json_encode($value), $element);
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Get Formstack forms to be injected into select
        $forms = Formstack::$plugin->formstackService->getForms();
        $options = [
            "value" => ""
        ];

        foreach ($forms as $form) {
            $options[] = [
                "value" => $form->id,
                "label" => $form->name
            ];
        }

        // Register our asset bundle
        Craft::$app->getView()->registerAssetBundle(FormstackFieldAsset::class);

        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        // Variables to pass down to our field JavaScript to let it namespace properly
        $jsonVars = [
            'id' => $id,
            'name' => $this->handle,
            'namespace' => $namespacedId,
            'prefix' => Craft::$app->getView()->namespaceInputId(''),
        ];

        $jsonVars = Json::encode($jsonVars);
        Craft::$app->getView()->registerJs("$('#{$namespacedId}-field').FormstackFormstackField(" . $jsonVars . ");");

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'formstack/_components/fields/FormstackField_input',
            [
                'name' => $this->handle,
                'field' => $this,
                'value' => $value,
                'options' => $options,
                'id' => $id,
                'namespacedId' => $namespacedId,
            ]
        );
    }
}
