<?php

namespace Breakdance\BreakdanceOxygen\Strings;

/**
 * public API
 * @param string $key
 * @return string
 */
function __bdox($key)
{

    if (BREAKDANCE_MODE === 'breakdance') {
        return StringsHolder::getInstance()->breakdance_translations[$key];
    }

    if (BREAKDANCE_MODE === 'oxygen') {
        return StringsHolder::getInstance()->oxygen_translations[$key];
    }

    return '';
}

/**
 * @return array{oxygen:array<string,string>,breakdance:array<string,string>}
 */
function getBdoxTranslationsForBuilder()
{
    return [
        'oxygen' => StringsHolder::getInstance()->oxygen_translations,
        'breakdance' => StringsHolder::getInstance()->breakdance_translations,
    ];
}

/**
 * @param bool $translate
 * @return array<string,string>
 */
function get_breakdance_translations($translate = false)
{
    return [
        'global_blocks' => $translate ? __('Global Blocks', 'breakdance') : 'Global Blocks',
        'global_block' => $translate ? __('Global Block', 'breakdance') : 'Global Block',

        'plugin_name' => 'Breakdance',
        'admin_page_settings_slug' => 'breakdance_settings',

        'support_link' => 'https://breakdance.com/support/',
        'docs_link_security' => 'https://breakdance.com/documentation/other/security/',
        'docs_link_element_studio' => 'https://breakdance.com/documentation/developers/element-studio/',
        'docs_link_troubleshooting_500' => 'https://breakdance.com/documentation/troubleshooting/troubleshooting-500-50x-errors/',
        'docs_link_troubleshooting_403' => 'https://breakdance.com/documentation/troubleshooting/403-errors/',

        'website_link_upgrade_to_pro_iframe' => 'https://breakdance.com/upgrade-iframe/',

        'partner_discounts_link' => 'https://breakdance.com/portal/partner-discounts',

        'meta_prefix'  => 'breakdance_',
        '_meta_prefix' => '_breakdance_',
        'table_prefix' => 'breakdance_'
    ];
}

/**
 * @param bool $translate
 * @return array<string,string>
 */
function get_oxygen_translations($translate = false)
{
    return [
        'global_blocks' => $translate ? __('Components', 'breakdance') : 'Components',
        'global_block' => $translate ? __('Component', 'breakdance') : 'Component',

        'plugin_name' => 'Oxygen',
        'admin_page_settings_slug' => 'oxygen_settings',

        'support_link' => 'https://oxygenbuilder.com/support/',
        'docs_link_security' => 'https://oxygenbuilder.com/documentation/other/security/',
        'docs_link_element_studio' => 'https://oxygenbuilder.com/documentation/developers/element-studio/',
        'docs_link_troubleshooting_500' => 'https://oxygenbuilder.com/documentation/troubleshooting/troubleshooting-500-50x-errors/',
        'docs_link_troubleshooting_403' => 'https://oxygenbuilder.com/documentation/troubleshooting/403-errors/',

        'website_link_upgrade_to_pro_iframe' => 'https://breakdance.com/upgrade-iframe/',

        'partner_discounts_link' => 'https://oxygenbuilder.com/portal/partner-discounts',

        'meta_prefix'  => 'oxygen_',
        '_meta_prefix' => '_oxygen_',
        'table_prefix' => 'oxygen_'
    ];
}

/**
 * private
 */
class StringsHolder
{

    use \Breakdance\Singleton;

    /**
     * @var array<string,string>
     */
    public $oxygen_translations = [];

    /**
     * @var array<string,string>
     */
    public $breakdance_translations = [];

    /**
     * @param bool $translate
     */
    function setupStrings($translate = false)
    {
        $this->oxygen_translations = get_oxygen_translations($translate);
        $this->breakdance_translations = get_breakdance_translations($translate);
    }
}

// Initialize immediately with untranslated strings
StringsHolder::getInstance()->setupStrings(false);

// Update with translated strings after WordPress is ready
add_action('init', function() {
    StringsHolder::getInstance()->setupStrings(true);
});
