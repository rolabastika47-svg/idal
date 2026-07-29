<?php

namespace EssentialElements;

/**
 * Renders button classes using Twig and the getButtonClasses macro
 *
 * @param array $propertiesData The properties data array containing design settings
 * @param string $propertyPath The path to the button styles in the properties data (e.g., 'design.form.submit_button.styles_v2')
 * @param string $defaultStyle The default button style to use if none is set (e.g., 'primary', 'secondary', 'custom', 'text')
 * @param string $additionalClasses Optional additional CSS classes to append
 * @return string The rendered button classes
 */
function getButtonClassesFromDesign($propertiesData, $propertyPath, $defaultStyle = 'primary', $additionalClasses = '')
{
    $buttonClassesTwig = sprintf('{{ trim_whitespace(macros.getButtonClasses(%s, "%s")) }} %s', $propertyPath, $defaultStyle, $additionalClasses);
    $result = \Breakdance\Render\Twig::getInstance()->runTwig($buttonClassesTwig, $propertiesData);
    return trim($result);
}

