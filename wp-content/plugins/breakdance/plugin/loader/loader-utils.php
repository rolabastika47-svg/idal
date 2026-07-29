<?php

// @psalm-ignore-file
use function Breakdance\Render\appendVersionToUrlForCacheBusting;

function getProductionManifest(string $relativePathToDist, $urlPath = false)
{
    $manifest = json_decode(file_get_contents("{$relativePathToDist}/manifest.json"), true);

    /*
     * Looks like: ["app.js" => "/js/app.dd8c7caa.js"]
     * @var array{string, string}
     */
    $manifestWithCorrectUrls = [];

    foreach ($manifest as $name => $url) {
        $url = str_replace("//breakdance.local:8080/", "/", $url);

        $manifestWithCorrectUrls[$name] = $urlPath ? $urlPath . $url : $relativePathToDist . $url;
    }

    return $manifestWithCorrectUrls;
}



function getProductionHeadLinks($manifest, $appName)
{
    ob_start();

    loadI18nScripts($appName);

    if (key_exists("favicon.svg", $manifest)) {
        $faviconLight = BREAKDANCE_MODE === 'oxygen' ? $manifest["favicon-oxygen.svg"] : $manifest["favicon.svg"];
        $faviconDark = BREAKDANCE_MODE === 'oxygen' ? $manifest["favicon-oxygen-dark.svg"] : $manifest["favicon-dark.svg"];
?>
        <link rel="icon" class="js-site-favicon" type="image/svg+xml" href="<?php echo $faviconLight; ?>" media="(prefers-color-scheme: light)">
        <link rel="icon" class="js-site-favicon" type="image/svg+xml" href="<?php echo $faviconDark; ?>" media="(prefers-color-scheme: dark)">
    <?php
    }

    if (key_exists('chunk-vendors.css', $manifest)) {
    ?>
        <link href="<?php echo appendVersionToUrlForCacheBusting($manifest['chunk-vendors.css']) ?>" rel="preload" as="style">
        <link href="<?php echo appendVersionToUrlForCacheBusting($manifest['chunk-vendors.css']) ?>" rel=stylesheet>
    <?php
    }

    if (key_exists('chunk-common.css', $manifest)) {
    ?>
        <link href="<?php echo appendVersionToUrlForCacheBusting($manifest['chunk-common.css']); ?>" rel="preload" as="style">
        <link href="<?php echo appendVersionToUrlForCacheBusting($manifest['chunk-common.css']); ?>" rel=stylesheet>
    <?php
    }

    if (key_exists("{$appName}.css", $manifest)) {
    ?>
        <link href="<?php echo appendVersionToUrlForCacheBusting($manifest["{$appName}.css"]); ?>" rel="stylesheet">
        <link href="<?php echo appendVersionToUrlForCacheBusting($manifest["{$appName}.css"]); ?>" rel="preload" as="style">
    <?php
    }

    if (key_exists('chunk-vendors.js', $manifest)) {
    ?>
        <link href="<?php echo appendVersionToUrlForCacheBusting($manifest['chunk-vendors.js']); ?>" rel="preload" as="script">
    <?php
    }

    if (key_exists('chunk-common.js', $manifest)) {
    ?>
        <link href="<?php echo appendVersionToUrlForCacheBusting($manifest['chunk-common.js']); ?>" rel="preload" as="script">
    <?php
    }

    if (key_exists("{$appName}.js", $manifest)) {
    ?>
        <link href="<?php echo appendVersionToUrlForCacheBusting($manifest["{$appName}.js"]); ?>" rel="preload" as="script">
    <?php
    }

    return ob_get_clean();
}

