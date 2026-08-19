<?php

/**
 * @psalm-ignore-file
 */


use function Breakdance\I18n\getLanguageAttribute;

require_once __DIR__ . "/../loader/loader-utils.php";

$ajaxurl = admin_url('admin-ajax.php');
$useViteDevServer = shouldUseViteDevServer();

if (!$useViteDevServer) {
    $manifest = getProductionManifest();
}

$window_dot_breakdance_object_data = new stdClass();
$window_dot_breakdance_object_data->ajaxurl = $ajaxurl;
$window_dot_breakdance_object_data->ajaxnonce = \Breakdance\AJAX\get_nonce_for_ajax_requests();
$window_dot_breakdance_object_data->subscriptionMode = \Breakdance\Subscription\getSubscriptionMode();

?>
<!DOCTYPE html>
<html <?php echo getLanguageAttribute(); ?>>

<head>
    <title>Breakdance Onboarding</title>

    <script>
        // This one does not implement BreakdanceWindowObject
        window.Breakdance = <?= json_encode($window_dot_breakdance_object_data); ?>;
    </script>

    <?php

    if ($useViteDevServer) {
        echo getDevelopmentHeadLinks('onboarding-app');
    } else {
        echo getProductionHeadLinks($manifest, 'onboarding-app');
    }


    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
</head>

<body>
    <div id="onboarding-app"></div>

    <?php
    if ($useViteDevServer) {
        echo getDevelopmentFooterScripts('onboarding-app');
    } else {
        echo getProductionFooterScripts($manifest, 'onboarding-app');
    }
    ?>
</body>

</html>
