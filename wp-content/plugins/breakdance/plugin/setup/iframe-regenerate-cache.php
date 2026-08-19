<?php

require_once __DIR__ . "/../loader/loader-utils.php";

$useViteDevServer = shouldUseViteDevServer();

if (!$useViteDevServer) {
    $manifest = getProductionManifest();
}

if ($useViteDevServer) {
    echo getDevelopmentHeadLinks('settings-tools-regenerate-cache');
} else {
    echo getProductionHeadLinks($manifest, 'settings-tools-regenerate-cache');
}

$ajaxurl = admin_url('admin-ajax.php');

$window_dot_breakdance_object_data = new stdClass();
$window_dot_breakdance_object_data->ajaxurl = $ajaxurl;
$window_dot_breakdance_object_data->ajaxnonce = \Breakdance\AJAX\get_nonce_for_ajax_requests();
$window_dot_breakdance_object_data->subscriptionMode = \Breakdance\Subscription\getSubscriptionMode();
$window_dot_breakdance_object_data->builderMode = BREAKDANCE_MODE;
$window_dot_breakdance_object_data->bdoxTranslations = \Breakdance\BreakdanceOxygen\Strings\getBdoxTranslationsForBuilder();
$window_dot_breakdance_object_data->builderDistUrl = breakdanceBuilderDistUrl();

?>

<!DOCTYPE html>
<html style="overflow: hidden">

<head>
    <script>
        // This one does not implement BreakdanceWindowObject
        window.Breakdance = <?= json_encode($window_dot_breakdance_object_data); ?>;
    </script>
</head>

<body>
    <div id="regenerate-cache-wrapper"></div>
    <script type="text/javascript"
        src="<?php echo BREAKDANCE_PLUGIN_URL; ?>plugin/lib/iframe-resizer@4/iframeResizer.contentWindow.min.js"></script>

    <?php
    if ($useViteDevServer) {
        echo getDevelopmentFooterScripts('settings-tools-regenerate-cache');
    } else {
        echo getProductionFooterScripts($manifest, 'settings-tools-regenerate-cache');
    }
    ?>
</body>

</html>
