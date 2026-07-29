<?php

namespace Breakdance\Subscription;

use function Breakdance\BreakdanceOxygen\Strings\__bdox;

/**
 * @param string $warning
 * @return void
 */
function logNoticeBecauseProOnlyFeatureWasUsed($warning) {
    if(!$warning) return;

    ProOnlyFeatureNoticesHolder::getInstance()->addWarning($warning);
}

class ProOnlyFeatureNoticesHolder
{
    use \Breakdance\Singleton;

    /**
     * @var string[]
     */
    public $warnings = [];

    /**
     * @param string $warning
     * @return void
     */
    public function addWarning($warning) {
        $this->warnings[] = $warning;
    }

}

add_action('init', function () {
    if (freeModeOnFrontend() && \Breakdance\Permissions\hasMinimumPermission('edit')) {
        add_action('wp_body_open', function() {
            $warnings = ProOnlyFeatureNoticesHolder::getInstance()->warnings;

            if(!count($warnings)) return '';

            $upgradeToProWarnings = join("\n", ProOnlyFeatureNoticesHolder::getInstance()->warnings);

            $licenseKeyAdminScreenUrl = admin_url("admin.php?page=" . __bdox('admin_page_settings_slug') . "&tab=license");

            $noticeTemplatePath = __DIR__ . "/notice.php";

            ob_start();
            /**
             * @psalm-suppress UnresolvableInclude
             */
            include $noticeTemplatePath;
            $renderedNoticeTemplate = ob_get_clean();

            echo $renderedNoticeTemplate;
        });
    }
});
