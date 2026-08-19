<?php

namespace Breakdance\DesignLibrary\Tab;

use function Breakdance\DesignLibrary\hideDesignLibrary;
use function Breakdance\DesignLibrary\hideDesignLibraryInBuilder;
use function Breakdance\Setup\admin_notice;
use function Breakdance\Util\is_post_request;
use function Breakdance\Data\set_global_option;
use function Breakdance\DesignLibrary\getDesignLibraryUrl;
use function Breakdance\DesignLibrary\getPassword;
use function Breakdance\DesignLibrary\setPassword;
use function Breakdance\DesignLibrary\getRegisteredDesignSets;
use function Breakdance\DesignLibrary\setDesignSets;
use function Breakdance\BreakdanceOxygen\Strings\__bdox;

add_action('breakdance_register_admin_settings_page_register_tabs', '\Breakdance\DesignLibrary\Tab\register');

function register()
{
    \Breakdance\Admin\SettingsPage\addTab(esc_html__('Design Library', 'breakdance'), 'design_library', '\Breakdance\DesignLibrary\Tab\tab', 1200);
}

function submit()
{
    $designLibraryEnabled = array_key_exists('is_copy_from_frontend_enabled', $_POST) ? 'yes' : false;
    $copyButtonEnabled = array_key_exists('is_copy_button_on_frontend_enabled', $_POST) ? 'yes' : false;
    $reliesOnGlobalSettings = array_key_exists('design_library_relies_on_global_settings', $_POST) ? 'yes' : false;
    $reliesOnDesignPresets = array_key_exists('design_library_relies_on_design_presets', $_POST) ? 'yes' : false;
    $hideOnThisWebsite = array_key_exists('design_library_hide_on_this_website', $_POST) ? 'yes' : false;
    $hideDesignLibraryInBuilder = array_key_exists('design_library_hide_in_builder', $_POST) ? 'yes' : false;
    $design_library_full_site_import_enabled = array_key_exists('design_library_full_site_import_enabled', $_POST) ? 'yes' : false;

    /** @var string */
    $password = filter_input(INPUT_POST, 'design_library_password', FILTER_UNSAFE_RAW);

    /** @var string */
    $design_library_full_site_import_thumbnail_url = filter_input(INPUT_POST, 'design_library_full_site_import_thumbnail_url', FILTER_UNSAFE_RAW);

    /** @var string */
    $providers = filter_input(INPUT_POST, 'providers', FILTER_UNSAFE_RAW);

    set_global_option('is_copy_from_frontend_enabled', $designLibraryEnabled);
    set_global_option('is_copy_button_on_frontend_enabled', $copyButtonEnabled);
    set_global_option('design_library_relies_on_global_settings', $reliesOnGlobalSettings);
    set_global_option('design_library_relies_on_design_presets', $reliesOnDesignPresets);
    set_global_option('design_library_hide_on_this_website', $hideOnThisWebsite);
    set_global_option('design_library_hide_in_builder', $hideDesignLibraryInBuilder);
    set_global_option('design_library_full_site_import_enabled', $design_library_full_site_import_enabled);
    set_global_option('design_library_full_site_import_thumbnail_url', $design_library_full_site_import_thumbnail_url);

    setDesignSets($providers);
    setPassword($password);
}

