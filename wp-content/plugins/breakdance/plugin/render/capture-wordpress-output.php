<?php

/* this file is where the crappy stateful complected shit is segregated */

namespace Breakdance\Render;

use Breakdance\GlobalDefaultStylesheets\GlobalDefaultStylesheetsController;

use function Breakdance\BrowseMode\isRequestFromBrowserIframe;
use function Breakdance\GoogleFontsPlugin\buildGoogleFontUrl;
use function Breakdance\isRequestFromBuilderIframe;
use function Breakdance\Util\Timing\finish;
use function Breakdance\Util\Timing\start;

define("BREAKDANCE_HEADER_ASSETS_PLACEHOLDER", '<!-- BREAKDANCE_HEADER_DEPENDENCIES -->');
define("BREAKDANCE_FOOTER_ASSETS_PLACEHOLDER", '<!-- BREAKDANCE_FOOTER_DEPENDENCIES -->');
define("BREAKDANCE_ASSETS_PRIORITY", 1000000);

add_action('init', function () {
    if (!isRequestFromBuilderIframe()) {
        $timing = start('captureWpOutputInit');
        $globalCache = getGlobalSettingsCache();
        $globalCache['dependencyCache'] && ScriptAndStyleHolder::getInstance()->append($globalCache['dependencyCache']);
        $globalCache['cssCache'] && ScriptAndStyleHolder::getInstance()->setGlobalGeneratedCssFilePaths($globalCache['cssCache']);
        finish($timing);
    }

    /*
    TODO: would it be smart to check if there actually is a valid cache here
    getPostCssCache and getGlobalCssCache both should always return a valid
    css cache... since they generate the cache.
    but what about not being able to write to the file system, etc?
    that has to be handled somewhere
    for now, we say the cache is valid and just assume everrything works
    */
});


function registerAssetPlaceholdersInHeaderAndFooter()
{
    add_action(
        'wp_head',
        function () {
            echo BREAKDANCE_HEADER_ASSETS_PLACEHOLDER;
        },
        BREAKDANCE_ASSETS_PRIORITY
    );

    add_action(
        'wp_footer',
        function () {
            echo BREAKDANCE_FOOTER_ASSETS_PLACEHOLDER;
        },
        BREAKDANCE_ASSETS_PRIORITY
    );
}

function replaceAssetPlaceholders(string $html): string
{
    $assets = getFrontendAssets();
    $html = str_replace(BREAKDANCE_HEADER_ASSETS_PLACEHOLDER, $assets['headerHtml'], $html);
    $html = str_replace(BREAKDANCE_FOOTER_ASSETS_PLACEHOLDER, $assets['footerHtml'], $html);
    return $html;
}

/**
 *
 * @param string $templateToInclude
 * @return string
 */
function getWordPressHtmlOutput($templateToInclude)
{
    ob_start();
    /** @psalm-suppress UnresolvableInclude */
    include $templateToInclude;
    return ob_get_clean();
    /* we should probably trycatch this shit as well huh */
}

class PreRenderedContent
{
    /** @var string */
    private static $html = '';

    public static function set(string $html): void
    {
        self::$html = $html;
    }

    public static function get(): string
    {
        return self::$html;
    }
}

/**
 * @return array{headerHtml:string,footerHtml:string}
 */
function getFrontendAssets()
{
    /** @var array{headerHtml:string,footerHtml:string}|null $cache */
    static $cache = null;
    if ($cache) return $cache;
    $cache = renderHtmlFromScriptAndStyleHolder(ScriptAndStyleHolder::getInstance());

    return $cache;
}

class ScriptAndStyleHolder
{
    use \Breakdance\Singleton;

    /** @var ElementDependencyWithoutConditions */
    public $dependencies = EMPTY_DEPENDENCIES;

    /** @var GlobalGeneratedCssFilePaths */
    protected $globalGeneratedCssFilePaths = [];

    /** @var PostGeneratedCssFilePaths[]  */
    protected $postsGeneratedCssFilePaths = [];

    /**
     * @return GlobalGeneratedCssFilePaths
     */
    public function getGlobalGeneratedCssFilePaths()
    {
        return $this->globalGeneratedCssFilePaths;
    }

