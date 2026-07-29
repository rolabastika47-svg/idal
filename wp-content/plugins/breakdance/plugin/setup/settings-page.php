<?php

namespace Breakdance\Setup;

use function Breakdance\BreakdanceOxygen\Strings\__bdox;
use function Breakdance\Filesystem\check_all_required_directories;
use function Breakdance\Filesystem\HelperFunctions\generate_error_msg_from_fs_wp_error;
use function Breakdance\Filesystem\try_to_create_all_required_directories;
use function Breakdance\Subscription\isFreeMode;
use function Breakdance\Util\is_post_request;

add_action('breakdance_register_admin_settings_page_register_tabs', function () {
    \Breakdance\Admin\SettingsPage\addTab(__('Tools', 'breakdance'), "tools", 'Breakdance\Setup\settings_page', 2000);
});

const BREAKDANCE_SETUP_NONCE_ACTION_MIGRATE_META = 'BREAKDANCE_SETUP_NONCE_ACTION_MIGRATE_META';
const BREAKDANCE_SETUP_NONCE_ACTION_UNDO_MIGRATE_META = 'BREAKDANCE_SETUP_NONCE_ACTION_UNDO_MIGRATE_META';
const BREAKDANCE_SETUP_NONCE_ACTION_TOTAL_RESET = 'BREAKDANCE_SETUP_NONCE_ACTION_TOTAL_RESET';
const BREAKDANCE_SETUP_NONCE_ACTION_SOFT_RESET = 'BREAKDANCE_SETUP_NONCE_ACTION_SOFT_RESET';
const BREAKDANCE_SETUP_NONCE_ACTION_CREATE_DIRECTORIES = 'BREAKDANCE_SETUP_NONCE_ACTION_CREATE_DIRECTORIES';
const BREAKDANCE_SETUP_NONCE_ACTION_DOWNLOAD_EXPORT_FILE = 'BREAKDANCE_SETUP_NONCE_ACTION_UPLOAD_EXPORT_FILE';
const BREAKDANCE_SETUP_NONCE_ACTION_UPLOAD_EXPORT_FILE = 'BREAKDANCE_SETUP_NONCE_ACTION_UPLOAD_EXPORT_FILE';
const BREAKDANCE_SETUP_NONCE_ACTION_REPLACE_URL = 'BREAKDANCE_SETUP_NONCE_ACTION_REPLACE_URL';

function admin_notice(string $message, string $type = 'success'): void
{
?>
    <div class="notice notice-<?php echo esc_attr($type); ?> is-dismissible">
        <p><?php echo esc_html($message); ?></p>
    </div>
<?php
}

function upload_export_file()
{
    if (isFreeMode()) return;

    if (empty($_FILES['breakdanceImportData'])) {
        admin_notice(__('Failed to upload a file', 'breakdance'), 'error');
        return;
    }

    /** @var array{ name:string, type:string, tmp_name:string, error:int, size: int } $import_file */
    $import_file = $_FILES['breakdanceImportData'];
    if ($import_file['error'] !== UPLOAD_ERR_OK) {
        admin_notice(__('Failed to upload a file', 'breakdance'), 'error');
        return;
    }
    $import_data = (string) file_get_contents($import_file['tmp_name']);

    /** @var list<array{option_name:string, option_value:string}> $config */
    $config = (array) json_decode($import_data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        admin_notice(__('Invalid export data', 'breakdance'), 'error');
        return;
    }

    $import_was_successful = \Breakdance\Data\import_global_options($config);
    if (!$import_was_successful) {
        admin_notice(__('Error importing settings', 'breakdance'), 'error');
        return;
    }

    admin_notice(__('Settings imported', 'breakdance'));
}

function replace_urls()
{
    $from = (string) filter_input(INPUT_POST, 'from');
    $to   = (string) filter_input(INPUT_POST, 'to');
    $affectedValues = \Breakdance\Setup\replaceUrls($from, $to);

    if (is_wp_error($affectedValues)) {
        /** @psalm-suppress PossiblyInvalidMethodCall */
        admin_notice($affectedValues->get_error_message(), 'error');
        return;
    }

    /** @var array{postMeta: string, preferences: boolean} $affectedValues */
    $affectedValues = $affectedValues;

    /** @psalm-suppress PossiblyInvalidArgument */
    /* translators: %s is the number of database rows affected by the URL replacement */
    admin_notice(sprintf(__('%s rows affected.', 'breakdance'), $affectedValues['postMeta']));

    //  always regenerate fonts, even if no replace was done
    // a user may have used a tool like "Search And Replace" to update all their URLs
    // and then run this tool for fonts, or just to verify everything was replaced
    /** @psalm-suppress UndefinedFunction
     * @var array{error?: string} $fontFilesRegenerated
     */
    $fontFilesRegenerated = \Breakdance\CustomFonts\regenerateFontFiles();

    if (isset($fontFilesRegenerated['error'])) {
        /* translators: %s is the error message */
        admin_notice(sprintf(__('Error regenerating font files: %s', 'breakdance'), $fontFilesRegenerated['error']));
    }
}

