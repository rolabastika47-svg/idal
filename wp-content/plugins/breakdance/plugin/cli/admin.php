<?php
// @psalm-ignore-file

namespace Breakdance\Admin\CLI;

use function Breakdance\Filesystem\HelperFunctions\generate_error_msg_from_fs_wp_error;
use function Breakdance\Filesystem\try_to_create_all_required_directories;
use function Breakdance\Filesystem\check_all_required_directories;
use function Breakdance\Setup\reset;
use function Breakdance\Setup\refresh;
use function Breakdance\Setup\install;
use function Breakdance\Setup\replaceUrls;
use function Breakdance\Data\get_global_options_for_export;
use function Breakdance\Data\import_global_options;
use function Breakdance\Render\clearAllCssCachesAndDeleteCachedFiles;
use function Breakdance\Render\generateCacheForGlobalSettings;
use function Breakdance\Subscription\isFreeMode;
use Breakdance\Licensing\LicenseKeyManager;

if (!defined('ABSPATH')) {
  die();
}

if (!defined('WP_CLI') || !WP_CLI) {
  return;
}

if (!class_exists('WP_CLI')) {
  return;
}

/**
 * Breakdance CLI Commands
 *
 * @psalm-suppress UndefinedClass
 */
class BreakdanceCommands
{
  /**
   * Create required directories for Breakdance
   *
   * ## EXAMPLES
   *
   *     wp breakdance create-directories
   *
   * @param array<int, string> $args
   * @param array<string, mixed> $assoc_args
   */
  public function create_directories($args, $assoc_args)
  {
    \WP_CLI::log('Creating required directories...');

    $maybe_wp_error = try_to_create_all_required_directories();
    if (is_wp_error($maybe_wp_error)) {
      \WP_CLI::error('Failed to create directories. ' . generate_error_msg_from_fs_wp_error($maybe_wp_error));
    }

    \WP_CLI::success('All directories have been successfully created.');
  }

  /**
   * Check directory status
   *
   * ## EXAMPLES
   *
   *     wp breakdance check-directories
   *
   * @param array<int, string> $args
   * @param array<string, mixed> $assoc_args
   */
  public function check_directories($args, $assoc_args)
  {
    $erroneous_directory_paths = array_filter(check_all_required_directories(), function ($maybe_fs_error) {
      return $maybe_fs_error !== null;
    });

    if (empty($erroneous_directory_paths)) {
      \WP_CLI::success('All required directories are properly configured.');
    } else {
      \WP_CLI::warning('Found directory issues:');
      foreach ($erroneous_directory_paths as $directory_path => $fs_error) {
        \WP_CLI::log("  {$directory_path}: {$fs_error}");
      }
    }
  }

  /**
   * Replace URLs in Breakdance content
   *
   * ## OPTIONS
   *
   * <from>
   * : The old URL to replace
   *
   * <to>
   * : The new URL to replace with
   *
   * ## EXAMPLES
   *
   *     wp breakdance replace_url http://old-site.com http://new-site.com
   *
   * @param array<int, string> $args
   * @param array<string, mixed> $assoc_args
   * @when after_wp_load
   */
  public function replace_url($args, $assoc_args)
  {
    if (count($args) < 2) {
      \WP_CLI::error('Please provide both from and to URLs.');
    }

    $from = $args[0];
    $to = $args[1];

    \WP_CLI::log("Replacing URLs from '{$from}' to '{$to}'...");

    $affectedValues = replaceUrls($from, $to);

    if (is_wp_error($affectedValues)) {
      \WP_CLI::error($affectedValues->get_error_message());
    }

    /** @var array{postMeta: int, preferences: bool} $affectedValues */
    $affectedValues = $affectedValues;

    \WP_CLI::success("URL replacement completed. {$affectedValues['postMeta']} rows affected.");

    // Regenerate font files
    \WP_CLI::log('Regenerating font files...');
    $fontFilesRegenerated = \Breakdance\CustomFonts\regenerateFontFiles();

    if (isset($fontFilesRegenerated['error'])) {
      \WP_CLI::warning("Error regenerating font files: " . $fontFilesRegenerated['error']);
    } else {
      \WP_CLI::success('Font files regenerated successfully.');
    }
  }