    /**
     * @param GlobalGeneratedCssFilePaths $cssFilePaths
     */
    public function setGlobalGeneratedCssFilePaths($cssFilePaths)
    {
        $this->globalGeneratedCssFilePaths = $cssFilePaths;
    }

    /**
     * @param int $postId
     * @param PostGeneratedCssFilePaths $cssFilePaths
     */
    public function setPostGeneratedCssFilePaths(int $postId, $cssFilePaths)
    {
        // Ignore empty arrays as the frontend expects an object and empty objects are converted to array.
        if (!$cssFilePaths) return;
        $this->postsGeneratedCssFilePaths[$postId] = $cssFilePaths;
    }

    /**
     * @return PostGeneratedCssFilePaths|null
     */
    public function getPostGeneratedCssFilePaths(int $postId)
    {
        return $this->postsGeneratedCssFilePaths[$postId] ?? null;
    }

    /**
     * @return PostGeneratedCssFilePaths[]
     */
    public function getPostsGeneratedCssFilePaths()
    {
        return $this->postsGeneratedCssFilePaths;
    }

    /**
     * @param ElementDependenciesAndConditions $dependenciesToAppend
     * @param boolean $dependencyIsBeingSideEffectedWhileRenderingCssAndMustBeCached
     * @return void
     */
    public function append($dependenciesToAppend, $dependencyIsBeingSideEffectedWhileRenderingCssAndMustBeCached = false)
    {

        /**
         * @var ElementDependenciesAndConditions
         */
        $dependenciesToAppend = bdox_run_filters('breakdance_append_dependencies', $dependenciesToAppend);

        $dependenciesWithVersionedScripts = appendVersionToUrlForCacheBustingToScriptsAndStyles($dependenciesToAppend);
        $mergedDeps = dependencies_array_merge_recursive_without_conditions([$this->dependencies, $dependenciesWithVersionedScripts]);
        $this->dependencies = removeDuplicatedAndEmptyDependencies($mergedDeps);

        if ($dependencyIsBeingSideEffectedWhileRenderingCssAndMustBeCached) {
            DependencyCache::getInstance()->append($dependenciesToAppend);
        }
    }
}

/**
 * @param string $styleSheetUrl
 * @return string
 */
function renderStylesheetTag(string $styleSheetUrl): string
{
    return empty($styleSheetUrl)
        ? "<!-- Error: empty stylesheet URL -->" . PHP_EOL
        : "<link rel=\"stylesheet\" href=\"{$styleSheetUrl}\" />" . PHP_EOL;
}

/**
 * @param string[] $arr
 * @param string $key
 * @return string
 */
function renderStylesheetTagFromArrByKeyOrReturnEmptyString($arr, $key)
{
    return empty($arr[$key]) ? '' : renderStylesheetTag($arr[$key]);
}

/**
 * @param string $rawHtml
 * @param string $metaTagName
 * @return string
 */
function renderHtmlWrappedWithMetaTagsWhenInIframe($rawHtml, $metaTagName): string
{
    if ((isRequestFromBuilderIframe() || isRequestFromBrowserIframe())) {
        return (PHP_EOL . "<meta name='{$metaTagName}' content='start'/>"
            . PHP_EOL
            . trim($rawHtml)
            . PHP_EOL
            . "<meta name='{$metaTagName}' content='end'/>" . PHP_EOL);
    } else {
        if (empty($rawHtml)) return '';
        return PHP_EOL . trim($rawHtml) . PHP_EOL;
    }
}

/**
 * @param ScriptAndStyleHolder $holder
 * @return array{headerHtml:string,footerHtml:string}
 */
