<?php

namespace Breakdance\Admin\SettingsPage\AdvancedTab;

use function Breakdance\Util\is_post_request;
use const Breakdance\Data\GlobalRevisions\BREAKDANCE_N_OF_LAST_REVISIONS_TO_KEEP;
use function Breakdance\BreakdanceOxygen\Strings\__bdox;

add_action('breakdance_register_admin_settings_page_register_tabs', '\Breakdance\Admin\SettingsPage\AdvancedTab\register');

function register()
{
    \Breakdance\Admin\SettingsPage\addTab(esc_html__('Advanced', 'breakdance'), 'advanced', '\Breakdance\Admin\SettingsPage\AdvancedTab\tab', 1200);
}

function tab()
{
    $nonce_action = 'breakdance_admin_advanced_tab';
    if (is_post_request() && check_admin_referer($nonce_action)) {
        if (array_key_exists('enable_simulate_the_content', $_POST)) {
            \Breakdance\Data\set_global_option('breakdance_settings_enable_simulate_the_content', 'yes');
        } else {
            \Breakdance\Data\set_global_option('breakdance_settings_enable_simulate_the_content', false);
        }

        if (array_key_exists('allow_unfiltered_html', $_POST)) {
            \Breakdance\Data\set_global_option('breakdance_settings_allow_unfiltered_html', 'yes');
        } else {
            \Breakdance\Data\set_global_option('breakdance_settings_allow_unfiltered_html', false);
        }

        if (array_key_exists('enable_render_performance_debug', $_POST)) {
            \Breakdance\Data\set_global_option('enable_render_performance_debug', 'yes');
        } else {
            \Breakdance\Data\set_global_option('enable_render_performance_debug', false);
        }

        if (array_key_exists('enable_svg_uploads', $_POST)) {
            \Breakdance\Data\set_global_option('breakdance_settings_enable_svg_uploads', 'yes');
        } else {
            \Breakdance\Data\set_global_option('breakdance_settings_enable_svg_uploads', false);
        }

        if (array_key_exists('enable_skip_link', $_POST)) {
            \Breakdance\Data\set_global_option('enable_skip_link', 'yes');
        } else {
            \Breakdance\Data\set_global_option('enable_skip_link', false);
        }

        if (array_key_exists('skip_link_text', $_POST)) {
            $skip_link_text = sanitize_text_field((string) filter_input(INPUT_POST, 'skip_link_text'));
            \Breakdance\Data\set_global_option('skip_link_text', $skip_link_text);
        } else {
            \Breakdance\Data\set_global_option('skip_link_text', false);
        }

        if (filter_input(INPUT_POST, 'enable_revision_limit')) {
            \Breakdance\Data\set_global_option('breakdance_settings_enable_revision_limit', 'yes');
        } else {
            \Breakdance\Data\set_global_option('breakdance_settings_enable_revision_limit', false);
        }

        $revisionLimit = (int) filter_input(INPUT_POST, 'revision_limit', FILTER_VALIDATE_INT);
        if ($revisionLimit) {
            \Breakdance\Data\set_global_option('breakdance_settings_revision_limit', $revisionLimit);
        }
    }

    $enable_skip_link = (bool) \Breakdance\Data\get_global_option('enable_skip_link');
    $skip_link_text = (string) \Breakdance\Data\get_global_option('skip_link_text') ?: '';

    // load data for use in form
    /** @var "yes"|false $enable_simulate_the_content */
    $enable_simulate_the_content = (bool) \Breakdance\Data\get_global_option('breakdance_settings_enable_simulate_the_content');

    /** @var "yes"|false $allow_unfiltered_html */
    $allow_unfiltered_html = (bool) \Breakdance\Data\get_global_option('breakdance_settings_allow_unfiltered_html');

    /** @var "yes"|false $enable_svg_uploads */
    $enable_svg_uploads = (bool) \Breakdance\Data\get_global_option('breakdance_settings_enable_svg_uploads');

    /** @var "yes"|false $enable_render_performance_debug */
    $enable_render_performance_debug = (bool) \Breakdance\Data\get_global_option('enable_render_performance_debug');

    /** @var string|false $enable_revision_limit */
    $enable_revision_limit = \Breakdance\Data\get_global_option('breakdance_settings_enable_revision_limit');

    $revision_limit = (string) \Breakdance\Data\get_global_option('breakdance_settings_revision_limit') ?: BREAKDANCE_N_OF_LAST_REVISIONS_TO_KEEP;

    $bdoroxy = __bdox('plugin_name');

    $docs_link_security = __bdox('docs_link_security');

?>

    <h2><?php esc_html_e('Advanced', 'breakdance'); ?></h2>

    <form action="" method="post">
        <?php wp_nonce_field($nonce_action); ?>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        <?php esc_html_e('Allow SVG Uploads In The WP Media Library', 'breakdance'); ?>
                    </th>
                    <td>
                        <fieldset>
                            <label for="enable_svg_uploads">
                                <input type="checkbox" <?php echo $enable_svg_uploads ? 'checked' : ''; ?> name="enable_svg_uploads" value="yes" id="enable_svg_uploads"> <?php esc_html_e('Enable', 'breakdance'); ?>
                            </label>
                        </fieldset>

                        <p class="description"><?php
                            /* translators: 1: Opening <a> tag for security docs, 2: Closing </a> tag, 3: Opening <a> tag for Safe SVG plugin, 4: Closing </a> tag */
                            printf(esc_html__('Allowing SVG uploads can be dangerous, and you should understand the %1$spotential security implications%2$s. If in doubt, use a plugin like %3$sSafe SVG%4$s instead of this option.', 'breakdance'), '<a href="' . esc_url($docs_link_security) . '" target="_blank">', '</a>', '<a href="https://wordpress.org/plugins/safe-svg/" target="_blank">', '</a>');
                        ?></p>
                    </td>
                </tr>
                <tr>
                  <th scope="row">
                    Accessibility: Skip Link
                  </th>
                  <td>
                    <fieldset>
                      <label for="enable_skip_link">
                        <input type="checkbox" <?php echo $enable_skip_link ? 'checked' : ''; ?> name="enable_skip_link" value="yes" id="enable_skip_link"> Enable
                      </label>
                    </fieldset>

                    <fieldset>
                      <label for="skip_link_text">
                        <input type="text" name="skip_link_text" value="<?php echo esc_attr($skip_link_text); ?>" placeholder="Skip Link Text" id="skip_link_text" class="regular-text">
                      </label>
                    </fieldset>

                    <p class="description">Enables a skip link and wraps the content in a <code>&lt;main&gt;</code> tag to allow users to skip the header and go straight to the main content. This is useful for screen readers and keyboard navigation.</p>
                  </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php
                            /* translators: 1: Filter name in <code> tags, 2: Plugin name */
                            printf(esc_html__('Apply %1$s filter to %2$s content', 'breakdance'), '<code>the_content</code>', esc_html($bdoroxy));
                        ?>
                    </th>
                    <td>
                        <fieldset>
                            <label for="enable_simulate_the_content">
                                <input type="checkbox" <?php echo $enable_simulate_the_content ? 'checked' : ''; ?> name="enable_simulate_the_content" value="yes" id="enable_simulate_the_content"> <?php esc_html_e('Enable', 'breakdance'); ?>
                            </label>
                        </fieldset>

                        <p class="description"><?php
                            /* translators: 1: Plugin name (used multiple times), 2: Filter name in <code> tags, 3: Function call in <code> tags, 4: Opening <a> tag for security docs, 5: Closing </a> tag */
                            printf(esc_html__('By default, %1$s does not apply %2$s filter to %1$s-designed content. You can enable this option to make %1$s run %3$s on singular content created with %1$s, but you should understand the %4$spotential security implications%5$s.', 'breakdance'), esc_html($bdoroxy), '<code>the_content</code>', '<code>apply_filters(\'the_content\', ...)</code>', '<a href="' . esc_url($docs_link_security) . '">', '</a>');
                        ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php esc_html_e('Allow unfiltered HTML in all dynamic data output', 'breakdance'); ?>
                    </th>
                    <td>
                        <fieldset>
                            <label for="allow_unfiltered_html">
                                <input type="checkbox" <?php echo $allow_unfiltered_html ? 'checked' : ''; ?> name="allow_unfiltered_html" value="yes" id="allow_unfiltered_html"> <?php esc_html_e('Allow', 'breakdance'); ?>
                            </label>
                        </fieldset>

                        <p class="description"><?php
                            /* translators: 1: Plugin name, 2: Function name in <code> tags, 3: Capability name in <code> tags, 4: Opening <a> tag for security docs, 5: Closing </a> tag */
                            printf(esc_html__('By default, %1$s applies %2$s to fields on posts with an author that lacks the %3$s capability. You can disable this if you understand the %4$spotential security implications%5$s.', 'breakdance'), esc_html($bdoroxy), '<code>wp_kses_post</code>', '<code>unfiltered_html</code>', '<a href="' . esc_url($docs_link_security) . '">', '</a>');
                        ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php esc_html_e('Enable rendering performance debugger', 'breakdance'); ?>
                    </th>
                    <td>
                        <fieldset>
                            <label for="enable_render_performance_debug">
                                <input type="checkbox" <?php echo $enable_render_performance_debug ? 'checked' : ''; ?> name="enable_render_performance_debug" value="yes" id="enable_render_performance_debug">
                                <?php esc_html_e('Enable', 'breakdance'); ?>
                            </label>
                        </fieldset>

                        <p class="description"><?php
                            /* translators: 1: Opening <a> tag, 2: Closing </a> tag */
                            printf(esc_html__('Use %1$sServer Timing API%2$s to audit rendering performance. This option is intended for debugging only. It is recommended to enable it while troubleshooting and disable it afterward to avoid unnecessary overhead.', 'breakdance'), '<a href="https://developer.mozilla.org/en-US/docs/Web/API/Performance_API/Server_timing" target="_blank">', '</a>');
                        ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
        <h3><?php esc_html_e('Revisions', 'breakdance'); ?></h3>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        <?php esc_html_e('Limit Revisions', 'breakdance'); ?>
                    </th>
                    <td>
                        <fieldset>
                            <label for="enable_revision_limit">
                                <input type="checkbox" <?php echo $enable_revision_limit ? 'checked' : ''; ?> name="enable_revision_limit" value="yes" id="enable_revision_limit"> <?php esc_html_e('Enable', 'breakdance'); ?>
                            </label>
                        </fieldset>

                        <p class="description"><?php
                            /* translators: 1: Plugin name, 2: Setting name in <code> tags */
                            printf(esc_html__('For each page, on save, if the quantity of stored revisions of %1$s content is higher than the %2$s setting, the oldest revisions will be removed your database.', 'breakdance'), esc_html($bdoroxy), '<code>' . esc_html__('Max Revisions', 'breakdance') . '</code>');
                        ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php esc_html_e('Max Revisions', 'breakdance'); ?>
                    </th>
                    <td>
                        <fieldset>
                            <label for="revision_limit">
                                <input type="number" value="<?php echo (string) $revision_limit; ?>" name="revision_limit" id="revision_limit">
                            </label>
                        </fieldset>

                        <p class="description"><?php
                            /* translators: 1: Setting name in <code> tags, 2: Plugin name */
                            printf(esc_html__('If %1$s is enabled, this is the max number of revisions of %2$s content that will be kept for each page.', 'breakdance'), '<code>' . esc_html__('Limit Revisions', 'breakdance') . '</code>', esc_html($bdoroxy));
                        ?></p>
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'breakdance'); ?>">
        </p>

    </form>
<?php
}
