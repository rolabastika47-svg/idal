<?php

/**
 * @psalm-ignore-file
 */

use function Breakdance\AJAX\get_nonce_for_ajax_requests;
use function Breakdance\BreakdanceOxygen\Strings\__bdox;
use function Breakdance\I18n\getLanguageAttribute;

require_once __DIR__ . "/../../loader/loader-utils.php";

$ajaxurl = admin_url('admin-ajax.php');
$useViteDevServer = shouldUseViteDevServer();

if (!$useViteDevServer) {
    $manifest = getProductionManifest();
}

$window_dot_breakdance_object_data = new stdClass();
$window_dot_breakdance_object_data->ajaxurl = $ajaxurl;
$window_dot_breakdance_object_data->ajaxnonce = get_nonce_for_ajax_requests();
$window_dot_breakdance_object_data->homeUrl = home_url();
$window_dot_breakdance_object_data->builderMode = BREAKDANCE_MODE;
$window_dot_breakdance_object_data->bdoxTranslations = \Breakdance\BreakdanceOxygen\Strings\getBdoxTranslationsForBuilder();
$window_dot_breakdance_object_data->builderDistUrl = breakdanceBuilderDistUrl();
$window_dot_breakdance_object_data->designLibrarySettingsUrl = get_admin_url() . 'admin.php?page=' . __bdox('admin_page_settings_slug') . '&tab=design_library';
?>
<!DOCTYPE html>
<html <?php echo getLanguageAttribute(); ?>>

<head>
    <title>Breakdance Templates</title>

    <script>
        // This one does not implement BreakdanceWindowObject
        window.Breakdance = <?= json_encode($window_dot_breakdance_object_data); ?>;
    </script>

    <?php

    if ($useViteDevServer) {
        echo getDevelopmentHeadLinks('design-library');
    } else {
        echo getProductionHeadLinks($manifest, 'design-library');
    }
    ?>
</head>

<body>
    <div id="design-library-wrapper"></div>
    <script type="text/javascript"
        src="<?php echo BREAKDANCE_PLUGIN_URL; ?>plugin/lib/iframe-resizer@4/iframeResizer.contentWindow.min.js"></script>

    <?php
    if ($useViteDevServer) {
        echo getDevelopmentFooterScripts('design-library');
    } else {
        echo getProductionFooterScripts($manifest, 'design-library');
    }
    ?>
</body>

</html>