function getProductionFooterScripts($manifest, $appName)
{
    ob_start();

    if (key_exists('chunk-vendors.js', $manifest)) {
    ?>
        <script src="<?php echo appendVersionToUrlForCacheBusting($manifest['chunk-vendors.js']); ?>"></script>
    <?php
    }

    if (key_exists('chunk-common.js', $manifest)) {
    ?>
        <script src="<?php echo appendVersionToUrlForCacheBusting($manifest['chunk-common.js']); ?>"></script>
    <?php
    }

    if (key_exists("{$appName}.js", $manifest)) {
    ?>
        <script src="<?php echo appendVersionToUrlForCacheBusting($manifest["{$appName}.js"]); ?>"></script>
    <?php
    }

    return ob_get_clean();
}



function getDevelopmentHeadLinks($appName)
{
    ob_start();

    loadI18nScripts($appName);
    ?>
    <link rel="icon" class="js-site-favicon" type="image/svg+xml" href="//breakdance.local:8080/<?php echo BREAKDANCE_MODE === 'oxygen' ? 'favicon-oxygen.svg' : 'favicon.svg'; ?>" media="(prefers-color-scheme: light)">
    <link rel="icon" class="js-site-favicon" type="image/svg+xml" href="//breakdance.local:8080/<?php echo BREAKDANCE_MODE === 'oxygen' ? 'favicon-oxygen-dark.svg' : 'favicon-dark.svg'; ?>" media="(prefers-color-scheme: dark)">
    <link href="//breakdance.local:8080/js/chunk-vendors.js" rel="preload" as="script">
    <link href="//breakdance.local:8080/js/chunk-common.js" rel="preload" as="script">
    <link href="//breakdance.local:8080/js/<?php echo $appName ?>.js" rel="preload" as="script">
<?php

    return ob_get_clean();
}

function getDevelopmentFooterScripts($appName)
{
    ob_start();
?>
    <script type="text/javascript" src="//breakdance.local:8080/js/chunk-vendors.js"></script>
    <script type="text/javascript" src="//breakdance.local:8080/js/chunk-common.js"></script>
    <script type="text/javascript" src="//breakdance.local:8080/js/<?php echo $appName ?>.js"></script>
<?php
    return ob_get_clean();
}

/**
 * @param string $handle // Must be a handle that WP registers automatically
 * @return string
 */
function getEnqueuedScriptUrl($handle) {
    wp_enqueue_script( $handle);

    $wp_scripts = wp_scripts();
    $script = $wp_scripts->registered[$handle];

    $url = $script->src . "?ver=" . $script->ver;

    if (filter_var($url, FILTER_VALIDATE_URL)) {
        return $url;
    }

    return \site_url($url);
}

function loadI18nScripts($appName) {

    $language = get_user_locale();
    $path = plugin_dir_path( __BREAKDANCE_PLUGIN_FILE__ ) . 'languages/breakdance-' . $language .'.json';

    if (!file_exists($path)) {
        $language = str_replace("_", "-", $language);
        $path = plugin_dir_path( __BREAKDANCE_PLUGIN_FILE__ ) . 'languages/breakdance-' . $language .'.json';
    }

    $json = load_script_translations($path, 'builder', 'breakdance') ?: '{}';
    $json = apply_filters('breakdance_i18n_json', $json);

    // wp i18n requires wp-hooks
    $hooksUrl = getEnqueuedScriptUrl('wp-hooks');
    $i18nUrl = getEnqueuedScriptUrl('wp-i18n');

    // Copied from what "wp_set_script_translations" outputs
    $output = <<<JS
      ( function( domain, translations ) {
          if (!translations?.locale_data) return;
          var localeData = translations.locale_data[ domain ] || translations.locale_data.messages;
          localeData[""].domain = domain;
          wp.i18n.setLocaleData( localeData, domain );
      } )( "breakdance", {$json} );
    JS;

    global $wp_locale;
    $textDirection = $wp_locale->text_direction;

    ?>
      <script type="text/javascript" src="<?= $hooksUrl ?>"></script>
      <script type="text/javascript" src="<?= $i18nUrl ?>"></script>
      <script>
        wp.i18n.setLocaleData( { 'text direction\u0004<?= $textDirection ?>': [ '<?= $textDirection ?>' ] } );
      </script>

      <script>
        <?php echo $output; ?>
      </script>
    <?php
  }