  /**
   * Perform a soft reset (refresh icon sets and fallback templates)
   *
   * ## EXAMPLES
   *
   *     wp breakdance soft_reset
   *
   * @param array<int, string> $args
   * @param array<string, mixed> $assoc_args
   * @when after_wp_load
   */
  public function soft_reset($args, $assoc_args)
  {
    \WP_CLI::log('Performing soft reset...');

    refresh();

    \WP_CLI::success('Soft reset completed successfully.');
  }

  /**
   * Perform a total reset (reset all settings and reinstall)
   *
   * ## EXAMPLES
   *
   *     wp breakdance total_reset
   *
   * @param array<int, string> $args
   * @param array<string, mixed> $assoc_args
   * @when after_wp_load
   */
  public function total_reset($args, $assoc_args)
  {
    if (!\WP_CLI::confirm('This will reset your entire Breakdance installation to factory defaults. Continue?')) {
      \WP_CLI::log('Operation cancelled.');
      return;
    }

    \WP_CLI::log('Performing total reset...');

    reset();
    install();

    \WP_CLI::success('Total reset completed successfully.');
  }

  /**
   * Clear and regenerate CSS cache
   *
   * ## EXAMPLES
   *
   *     wp breakdance clear_cache
   *
   * @param array<int, string> $args
   * @param array<string, mixed> $assoc_args
   * @when after_wp_load
   */
  public function clear_cache($args, $assoc_args)
  {
    \WP_CLI::log('Clearing CSS cache...');

    clearAllCssCachesAndDeleteCachedFiles();
    generateCacheForGlobalSettings();

    \WP_CLI::success('CSS cache cleared and regenerated successfully.');
  }

  /**
   * Export Breakdance settings to JSON file
   *
   * ## OPTIONS
   *
   * [--file=<file>]
   * : Output file path (default: breakdance_settings_YYYY-MM-DD.json)
   *
   * ## EXAMPLES
   *
   *     wp breakdance export_settings
   *     wp breakdance export_settings --file=/path/to/settings.json
   *
   * @param array<int, string> $args
   * @param array<string, mixed> $assoc_args
   * @when after_wp_load
   */
  public function export_settings($args, $assoc_args)
  {
    if (isFreeMode()) {
      \WP_CLI::error('Settings export is not available in free mode.');
    }

    $filename = $assoc_args['file'] ?? ((string) BREAKDANCE_MODE) . '_settings_' . date('Y-m-d') . '.json';

    \WP_CLI::log("Exporting settings to {$filename}...");

    $data_for_export = get_global_options_for_export();
    $json_data = json_encode($data_for_export, JSON_PRETTY_PRINT);

    if (file_put_contents($filename, $json_data) === false) {
      \WP_CLI::error("Failed to write to file: {$filename}");
    }

    \WP_CLI::success("Settings exported successfully to {$filename}");
  }

  /**
   * Import Breakdance settings from JSON file
   *
   * ## OPTIONS
   *
   * <file>
   * : Path to the JSON settings file
   *
   * ## EXAMPLES
   *
   *     wp breakdance import_settings /path/to/settings.json
   *
   * @param array<int, string> $args
   * @param array<string, mixed> $assoc_args
   * @when after_wp_load
   */
  public function import_settings($args, $assoc_args)
  {
    if (isFreeMode()) {
      \WP_CLI::error('Settings import is not available in free mode.');
    }

    if (empty($args[0])) {
      \WP_CLI::error('Please provide the path to the settings file.');
    }

    $file_path = $args[0];

    if (!file_exists($file_path)) {
      \WP_CLI::error("File not found: {$file_path}");
    }

    \WP_CLI::log("Importing settings from {$file_path}...");

    $import_data = file_get_contents($file_path);
    if ($import_data === false) {
      \WP_CLI::error("Failed to read file: {$file_path}");
    }

    $config = json_decode($import_data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
      \WP_CLI::error('Invalid JSON data in settings file.');
    }

    $import_was_successful = import_global_options($config);
    if (!$import_was_successful) {
      \WP_CLI::error('Failed to import settings.');
    }

    \WP_CLI::success('Settings imported successfully.');
  }

