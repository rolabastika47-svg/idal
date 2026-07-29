<?php

namespace Breakdance\Themeless\ThemeDisabler\SettingsTab;

use function Breakdance\Data\get_global_option;
use function Breakdance\Util\is_post_request;

add_action('breakdance_register_admin_settings_page_register_tabs', '\Breakdance\Themeless\ThemeDisabler\SettingsTab\register');

function register()
{

    if (BREAKDANCE_MODE !== 'breakdance') {
        return;
    }

    \Breakdance\Admin\SettingsPage\addTab(esc_html__('Theme', 'breakdance'), 'theme_disabler', '\Breakdance\Themeless\ThemeDisabler\SettingsTab\tab', 100);
}

function tab()
{
    $is_theme_disabled = (string) get_global_option('is_theme_disabled');

    $nonce_action = 'breakdance_admin_theme-disabler_tab';
    if (is_post_request() && check_admin_referer($nonce_action)) {
        $is_theme_disabled = (string) filter_input(INPUT_POST, 'is_theme_disabled');
        \Breakdance\Data\set_global_option('is_theme_disabled', $is_theme_disabled);
    }
?>

    <style>
        .disable-theme-recommended {
            font-size: 0.65em;
            display: inline-block;
            padding: 5px;
            background-color: #d2f2b8;
            color: #31610a;
            line-height: 1;
            font-weight: 500;
            border-radius: 3px;
            position: relative;
            top: -2px;
        }
    </style>

    <h2><?php esc_html_e('Theme', 'breakdance'); ?></h2>


    <form action="" method="post">
        <?php wp_nonce_field($nonce_action); ?>


        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        <?php esc_html_e('WordPress Theme System', 'breakdance'); ?>
                    </th>
                    <td>
                        <fieldset>
                            <label for='disable_theme'>
                                <input type='radio' name='is_theme_disabled' id='disable_theme' value='yes' <?php echo $is_theme_disabled === 'yes' ? 'checked' : ''; ?> />
                                <?php esc_html_e('Disable Theme', 'breakdance'); ?> <span class='disable-theme-recommended'><?php esc_html_e('recommended', 'breakdance'); ?></span>
                                <p class="description"><?php esc_html_e('Design every part of your site with Breakdance. Disabling your theme gives you the best performance and maximum flexibility.', 'breakdance'); ?></p>

                            </label>
                            <br />
                            <label for='enable_theme'>
                                <input type='radio' name='is_theme_disabled' id='enable_theme' value='no' <?php echo $is_theme_disabled !== 'yes' ? 'checked' : ''; ?> />
                                <?php esc_html_e('Keep Theme', 'breakdance'); ?>
                                <p class="description"><?php esc_html_e('Your theme\'s header, footer, and templates will be used unless they are explicitly overridden in Breakdance. Your theme\'s styles may affect the design of Breakdance elements.', 'breakdance'); ?></p>

                                <p class='description'>
                                    <?php
                                        /* translators: 1: Opening <a> tag, 2: Closing </a> tag */
                                        printf(esc_html__('Need help choosing a theme? Use %1$sBreakdance Zero Theme%2$s, a completely blank theme designed for Breakdance with support for child themes, overriding plugin templates, and functions.php.', 'breakdance'), '<a href="https://breakdance.com/zero-theme" target="_blank">', '</a>');
                                    ?>
                                </p>

                            </label>
                        </fieldset>


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
