<?php
/**
 * @var string $licenseKeyAdminScreenUrl
 * @var string $upgradeToProWarnings
 */

use function Breakdance\BreakdanceOxygen\Strings\__bdox;

// Bail if accessed directly
if (!defined('ABSPATH')) exit;
?>
<style>
  .breakdance-upgrade-to-pro-frontend-notice {
    background-color: #374151;
    color: #e5e7eb;
    font-family: "system-ui";
    font-size: 15px;
    font-weight: 700;
    line-height: 1.2;
  }

  .breakdance-upgrade-to-pro-frontend-notice-container {
    padding: 20px;
    max-width: 1120px;
    margin-left: auto;
    margin-right: auto;
  }

  .breakdance-upgrade-to-pro-frontend-notice a {
    color: rgb(255, 197, 20) !important;
  }

  .breakdance-upgrade-to-pro-frontend-notice__details.hidden {
    display: none;
  }

  .breakdance-upgrade-to-pro-frontend-notice__details-overview {
    margin-top: 30px;
    font-weight: 400;
    line-height: 1.6;
  }

  .breakdance-upgrade-to-pro-frontend-notice__details-details {
    display: flex;
    padding: 40px;
    flex-direction: column;
    gap: 15px;
    margin-top: 20px;
    background-color: #eee;
    color: #222;
    font-size: 13px;
    font-weight: 400;
  }

  .breakdance-upgrade-to-pro-frontend-notice-top {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    gap: 40px;
  }
</style>

<section class="breakdance-upgrade-to-pro-frontend-notice">
  <div class="breakdance-upgrade-to-pro-frontend-notice-container">
    <div class="breakdance-upgrade-to-pro-frontend-notice-top">
      <div>
        <?php
        echo wp_kses(
            sprintf(
                __('This page uses pro-only features. Please upgrade to <a href="%1$s" target="_blank">%2$s Pro</a>, or enter your <a href="%3$s">license key</a>.', 'breakdance'),
                BREAKDANCE_MODE === 'breakdance' ? 'https://breakdance.com/' : 'https://oxygenbuilder.com/',
                __bdox('plugin_name'),
                esc_url($licenseKeyAdminScreenUrl)
            ),
            [
                'a' => [
                    'href' => [],
                    'target' => [],
                ],
            ]
        );
        ?>
      </div>
      <a
        href="#"
        class="breakdance-upgrade-to-pro-frontend-notice__expand-details"
        ><?php echo esc_html(__('See details', 'breakdance')); ?></a
      >
    </div>

    <div class="breakdance-upgrade-to-pro-frontend-notice__details hidden">
      <div class="breakdance-upgrade-to-pro-frontend-notice__details-overview">
        <?php echo esc_html(__('This notice only appears for site administrators. For normal visitors, this notice won\'t appear, and pro-only features won\'t work.', 'breakdance')); ?>
      </div>
      <div class="breakdance-upgrade-to-pro-frontend-notice__details-details">
        <?php echo $upgradeToProWarnings; ?>
      </div>
    </div>
  </div>
</section>

<script>
  document.addEventListener(
    "DOMContentLoaded",
    function () {
      const expandDetailsButton = document.querySelector(
        ".breakdance-upgrade-to-pro-frontend-notice__expand-details"
      );
      const detailsContainer = document.querySelector(
        ".breakdance-upgrade-to-pro-frontend-notice__details"
      );

      expandDetailsButton.addEventListener("click", function () {
        detailsContainer.classList.toggle("hidden");
      });
    },
    false
  );
</script>

