<?php

/**
 * @psalm-ignore-file
 */


use function Breakdance\I18n\getLanguageAttribute;

require_once __DIR__ . "/../../loader/loader-utils.php";

$ajaxurl = admin_url('admin-ajax.php');
$useViteDevServer = shouldUseViteDevServer();

if (!$useViteDevServer) {
    $manifest = getProductionManifest();
}

$window_dot_breakdance_object_data = new stdClass();
$window_dot_breakdance_object_data->ajaxurl = $ajaxurl;
$window_dot_breakdance_object_data->ajaxnonce = \Breakdance\AJAX\get_nonce_for_ajax_requests();
$window_dot_breakdance_object_data->subscriptionMode = \Breakdance\Subscription\getSubscriptionMode();
$window_dot_breakdance_object_data->builderMode = BREAKDANCE_MODE;
$window_dot_breakdance_object_data->bdoxTranslations = \Breakdance\BreakdanceOxygen\Strings\getBdoxTranslationsForBuilder();
$window_dot_breakdance_object_data->builderDistUrl = breakdanceBuilderDistUrl();

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
        echo getDevelopmentHeadLinks('manage-templates');
    } else {
        echo getProductionHeadLinks($manifest, 'manage-templates');
    }
    ?>
</head>

<body>
    <div id="manage-templates-wrapper"></div>

    <?php
    if ($useViteDevServer) {
        echo getDevelopmentFooterScripts('manage-templates');
    } else {
        echo getProductionFooterScripts($manifest, 'manage-templates');
    }
    ?>
</body>

</html>