  /**
   * Set Breakdance license key
   *
   * ## OPTIONS
   *
   * <license_key>
   * : The license key to set
   *
   * ## EXAMPLES
   *
   *     wp breakdance license abc123def456ghi789
   *     wp breakdance license clear
   *
   * @param array<int, string> $args
   * @param array<string, mixed> $assoc_args
   */
  public function license($args, $assoc_args)
  {
    if (empty($args[0])) {
      \WP_CLI::error('Please provide a license key or use "clear" to clear the license key.');
    }

    $license_key = $args[0];

    // Handle empty string to clear license
    if ($license_key === 'clear') {
      $license_key = null;
    }

    \WP_CLI::log('Setting license key...');

    $license_key_manager = LicenseKeyManager::getInstance();
    $license_key_manager->changeLicenseKey($license_key);

    if ($license_key === null) {
      \WP_CLI::success('License key cleared successfully.');
    } else {
      // Show license information
      $license_info = $license_key_manager->getHumanReadableLicenseKeyInformation();
      \WP_CLI::log('License Information:');
      \WP_CLI::log('  Product: ' . $license_info['product']);
      \WP_CLI::log('  Status: ' . $license_info['is_valid']);
      \WP_CLI::log('  Activation: ' . $license_info['activation_status']);
      \WP_CLI::log('  Expires: ' . $license_info['expires'] . $license_info['expires_in_human_readable']);

      // Only show success if license is valid
      if ($license_info['is_valid'] === 'Valid') {
        \WP_CLI::success('License key set successfully.');
      } else {
        \WP_CLI::warning('License key was set but status is ' . $license_info['is_valid'] . '. Please verify the license key is correct.');
      }
    }
  }

  /**
   * Get Breakdance system status
   *
   * ## EXAMPLES
   *
   *     wp breakdance status
   *
   * @param array<int, string> $args
   * @param array<string, mixed> $assoc_args
   * @when after_wp_load
   */
  public function status($args, $assoc_args)
  {
    \WP_CLI::log('Breakdance System Status:');
    \WP_CLI::log('========================');

    // Check directories
    $erroneous_directory_paths = array_filter(check_all_required_directories(), function ($maybe_fs_error) {
      return $maybe_fs_error !== null;
    });

    if (empty($erroneous_directory_paths)) {
      \WP_CLI::log('✓ All required directories are properly configured');
    } else {
      \WP_CLI::log('✗ Directory issues found:');
      foreach ($erroneous_directory_paths as $directory_path => $fs_error) {
        \WP_CLI::log("  - {$fs_error}");
      }
    }

    // Check mode
    \WP_CLI::log('Mode: ' . BREAKDANCE_MODE);

    // Check free mode
    if (isFreeMode()) {
      \WP_CLI::log('License: Free Mode');
    } else {
      \WP_CLI::log('License: Pro Mode');
    }

    // Check version
    if (defined('__BREAKDANCE_VERSION__')) {
      \WP_CLI::log('Version: ' . constant('__BREAKDANCE_VERSION__'));
    }
  }

  /**
   * Enable or disable the WordPress theme system
   *
   * ## OPTIONS
   *
   * <status>
   * : The status to set the theme system to.
   * ---
   * options:
   *   - on
   *   - off
   * ---
   *
   * ## EXAMPLES
   *
   *     wp breakdance theme on
   *     wp breakdance theme off
   *
   * @param array<int, string> $args
   * @param array<string, mixed> $assoc_args
   */
  public function theme($args, $assoc_args)
  {
    $theme_status = $args[0] ?? null;

    if ($theme_status !== 'on' && $theme_status !== 'off') {
      \WP_CLI::error('Please provide a valid theme status: "on" or "off".');
    }

    if ($theme_status === 'on') {
      \Breakdance\Data\set_global_option('is_theme_disabled', 'no');
      \WP_CLI::success('Theme enabled successfully.');
    } else {
      \Breakdance\Data\set_global_option('is_theme_disabled', 'yes');
      \WP_CLI::success('Theme disabled successfully.');
    }
  }
}

// Register the CLI commands when WP_CLI is ready
add_action('cli_init', function () {
  if (!class_exists('WP_CLI')) return;
  \WP_CLI::add_command(BREAKDANCE_MODE, 'Breakdance\Admin\CLI\BreakdanceCommands');
});