function renderHtmlFromScriptAndStyleHolder(ScriptAndStyleHolder $holder)
{
    $timing = start('renderHtmlFromScriptAndStyleHolder');
    $globalCssFilePaths = $holder->getGlobalGeneratedCssFilePaths();

    $globalDefaultStylesheets = implode(
        '',
        array_map(
            fn($stylesheetUrl) => renderStylesheetTag($stylesheetUrl),
            GlobalDefaultStylesheetsController::getInstance()->stylesheetUrls
        )
    );

    $cssMappings = [
        'globalSettingsCssFilePath' => '__breakdance_global_settings_css_stylesheet_tag',
        'globalPresetsCssFilePath' => '__breakdance_presets_css_stylesheet_tag',
        'globalVariablesCssFilePath' => '__breakdance_variables_css_stylesheet_tag',
        'globalOxySelectorsCssFilePath' => '__breakdance_oxy_selectors_css_stylesheet_tag',
        'globalSelectorsCssFilePath' => 'breakdance_global_selectors_css', // TODO: Why is this hook different?
    ];

    $renderedCss = [];

    foreach ($cssMappings as $cssKey => $filter) {
        $renderedCss[$cssKey] = '';
        if (!empty($globalCssFilePaths[$cssKey]) && !isRequestFromBrowserIframe()) {
            $renderedCss[$cssKey] = (string) apply_filters(
                $filter,
                renderStylesheetTagFromArrByKeyOrReturnEmptyString($globalCssFilePaths, $cssKey)
            );
        }
    }

    /**
     * @psalm-suppress PossiblyUndefinedStringArrayOffset
     * @var string $maybeGlobalSettingsCss
     */
    $maybeGlobalSettingsCss = bdox_run_filters(
        'breakdance_global_settings_css',
        $renderedCss['globalSettingsCssFilePath']
    );

    /**
     * @psalm-suppress PossiblyUndefinedStringArrayOffset
     * @var string $maybeGlobalPresetsCss
     */
    $maybeGlobalPresetsCss = bdox_run_filters(
        'breakdance_presets_css',
        $renderedCss['globalPresetsCssFilePath']
    );

    /**
     * @psalm-suppress PossiblyUndefinedStringArrayOffset
     * @var string $maybeGlobalVariablesCss
     */
    $maybeGlobalVariablesCss = bdox_run_filters(
        'breakdance_variables_css',
        $renderedCss['globalVariablesCssFilePath']
    );

    /**
     * @psalm-suppress PossiblyUndefinedStringArrayOffset
     * @var string $maybeGlobalSelectorsCss
     */
    $maybeGlobalSelectorsCss = bdox_run_filters(
        'breakdance_global_selectors_css',
        $renderedCss['globalSelectorsCssFilePath']
    );

    /**
     * @psalm-suppress PossiblyUndefinedStringArrayOffset
     * @var string $maybeOxySelectorsCss
     */
    $maybeOxySelectorsCss = bdox_run_filters(
        'breakdance_oxy_selectors_css',
        $renderedCss['globalOxySelectorsCssFilePath']
    );

    $maybeDefaultCssForAllElements = renderStylesheetTagFromArrByKeyOrReturnEmptyString(
        $globalCssFilePaths,
        'defaultCssForAllElementsFilePath'
    );

    $maybeDefaultCssForElementsOfRenderedPosts = '';
    foreach ($holder->getPostsGeneratedCssFilePaths() as $postId => $postCssFilePaths) {
        if (!empty($postCssFilePaths['postDefaultsCssFilePath'])) {
            $maybeDefaultCssForElementsOfRenderedPosts .= renderStylesheetTag(
                $postCssFilePaths['postDefaultsCssFilePath']
            ) . PHP_EOL;
        }
    }

    $maybeCssOfAllRenderedPosts = '';
    foreach ($holder->getPostsGeneratedCssFilePaths() as $postId => $postCssFilePaths) {
        $maybeCssOfRenderedPost = renderStylesheetTagFromArrByKeyOrReturnEmptyString(
            $postCssFilePaths,
            'postCssFilePath'
        );
        $maybeCssOfAllRenderedPosts .= $maybeCssOfRenderedPost . PHP_EOL;
    }

    // Google Font dependencies are stored separately as an array of font family names so
    // that we can retrieve all the selected font families in a single CSS file request
    if (array_key_exists('googleFonts', $holder->dependencies)) {
        $googleFontUrl = buildGoogleFontUrl($holder->dependencies['googleFonts']);

        if ($googleFontUrl) {
            if (!array_key_exists('styles', $holder->dependencies)) {
                $holder->dependencies['styles'] = [];
            }

            $holder->dependencies['styles'][] = $googleFontUrl;
        }
    }

    $maybeCssOfRenderedElementsDependencies = '';
    if (array_key_exists('styles', $holder->dependencies)) {
        $maybeCssOfRenderedElementsDependencies .= array_reduce(
            $holder->dependencies['styles'],
            /**
             * @param string $acc
             * @param string $stylesheetUrl
             *
             * @return string
             */
            function ($acc, $stylesheetUrl) {
                return $acc . renderStylesheetTag($stylesheetUrl);
            },
            ''
        );
    }

    $headerInlineStyles = '';
    if (array_key_exists('inlineStyles', $holder->dependencies)) {
        $headerInlineStyles .= array_reduce(
            $holder->dependencies['inlineStyles'],
            /**
             * @param string $acc
             * @param string $inlineStyle
             *
             * @return string
             */
            function ($acc, $inlineStyle) {
                // Add an ID with the style hash to enable testing the order of the scripts
                return $acc . '<style id=' . md5($inlineStyle) . '>' . $inlineStyle . '</style>' . PHP_EOL;
            },
            ''
        );
    }

    /**
     * 1. global-defaults - This is first, because it shouldn’t override anything
     * (i.e. our WooCommerce default styles)
     *
     * 2. elements-dependencies - This is second, because it also shouldn’t override
     * anything (except maybe the global defaults), and everything should be able to
     * override it.
     *
     * 3. element-defaults - This is third, since it should override the dependencies
     * and global defaults. (Imagine a slider library that ships with stock styles,
     * but the slider element that uses the library wants to customize those styles)
     *
     * 4. global-settings - This should be able to override any dependencies and default CSS
     *
     * 5. selectors - a user creating a custom selector wants to change the style of something.
     * Therefore, this comes after all CSS except for element-specific CSS
     *
     * 6. element-dependencies-inline - inline style tags may want to style a specific
     * element, be dynamically generated, or something else. I really don’t think we need
     * inline style dependencies, but a long as we have them, they should be treated the
     * same way as the element-specific CSS (all-rendered-posts)
     *
     * 7. all-rendered-posts - element-specific CSS should override all other CSS
     */

    $headerHtml = '<!-- [HEADER ASSETS] -->'
        . renderHtmlWrappedWithMetaTagsWhenInIframe(
            $globalDefaultStylesheets,
            'breakdance:header-assets:css:global-default-stylesheets'
        )
        . renderHtmlWrappedWithMetaTagsWhenInIframe(
            $maybeCssOfRenderedElementsDependencies,
            'breakdance:header-assets:css:elements-dependencies'
        )
        . renderHtmlWrappedWithMetaTagsWhenInIframe(
            $maybeDefaultCssForElementsOfRenderedPosts . $maybeDefaultCssForAllElements,
            'breakdance:header-assets:css:elements-defaults'
        )
        . renderHtmlWrappedWithMetaTagsWhenInIframe(
            $maybeGlobalSettingsCss,
            'breakdance:header-assets:css:global-settings'
        )
        . renderHtmlWrappedWithMetaTagsWhenInIframe(
            $maybeGlobalPresetsCss,
            'breakdance:header-assets:css:presets'
        )
        . renderHtmlWrappedWithMetaTagsWhenInIframe(
            $maybeOxySelectorsCss,
            'breakdance:header-assets:css:oxy-selectors'
        )
        . renderHtmlWrappedWithMetaTagsWhenInIframe(
            $maybeGlobalSelectorsCss,
            'breakdance:header-assets:css:selectors'
        )
        . renderHtmlWrappedWithMetaTagsWhenInIframe(
            $maybeGlobalVariablesCss,
            'breakdance:header-assets:css:variables'
        )
        . renderHtmlWrappedWithMetaTagsWhenInIframe(
            $headerInlineStyles,
            'breakdance:header-assets:css:elements-dependencies-inline'
        )
        . renderHtmlWrappedWithMetaTagsWhenInIframe(
            $maybeCssOfAllRenderedPosts,
            'breakdance:header-assets:css:all-rendered-posts'
        )
        . '<!-- [/EOF HEADER ASSETS] -->'
        . PHP_EOL;

    $footerHtml = "";

    if (array_key_exists('scripts', $holder->dependencies)) {
        // JS loaded on the footer makes the HTML rendering faster
        // Note: we're also deferring inline scripts
        $footerHtml .= array_reduce(
            $holder->dependencies['scripts'],
            /**
             * @param string $acc
             * @param string $scriptUrl
             * @return string
             */
            function ($acc, $scriptUrl) {
                return "$acc<script src='$scriptUrl' defer></script>" . PHP_EOL;
            },
            ''
        );
    }

    if (array_key_exists('inlineScripts', $holder->dependencies)) {
        // these must load *after* the deferred scripts since they'll run in order.
        $footerHtml .= array_reduce(
            $holder->dependencies['inlineScripts'],
            /**
             * @param string $acc
             * @param string $inlineScript
             * @return string
             */
            function ($acc, $inlineScript) {
                // this is the equivalent of adding "defer" to a script with "src", since it'll only run after those scripts.
                $deferredInlineScript = "document.addEventListener('DOMContentLoaded', function(){ $inlineScript })";
                return "$acc<script>$deferredInlineScript </script>" . PHP_EOL;
            },
            ''
        );
    }

    finish($timing);

    return [
        'footerHtml' => $footerHtml,
        'headerHtml' => $headerHtml,
    ];
}

