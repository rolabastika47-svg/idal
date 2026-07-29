<?php

// @psalm-ignore-file

namespace Breakdance\Elements;

use function Breakdance\Elements\PresetSections\requirePresetsAndGetData;

function get_elements_for_builder()
{
    // Element controls will get the presets, so we must require them first.
    requirePresetsAndGetData();

    $elements = get_element_classnames();
    $visibleElements = bdox_run_filters('breakdance_builder_elements', $elements);

    $allElements = array_map(function ($elementClassName) use ($visibleElements) {
        // It's okay to instantiate this as it'll be garbage collected.
        $element = new $elementClassName(); // TODO - why are we instantiating a class with only static methods.
        $dynamicPropertyPaths = \Breakdance\Elements\FilteredGets\dynamicPropertyPaths($element);
        $controls = \Breakdance\Elements\FilteredGets\controls($element);

        $addPanelRules = \Breakdance\Elements\FilteredGets\addPanelRules($elementClassName, $visibleElements);

        return array(
            'name' => \Breakdance\Elements\FilteredGets\name($element),
            'className' => $element::className(),
            'uiIcon' => $element::uiIcon(),
            'slug' => $element::slug(),
            'category' => $element::category(),
            'badge' => $element::badge(),
            'htmlTag' => [
                'default' => $element::tag(),
                'options' => $element::tagOptions(),
                'pathToControl' => $element::tagControlPath(),
            ],
            'template' => $element::template(),
            'cssTemplate' => \Breakdance\Elements\FilteredGets\cssTemplate($element),
            'defaultCss' => $element::defaultCss(),
            'attributes' => $element::attributes(),
            'defaultProperties' => \Breakdance\Elements\FilteredGets\defaultProperties($element),
            'defaultChildren' => $element::defaultChildren(),
            'dependencies' => $element::dependencies(),
            'actions' => $element::actions(),
            'controls' => addDynamicDataInfoToControls($controls, $dynamicPropertyPaths),
            'nestingRule' => $element::nestingRule(),
            'spacingBars' => $element::spacingBars(),
            'dynamicPropertyPaths' => $dynamicPropertyPaths,
            'settings' => $element::settings(),
            'addPanelRules' => $addPanelRules,
            'experimental' => $element::experimental(),
            'availableIn' => $element::availableIn(),
            'order' => $element::order(),
            'additionalClasses' => $element::additionalClasses(),
            'projectManagement' => $element::projectManagement(),
            'propertyPathsToWhitelistInFlatProps' => \Breakdance\Elements\FilteredGets\propertyPathsToWhitelistInFlatProps($element),
            'propertyPathsToSsrElementWhenValueChanges' => $element::propertyPathsToSsrElementWhenValueChanges(),
        );
    }, $elements);

    $availableElements = array_filter(
        $allElements,
        fn($element) => in_array(BREAKDANCE_MODE, $element['availableIn'])
    );

    return array_values($availableElements);
}

function traverseAndUpdate($controls, $segments, $key, $value)
{
    if (empty($segments)) {
        return $controls;
    }

    $slug = array_shift($segments);
    $newControls = is_array($controls) ? [] : $controls;

    foreach ($controls as $controlKey => $item) {
        if ($slug === $controlKey) {
            // Root section (content, settings, design...)
            $newControls[$controlKey] = traverseAndUpdate(
                $item,
                $segments,
                $key,
                $value
            );
        } else if (isset($item['slug']) && $item['slug'] === $slug) {
            // If it's the last segment, add the key-value pair
            if (empty($segments)) {
                $item['dynamic'] = [
                    $key => $value
                ];
            } elseif (isset($item['children'])) {
                // If not the last segment, traverse deeper into children
                $item['children'] = traverseAndUpdate($item['children'], $segments, $key, $value);
            }

            $newControls[$controlKey] = $item;
        } else {
            $newControls[$controlKey] = $item;
        }
    }

    return $newControls;
}

function addDynamicDataInfoToControls($controls, $dynamicPropertyPaths)
{
    if (empty($dynamicPropertyPaths)) {
        return $controls;
    }

    $replacements = [
        '/^content\./' => 'contentSections.',
        '/^design\./' => 'designSections.',
        '/^settings\./' => 'settingsSections.'
    ];

    $newControls = $controls;

    foreach ($dynamicPropertyPaths as $property) {
        $path = preg_replace(array_keys($replacements), array_values($replacements), $property['path']);
        $path = str_replace('[]', '', $path); // Distinguishing repeater controls are not necessary.
        $segments = explode('.', $path);

        $newControls = traverseAndUpdate($newControls, $segments, 'accepts', $property['accepts']);
    }

    return $newControls;
}
