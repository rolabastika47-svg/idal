<?php

namespace Breakdance\I18n\Elements;

/**
 * Translate elements at runtime.
 *
 * @see https://github.com/soflyy/breakdance-developer-docs/blob/master/i18n/readme.md
 */

function init() {
    if (!shouldTranslateElements()) return;
    add_filter( 'breakdance_element_controls', __NAMESPACE__ . '\\translateControls', 10, 2 );
    add_filter( 'breakdance_element_name', __NAMESPACE__ . '\\translateElementName', 10, 2 );
}

/**
 * @param array $controls
 * @param \Breakdance\Elements\Element $element
 * @return array
 */
function translateControls( $controls, $element ) {
    $domain = getTextDomainForElement( $element );

    if ( ! $domain ) {
        return $controls;
    }

    // Translate each section
    if ( isset( $controls['contentSections'] ) && is_array( $controls['contentSections'] ) ) {
        $controls['contentSections'] = translateControlsRecursive( $controls['contentSections'], $domain );
    }

    if ( isset( $controls['designSections'] ) && is_array( $controls['designSections'] ) ) {
        $controls['designSections'] = translateControlsRecursive( $controls['designSections'], $domain );
    }

    if ( isset( $controls['settingsSections'] ) && is_array( $controls['settingsSections'] ) ) {
        $controls['settingsSections'] = translateControlsRecursive( $controls['settingsSections'], $domain );
    }

    return $controls;
}

/**
 * @param string $name
 * @param \Breakdance\Elements\Element $element
 * @return string
 */
function translateElementName( $name, $element ) {
    $domain = getTextDomainForElement( $element );

    if ( ! $domain ) {
        return $name;
    }

    return _x( $name, 'Element name', $domain );
}

/**
 * @return bool
 */
function shouldTranslateElements() {
    return !isEnglishLocale();
}

/**
 * Check if current locale is English (to skip expensive translations)
 *
 * @return bool
 */
function isEnglishLocale() {
    $locale = get_user_locale();

    $skippedLocales = [
        'en',
        'en_US'
    ];

    return in_array( $locale, $skippedLocales, true );
}

/**
 * Get the text domain for an element based on its namespace
 *
 * @param \Breakdance\Elements\Element $element
 * @return string
 */
function getTextDomainForElement( $element ) {
    $slug = $element::slug();
    $namespace = \Breakdance\Elements\getItemNamespaceFromSlug( $slug );

    /** @var array<string, string> $namespace_map */
    $namespace_map = apply_filters( 'breakdance_element_i18n_namespace_map', [] );

    if ( isset( $namespace_map[ $namespace ] ) ) {
        return $namespace_map[ $namespace ];
    }

    return 'breakdance-elements';
}

/**
 * @param array<array-key, mixed> $controls
 * @param string $domain
 * @return array<array-key, mixed>
 */
function translateControlsRecursive( $controls, $domain ) {
    /** @psalm-suppress MissingClosureReturnType */
    return array_map( function( $control ) use ( $domain ) {
        if ( ! is_array( $control ) ) {
            return $control;
        }

        // Translate label (second parameter in c() function)
        if ( isset( $control['label'] ) && is_string( $control['label'] ) && ! empty( $control['label'] ) ) {
            $control['label'] = _x( $control['label'], 'Control label', $domain );
        }

        // Translate dropdown/button items
        if ( isset( $control['options']['items'] ) && is_array( $control['options']['items'] ) ) {
            $control['options']['items'] = array_map(
                /**
                 * @param mixed $item
                 * @return mixed
                 */
                function( $item ) use ( $domain ) {
                    if ( ! is_array( $item ) ) {
                        return $item;
                    }

                    // Translate 'label' field
                    if ( isset( $item['label'] ) && is_string( $item['label'] ) && ! empty( $item['label'] ) && $item['label'] !== 'Label' ) {
                        $item['label'] = _x( $item['label'], 'Item label', $domain );
                    }

                    if ( isset( $item['text'] ) && is_string( $item['text'] ) && ! empty( $item['text'] ) ) {
                        $item['text'] = _x( $item['text'], 'Item text', $domain );
                    }

                    return $item;
                },
                $control['options']['items']
            );
        }

        // Translate placeholder
        if ( isset( $control['options']['placeholder'] ) && is_string( $control['options']['placeholder'] ) && ! empty( $control['options']['placeholder'] ) ) {
            /** @psalm-suppress MixedArgument */
            $control['options']['placeholder'] = _x( $control['options']['placeholder'], 'Placeholder', $domain );
        }

        // Recurse into children controls
        if ( isset( $control['children'] ) && is_array( $control['children'] ) ) {
            $control['children'] = translateControlsRecursive( $control['children'], $domain );
        }

        return $control;
    }, $controls );
}

/**
 * Register element translations for a namespace
 *
 * @param string $namespace Element namespace (e.g., 'MyElements')
 * @param string $text_domain Text domain for translations
 */
function registerElementTranslations( $namespace, $text_domain ) {
    add_filter( 'breakdance_element_i18n_namespace_map',
    /**
     * @param array<string, string> $map
     * @return array<string, string>
     */
    function( $map ) use ( $namespace, $text_domain ) {
        $map[ $namespace ] = $text_domain;
        return $map;
    } );
}
