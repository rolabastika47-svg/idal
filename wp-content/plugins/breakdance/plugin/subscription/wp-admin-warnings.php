<?php

namespace Breakdance\Subscription;

use function Breakdance\BreakdanceOxygen\Strings\__bdox;

add_action('breakdance_loaded', function() {

  if (isFreeMode()) {
    add_action('admin_notices', function () {
      if (filter_input(INPUT_GET, 'breakdance_form_submissions_upgrade_pro', FILTER_UNSAFE_RAW)) {
        /* translators: %s: plugin name */
        $message = sprintf(__('Please upgrade to %s Pro to export form submissions.', 'breakdance'), __bdox('plugin_name'));
        showWpAdminUpgradeToProNotice($message, 'forms');
      }
    });

    add_action('admin_notices', function () {
      $isBreakdanceSettingsPage = filter_input(INPUT_GET, 'page', FILTER_UNSAFE_RAW) === 'breakdance_settings';

      if ($isBreakdanceSettingsPage){
        $currentTab = (string)filter_input(INPUT_GET, 'tab', FILTER_UNSAFE_RAW);

        if ($currentTab === 'design_library') {
          /* translators: %s is the plugin name */
          $warningText = sprintf(__('When the design library comes out of beta, it will only be available in %s Pro.', 'breakdance'), __bdox('plugin_name'));
          showWpAdminUpgradeToProNotice($warningText, 'design-library');
        }

        else if ($currentTab === 'permissions') {
          /* translators: %s is the plugin name */
          $warningText = sprintf(__('Please upgrade to %s Pro to configure permissions.', 'breakdance'), __bdox('plugin_name'));
          showWpAdminUpgradeToProNotice($warningText, 'user-access');
        }

        else if ($currentTab === 'tools') {
          /* translators: %s is the plugin name */
          $warningText = sprintf(__('Please upgrade to %s Pro to import and export settings.', 'breakdance'), __bdox('plugin_name'));
          showWpAdminUpgradeToProNotice($warningText, 'import-export');
        }
      }
    });
  }
});

/**
 * @param string $text
 * @param string $feature
 * @return void
 */
function showWpAdminUpgradeToProNotice($text, $feature = 'unknown') {
  ?>
  <style>

.breakdance-wp-admin-upgrade-to-pro-notice-button {
  background-color: rgb(255, 197, 20);
  color: black !important;
  border-radius: 4px;
  font-size: var(--text-sm);
  font-weight: 600;
  line-height: 1.2;
  display: flex;
  align-items: center;
  flex-direction: column;
  padding: 12px;
  margin-top: 16px;
  text-decoration: none;
}

.breakdance-wp-admin-upgrade-to-pro-notice {
  background-color: #374151;
  color: #e5e7eb;
  border-radius: 4px;
  font-size: 15px;
  font-weight: 700;
  line-height: 1.2;
  padding: 80px;
  text-align: center;
  margin-left: auto;
  margin-right: auto;
  max-width: 450px;
  margin-top: 40px;
  margin-bottom: 40px;
}


  </style>
  <div class='breakdance-wp-admin-upgrade-to-pro-notice'>
    <?php
    $domain = BREAKDANCE_MODE === 'oxygen' ? 'oxygenbuilder.com' : 'breakdance.com';
    ?>
    <?php echo $text; ?>
    <a href='https://<?php echo $domain; ?>/?utm_source=free-version&utm_medium=free-version&utm_campaign=<?php echo $feature; ?>' target='_blank' class='breakdance-wp-admin-upgrade-to-pro-notice-button'><?php echo esc_html__('Get Pro', 'breakdance'); ?></a>
  </div>
  <?php
}