/**
 * @param ElementDependencyWithoutConditions $dependencies
 * @return ElementDependencyWithoutConditions
 */
function removeDuplicatedAndEmptyDependencies($dependencies)
{
    $deduplicatedDependencies = EMPTY_DEPENDENCIES;

    $deduplicatedDependencies['scripts'] = isset($dependencies['scripts'])
        ? array_values(
            array_filter(
                array_unique($dependencies['scripts']),
                '\Breakdance\Render\removeFalsyDependency'
            )
        )
        : [];

    $deduplicatedDependencies['inlineScripts'] = isset($dependencies['inlineScripts'])
        ? array_values(
            array_filter(
                array_unique($dependencies['inlineScripts']),
                '\Breakdance\Render\removeFalsyDependency'
            )
        )
        : [];

    $deduplicatedDependencies['styles'] = isset($dependencies['styles'])
        ? array_values(
            array_filter(
                array_unique($dependencies['styles']),
                '\Breakdance\Render\removeFalsyDependency'
            )
        )
        : [];

    $deduplicatedDependencies['inlineStyles'] = isset($dependencies['inlineStyles'])
        ? array_values(
            array_filter(
                array_unique($dependencies['inlineStyles']),
                '\Breakdance\Render\removeFalsyDependency'
            )
        )
        : [];

    $deduplicatedDependencies['googleFonts'] = isset($dependencies['googleFonts'])
        ? array_values(
            array_filter(
                array_unique($dependencies['googleFonts']),
                '\Breakdance\Render\removeFalsyDependency'
            )
        )
        : [];

    return $deduplicatedDependencies;
}

/**
 * @param null|string $dependency
 * @return bool
 */
function removeFalsyDependency($dependency)
{
    // null, "", are invalid dependencies
    return !!$dependency;
}

/**
 * @param ElementDependencyWithoutConditions $dependencies
 * @return ElementDependencyWithoutConditions
 */
function appendVersionToUrlForCacheBustingToScriptsAndStyles($dependencies)
{
    $depsWithVersionedScriptsAndStyles = $dependencies;

    $depsWithVersionedScriptsAndStyles['styles'] = isset($dependencies['styles'])
        ? array_map('\Breakdance\Render\appendVersionToUrlForCacheBusting', $dependencies['styles'])
        : [];

    $depsWithVersionedScriptsAndStyles['scripts'] = isset($dependencies['scripts'])
        ? array_map('\Breakdance\Render\appendVersionToUrlForCacheBusting', $dependencies['scripts'])
        : [];

    return $depsWithVersionedScriptsAndStyles;
}

define('EMPTY_DEPENDENCIES', [
    'scripts' => [],
    'inlineScripts' => [],
    'styles' => [],
    'inlineStyles' => [],
    'googleFonts' => [],
]);