function tab()
{
    $nonce_action = 'breakdance_admin_design_library_tab';

    if (is_post_request() && check_admin_referer($nonce_action)) {
        submit();
    }

    /**
     * @var string
     */
    $designLibraryEnabled = \Breakdance\Data\get_global_option('is_copy_from_frontend_enabled');

    /**
     * @var string
     */
    $copyButtonEnabled = \Breakdance\Data\get_global_option('is_copy_button_on_frontend_enabled');

    /**
     * @var string
     */
    $reliesOnGlobalSettings = \Breakdance\Data\get_global_option('design_library_relies_on_global_settings');

    /**
     * @var string
     */
    $reliesOnDesignPresets = \Breakdance\Data\get_global_option('design_library_relies_on_design_presets');

    $hideDesignLibraryOnThisWebsite = hideDesignLibrary();
    $hideDesignLibraryInBuilder = hideDesignLibraryInBuilder();

    /**
     * @var string
     */
    $design_library_full_site_import_enabled = \Breakdance\Data\get_global_option('design_library_full_site_import_enabled');

    /**
     * @var string
     */
    $design_library_full_site_import_thumbnail_url = \Breakdance\Data\get_global_option('design_library_full_site_import_thumbnail_url');

    $providers = implode("\n", getRegisteredDesignSets());

    $bdoroxy = __bdox('plugin_name');

    ?>
    <h2><?php esc_html_e('Design Library', 'breakdance'); ?></h2>

    <form action="" method="post">
        <?php wp_nonce_field($nonce_action); ?>
        <table class="form-table" role="presentation">
            <tbody>
            <tr>
                <th scope="row">
                    <?php esc_html_e('Design Set', 'breakdance'); ?>
                </th>
                <td>
                    <fieldset>
                        <div>
                            <label for="is_copy_from_frontend_enabled">
                                <input type="checkbox" <?php echo $designLibraryEnabled ? 'checked' : ''; ?> name="is_copy_from_frontend_enabled" value="yes" id="is_copy_from_frontend_enabled">
                                <?php esc_html_e('Turn This Website Into a Design Set', 'breakdance'); ?>
                            </label>
                        </div>
                        <div>
                            <label for="is_copy_button_on_frontend_enabled">
                                <input type="checkbox" <?php echo $copyButtonEnabled ? 'checked' : ''; ?> name="is_copy_button_on_frontend_enabled" value="yes" id="is_copy_button_on_frontend_enabled">
                                <?php esc_html_e('Enable Copy From Frontend', 'breakdance'); ?>
                            </label>
                        </div>
                        <div>
                            <label for="design_library_relies_on_global_settings">
                                <input type="checkbox" <?php echo $reliesOnGlobalSettings ? 'checked' : ''; ?> name="design_library_relies_on_global_settings" value="yes" id="design_library_relies_on_global_settings">
                                <?php esc_html_e('This Design Set Relies On Global Settings', 'breakdance'); ?>
                            </label>
                        </div>
                        <div>
                            <label for="design_library_relies_on_design_presets">
                                <input type="checkbox" <?php echo $reliesOnDesignPresets ? 'checked' : ''; ?> name="design_library_relies_on_design_presets" value="yes" id="design_library_relies_on_design_presets">
                                <?php esc_html_e('This Design Set Relies On Design Presets', 'breakdance'); ?>
                            </label>
                        </div>

                        <div>
                            <label for="design_library_full_site_import_enabled">
                                <input type="checkbox" <?php echo $design_library_full_site_import_enabled ? 'checked' : ''; ?> name="design_library_full_site_import_enabled" value="yes" id="design_library_full_site_import_enabled">
                                <?php esc_html_e('Full Site Import Enabled', 'breakdance'); ?>
                            </label>
                        </div>

                        <div>
                            <label for="design_library_full_site_import_thumbnail_url">
                                <?php esc_html_e('Thumbnail URL', 'breakdance'); ?>
                            </label>
                            <input type="text" name="design_library_full_site_import_thumbnail_url" value="<?php echo esc_attr($design_library_full_site_import_thumbnail_url); ?>" style="width: 400px">
                        </div>

                        <script type="text/javascript">
                          document.addEventListener('DOMContentLoaded', function() {
                            var checkbox = document.querySelector('input[name="design_library_full_site_import_enabled"]');
                            var thumbnailUrlDiv = document.querySelector('input[name="design_library_full_site_import_thumbnail_url"]').closest('div');

                            function toggleThumbnailUrl() {
                              if (checkbox.checked) {
                                thumbnailUrlDiv.style.display = '';
                              } else {
                                thumbnailUrlDiv.style.display = 'none';
                              }
                            }

                            // Initial toggle based on the checkbox state
                            toggleThumbnailUrl();

                            // Add event listener to the checkbox to toggle visibility on change
                            checkbox.addEventListener('change', toggleThumbnailUrl);
                          });
                        </script>

                    </fieldset>

                    <hr />

                    <?php if ($designLibraryEnabled): ?>
                        <p class="description">
                            <?php esc_html_e('Shareable URL:', 'breakdance'); ?> <input class="breakdance-design-library-shareable-url" type="url" value="<?php echo esc_url(getDesignLibraryUrl()); ?>" readonly style="width: 400px">
                        </p>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e('Password Protection', 'breakdance'); ?>
                </th>
                <td>
                    <input type="text" name="design_library_password" value="<?php echo esc_attr(getPassword()); ?>" placeholder="<?php esc_attr_e('Enter a password', 'breakdance'); ?>">
                    <p class="description">
                        <small>
                            <em><?php
                                /* translators: %s: Password parameter in HTML bold tags */
                                printf(esc_html__('Protect your Design Set with a password. (Optional) If the %s param is not provided, users will be prompted to enter this password.', 'breakdance'), '<b>?password=' . esc_html(getPassword()) . '</b>');
                            ?></em>
                        </small>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="providers"><?php esc_html_e('Custom Design Sets', 'breakdance'); ?></label></th>
                <td>

                    <fieldset>
                        <p>
                            <textarea class="large-text code" cols="50" id="providers" name="providers" rows="10"><?php echo esc_textarea($providers); ?></textarea>
                        </p>
                    </fieldset>
                    <p class="description">
                        <?php
                            if (BREAKDANCE_MODE === 'oxygen') {
                                printf(esc_html__('Add custom Design Sets to your Oxygen installation.', 'breakdance'));
                            } else {
                                /* translators: %1$s: Plugin name (used twice in the sentence) */
                                printf(esc_html__('Add custom Design Sets to your %1$s installation. By default, %1$s provides a list of official Design Sets, but you can use this field to add any custom design sets you want.', 'breakdance'), esc_html($bdoroxy));
                            }
                        ?>
                    </p>
                    <p><strong><?php esc_html_e('Insert one URL per line.', 'breakdance'); ?></strong></p>
                </td>
            </tr>
            <tr>
              <th scope="row">
                Client Protection
              </th>
              <td>
                <label for="design_library_hide_on_this_website">
                  <input type="checkbox" <?php echo $hideDesignLibraryOnThisWebsite ? 'checked' : ''; ?> name="design_library_hide_on_this_website" value="yes" id="design_library_hide_on_this_website">
                  Hide Design Library On This Website
                </label>

                <p class="description" style="margin-bottom: 15px">
                  <small>
                    <em>Hides the Design Library from the admin menu and blocks access to all design sets except “This Website” or the last one you imported. Useful for preventing clients from importing new sets while still allowing use of the one you imported.</em>
                  </small>
                </p>

                <label for="design_library_hide_in_builder">
                  <input type="checkbox" <?php echo $hideDesignLibraryInBuilder ? 'checked' : ''; ?> name="design_library_hide_in_builder" value="yes" id="design_library_hide_in_builder">
                  Hide Design Library In Builder
                </label>

                <p class="description">
                  <small>
                    <em>Hides the Design Library from the Builder only.</em>
                  </small>
                </p>
              </td>
            </tr>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'breakdance'); ?>">
        </p>

        <script>
          document.querySelector('.breakdance-design-library-shareable-url')
            ?.addEventListener('focus', (event) => {
              event.currentTarget.select();
            });

            async function checkIfDesignSetsAreValid() {
                const formData = new FormData();
                formData.append("action", "breakdance_get_invalid_design_sets");

                const request = await fetch(window.ajaxurl, {
                    method: "POST",
                    credentials: "same-origin",
                    body: formData
                });

                const invalidUrls = await request.json();

                const notice = document.createElement('div');
                notice.classList.add('notice', 'notice-error');

                if (invalidUrls.length > 0) {
                    notice.innerHTML = `
                        <p>${invalidUrls.length === 1 ? 'The following URL is an invalid design set:' : 'The following URLs are invalid design sets'}</p>
                        <ul>
                            ${invalidUrls.map(url => `<li><strong>${url}</strong></li>`).join('')}
                        </ul>
                    `;

                    document.querySelector('.breakdance-admin__content').prepend(notice);
                }
            }

            checkIfDesignSetsAreValid();
        </script>
    </form>
    <?php
}