function settings_page()
{
?>
    <h2><?php esc_html_e('Tools', 'breakdance'); ?></h2>
    <?php
    if (is_post_request()) {
        $mode = (string) filter_input(INPUT_POST, 'mode');

        switch ($mode) {
            case 'soft_reset':
                check_admin_referer(BREAKDANCE_SETUP_NONCE_ACTION_SOFT_RESET);
                refresh();
                admin_notice(__('Soft Reset Succeeded', 'breakdance'));
                break;
            case 'migrate_meta':
                check_admin_referer(BREAKDANCE_SETUP_NONCE_ACTION_MIGRATE_META);
                /**
                 * @psalm-suppress UndefinedFunction
                 */
                \Breakdance\Setup\prefixBreakdanceMetaKeysWithUnderscore();
                admin_notice(__('Meta Migration Successful', 'breakdance'));
                break;
            case 'undo_migrate_meta':
                check_admin_referer(BREAKDANCE_SETUP_NONCE_ACTION_UNDO_MIGRATE_META);
                /**
                 * @psalm-suppress UndefinedFunction
                 */
                \Breakdance\Setup\undoPrefixBreakdanceMetaKeysWithUnderscore();
                admin_notice(__('Undo Meta Migration Successful', 'breakdance'));
                break;
            case 'total_reset':
                check_admin_referer(BREAKDANCE_SETUP_NONCE_ACTION_TOTAL_RESET);
                reset();
                install();
                admin_notice(__('Total Reset Succeeded', 'breakdance'));
                break;
            case 'create_dirs':
                check_admin_referer(BREAKDANCE_SETUP_NONCE_ACTION_CREATE_DIRECTORIES);

                $maybe_wp_error = try_to_create_all_required_directories();
                if (is_wp_error($maybe_wp_error)) {
                    /**
                     * @var \WP_Error $maybe_wp_error
                     */
                    $maybe_wp_error = $maybe_wp_error;
                    /* translators: %s is the filesystem error message */
                    admin_notice(sprintf(__('Failed to create directories. %s', 'breakdance'), generate_error_msg_from_fs_wp_error($maybe_wp_error)), 'error');
                } else {
                    admin_notice(__('All directories have been successfully created.', 'breakdance'));
                }
                break;
            case 'upload_export_file':
                check_admin_referer(BREAKDANCE_SETUP_NONCE_ACTION_UPLOAD_EXPORT_FILE);
                upload_export_file();
                break;
            case 'replace_url':
                check_admin_referer(BREAKDANCE_SETUP_NONCE_ACTION_REPLACE_URL);
                replace_urls();
                break;
        }
    }

    /** @var array<string, string> $erroneous_directory_paths */
    $erroneous_directory_paths = array_filter(check_all_required_directories(), function ($maybe_fs_error) {
        return $maybe_fs_error !== null;
    });

    $disabledBecauseOfFreeMode = isFreeMode() ? "disabled" : '';

    $bdoroxy = __bdox('plugin_name');

    ?>

    <table class="form-table" role="presentation">
        <tbody>
            <?php
            $regencache = (string) ($_GET['regencache'] ?? '');
            $highlightRegencache = ($regencache) && !is_post_request();
            ?>

            <?php
            if (BREAKDANCE_MODE === 'breakdance') {
            ?>
                <tr>
                    <th scope="row">
                        <?php esc_html_e('Migrate Meta', 'breakdance'); ?>
                    </th>
                    <td>

                        <form class='breakdance-migrate-meta' action="" method="post">
                            <?php wp_nonce_field(BREAKDANCE_SETUP_NONCE_ACTION_MIGRATE_META); ?>
                            <button type="submit" class="button" name="mode" value="migrate_meta"><?php esc_html_e('Migrate Meta', 'breakdance'); ?></button>
                        </form>

                        <p class='description'>
                            <?php esc_html_e('Migrate your post meta key names to the new format.', 'breakdance'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php esc_html_e('Undo Migrate Meta & Downgrade to 1.7.2', 'breakdance'); ?>
                    </th>
                    <td>

                        <form class='breakdance-migrate-meta' action="" method="post">
                            <?php wp_nonce_field(BREAKDANCE_SETUP_NONCE_ACTION_UNDO_MIGRATE_META); ?>
                            <button type="submit" class="button" name="mode" value="undo_migrate_meta"><?php esc_html_e('Undo Migrate Meta', 'breakdance'); ?></button>
                        </form>

                        <p class='description'>
                            <?php esc_html_e('Restore your post meta key names the way they were before the migration.', 'breakdance'); ?> <br>
                            <?php echo wp_kses_post(__('You <b>must</b> downgrade to Breakdance <b>1.7.2</b> afterwards.', 'breakdance')); ?>
                        </p>
                    </td>
                </tr>
            <?php
            }
            ?>
            <tr <?php echo $highlightRegencache ? 'class="breakdance-admin-highlight-row"' : ''; ?>>
                <th scope="row">
                    <?php esc_html_e('Regenerate CSS Cache', 'breakdance'); ?>
                </th>
                <td>
                    <?php $breakdance_or_oxygen = BREAKDANCE_MODE === 'oxygen' ? 'oxygen' : 'breakdance'; ?>
                    <iframe id='settings-tools-regenerate-cache-iframe' width="100%" frameborder="0" src='<?php echo site_url("?" . $breakdance_or_oxygen . "=regenerate-cache") ?>'>
                    </iframe>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e('Soft Reset', 'breakdance'); ?>
                </th>
                <td>

                    <form class='breakdance-are-you-sure-form' action="" method="post">
                        <?php wp_nonce_field(BREAKDANCE_SETUP_NONCE_ACTION_SOFT_RESET); ?>
                        <button type="submit" class="button" name="mode" value="soft_reset"><?php esc_html_e('Soft Reset', 'breakdance'); ?></button>
                    </form>

                    <p class="description">
                        <?php
                        if (BREAKDANCE_MODE === 'oxygen') {
                            esc_html_e('Reset Icon Sets to factory defaults.', 'breakdance');
                        } else {
                            esc_html_e('Reset Icon Sets and fallback default templates to factory defaults.', 'breakdance');
                        }
                        ?>
                    </p>

                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e('Total Reset', 'breakdance'); ?>
                </th>
                <td>

                    <form class='breakdance-are-you-sure-form' action="" method="post">
                        <?php wp_nonce_field(BREAKDANCE_SETUP_NONCE_ACTION_TOTAL_RESET); ?>
                        <button type="submit" class="button" name="mode" value="total_reset"><?php esc_html_e('Total Reset', 'breakdance'); ?></button>
                    </form>

                    <p class='description'>
                        <?php
                        /* translators: %1$s and %2$s are the plugin name (Breakdance or Oxygen) */
                        printf(esc_html__('Reset your entire %1$s installation to factory defaults. Content you created in %2$s must still be deleted manually.', 'breakdance'), esc_html($bdoroxy), esc_html($bdoroxy)); ?>
                    </p>
                </td>
            </tr>
            <tr id="create_directories_row">
                <th scope="row">
                    <?php esc_html_e('Create Directories', 'breakdance'); ?>
                </th>
                <td>
                    <form action="#" method="post">
                        <?php wp_nonce_field(BREAKDANCE_SETUP_NONCE_ACTION_CREATE_DIRECTORIES); ?>
                        <button type="submit" class="button" name="mode" value="create_dirs"><?php esc_html_e('Create Directories', 'breakdance'); ?></button>
                    </form>
                    <p class='description'>
                        <?php
                        /* translators: %s is the plugin name (Breakdance or Oxygen) */
                        printf(esc_html__('Create the directories on your server that are required for %s to function properly.', 'breakdance'), esc_html($bdoroxy)); ?></p>
                    <?php
                    if (sizeof($erroneous_directory_paths)) : ?>
                        <p class='description'>
                            <?php esc_html_e('Existing problems:', 'breakdance'); ?>
                        <dl>
                            <?php
                            foreach ($erroneous_directory_paths as $directory_path => $fs_error) : ?>
                                <dt><code><?= $directory_path ?></code>:</dt>
                                <dd style="color: red;">
                                    <strong><?= $fs_error; ?></strong>
                                </dd>
                            <?php
                            endforeach; ?>
                        </dl>
                        </p>
                    <?php
                    endif; ?>
                </td>
            </tr>
            <?php
            $fromUrl = (string) filter_input(INPUT_GET, 'from', FILTER_VALIDATE_URL);
            $toUrl = (string) filter_input(INPUT_GET, 'to', FILTER_VALIDATE_URL);
            $highlightReplaceTool = ($fromUrl || $toUrl) && !is_post_request();
            ?>
            <tr <?php echo $highlightReplaceTool ? 'class="breakdance-admin-highlight-row"' : ''; ?>>
                <th scope="row">
                    <?php esc_html_e('Replace URL', 'breakdance'); ?>
                </th>
                <td>
                    <form action="" method="post">
                        <?php wp_nonce_field(BREAKDANCE_SETUP_NONCE_ACTION_REPLACE_URL); ?>
                        <input type="url" name="from" placeholder="http://old-url.com" required value="<?php echo esc_attr($fromUrl); ?>">
                        <input type="url" name="to" placeholder="http://new-url.com" required value="<?php echo esc_attr($toUrl); ?>">
                        <button class="button" name="mode" value="replace_url"><?php esc_html_e('Replace URL', 'breakdance'); ?></button>
                    </form>
                    <p class="description"><?php esc_html_e('It is strongly recommended that you backup your database before running this tool.', 'breakdance'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e('Export Settings', 'breakdance'); ?>
                </th>
                <td>
                    <form action="" method="post">
                        <?php wp_nonce_field(BREAKDANCE_SETUP_NONCE_ACTION_DOWNLOAD_EXPORT_FILE); ?>
                        <button type="submit" class="button" name="mode" value="download_export_file" <?= $disabledBecauseOfFreeMode ?>><?php esc_html_e('Download Export File', 'breakdance'); ?></button>
                    </form>
                    <p class='description'>
                        <?php esc_html_e('Export your Settings, Preferences, and Icon Sets to a JSON file.', 'breakdance'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e('Import Settings', 'breakdance'); ?>
                </th>
                <td>
                    <form class='breakdance-are-you-sure-form wp-upload-form' enctype="multipart/form-data" action="" method="post">
                        <?php wp_nonce_field(BREAKDANCE_SETUP_NONCE_ACTION_UPLOAD_EXPORT_FILE); ?>
                        <input type="file" accept="application/json" name="breakdanceImportData" <?= $disabledBecauseOfFreeMode ?> />
                        <button type="submit" class="button" name="mode" value="upload_export_file" <?= $disabledBecauseOfFreeMode ?>><?php esc_html_e('Upload Export JSON File', 'breakdance'); ?></button>
                    </form>
                </td>
            </tr>
        </tbody>
    </table>

    <script type="text/javascript" src="<?php
                                        echo BREAKDANCE_PLUGIN_URL; ?>plugin/lib/iframe-resizer@4/iframeResizer.min.js"></script>
    <script>
        iFrameResize({}, "#settings-tools-regenerate-cache-iframe");
    </script>


    <script>
        jQuery('.breakdance-are-you-sure-form').submit(function() {
            return confirm("<?php echo esc_js(__("Overwrite all your settings? This can't be undone without a backup.", 'breakdance')); ?>");
        });
    </script>

    </form>


<?php
}


// handle the file download in the init hook so we can send headers
add_action('init', function () {
    if (!is_post_request() || isFreeMode()) {
        return;
    }

    $mode = (string) filter_input(INPUT_POST, 'mode');
    if ($mode === 'download_export_file') {
        check_admin_referer(BREAKDANCE_SETUP_NONCE_ACTION_DOWNLOAD_EXPORT_FILE);

        $data_for_export = \Breakdance\Data\get_global_options_for_export();
        $filename = ((string) BREAKDANCE_MODE) . '_settings_' . date('Y-m-d');
        // Force download .json file with exportData in it
        header("Content-type: application/vnd.ms-excel");
        header("Content-Type: application/force-download");
        header("Content-Type: application/download");
        header("Content-disposition: " . $filename . ".json");
        header("Content-disposition: filename=" . $filename . ".json");

        print json_encode($data_for_export);
        exit;
    }
});
