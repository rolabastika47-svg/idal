<?php

namespace Breakdance\Subscription;

use function Breakdance\Elements\getSsrErrorMessage;
use function \Breakdance\BreakdanceOxygen\Strings\__bdox;

/**
 * @param string $label
 * @return string
 */
function appendProToLabelInFreeMode($label)
{
    return isFreeMode() ? "$label [PRO]" : $label;
}

/**
 * @param TemplateCondition $condition
 * @return TemplateCondition
 */
function makeConditionProOnlyByDefault($condition) {
    if (!array_key_exists('proOnly', $condition)) {
        $condition['proOnly'] = true;
    }
    return $condition;
}

/**
 * @param string $message
 * @return string
 */
function getFreeModeErrorMessage($message){
    return <<<HTML
<div class="breakdance-pro-only-element-notice">
    {$message}
</div>
HTML;
}

/**
 * @param string $elementName
 * @return string
 */
function getFreeModeOnFrontendError($elementName)
{
    /* translators: %1$s: element name, %2$s: plugin name */
    $warningText = sprintf(__('The <b>%1$s</b> element is only available in %2$s Pro.', 'breakdance'), $elementName, __bdox('plugin_name'));
    return getFreeModeErrorMessage($warningText);
}

/**
 * @param string $elementName
 * @return string
 */
function getProOnlyConditionMessage($elementName)
{
    /* translators: %s is the element name */
    $warning = sprintf(__('A Pro-only visibility condition was used on a <b>%s</b> element.', 'breakdance'), $elementName);
    return getFreeModeErrorMessage($warning);
}
