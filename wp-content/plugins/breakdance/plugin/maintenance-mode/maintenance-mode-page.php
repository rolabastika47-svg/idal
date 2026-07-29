<?php

namespace Breakdance\MaintenanceMode;

use function Breakdance\Permissions\_getRoles;

$modeOptions = [
    'disabled' => esc_html__('Disabled', 'breakdance'),
    'coming-soon' => esc_html__('(200) Coming Soon', 'breakdance'),
    'maintenance' => esc_html__('(503) Maintenance', 'breakdance'),
];

$userStatuses = [
    'auth' => esc_html__('Logged in', 'breakdance'),
    'auth-with-status' => esc_html__('Logged in with role', 'breakdance'),
];

/** @var \WP_Post[] $pages */
$pages = get_pages();

$roles = _getRoles();
$userAuthStatus = (string) optionsGetter('status', 'auth')
?>

<form action="" method="post">
    <?php wp_nonce_field('breakdance_admin_maintenance-mode_tab'); ?>

    <h2><?php esc_html_e('Maintenance', 'breakdance'); ?></h2>

    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th class='valign-th-middle' scope="row"><?php esc_html_e('Mode', 'breakdance'); ?></th>
                <td>
                    <select name="mode">
                    <?php foreach ($modeOptions as $key => $option) : ?>
                        <option
                            value="<?php echo $key; ?>"
                            <?php echo $key === optionsGetter('mode', 'disabled') ? 'selected' : ''; ?>
                        >
                            <?php echo $option; ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th class='valign-th-middle' scope="row"><?php esc_html_e('Page', 'breakdance'); ?></th>
                <td>
                    <select name="page">
                        <option value="none"><?php esc_html_e('Select a page&hellip;', 'breakdance'); ?></option>
                    <?php foreach ($pages as $page) : ?>
                        <option
                            value="<?php echo $page->ID; ?>"
                            <?php echo (string) $page->ID === optionsGetter('page', 'none') ? 'selected' : ''; ?>
                        >
                            <?php echo esc_html($page->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                    <p class="description">
                        <small>
                            <em><?php esc_html_e('This will be displayed to the user when maintenance mode is enabled.', 'breakdance'); ?></em>
                        </small>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <h2><?php esc_html_e('Access Control', 'breakdance'); ?></h2>
    <p><?php esc_html_e('Users can bypass maintenance mode using any of the options listed below.', 'breakdance'); ?></p>

    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th class='valign-th-middle' scope="row"><?php esc_html_e('Authentication Status', 'breakdance'); ?></th>
                <td>
                    <fieldset>
                        <select name="status" id="breakdance-maintenance-roles-selector-input">
                        <?php foreach ($userStatuses as $key => $value) : ?>
                            <option
                                value="<?php echo $key; ?>"
                                <?php echo $key === optionsGetter('status', 'auth') ? 'selected' : ''; ?>
                            >
                                <?php echo $value; ?>
                            </option>
                        <?php endforeach; ?>
                        </select>
                    </fieldset>

                    <div id="breakdance-maintenance-user-roles"
                         style="<?php echo $userAuthStatus === 'auth-with-status' ? '' : 'display: none;' ?>"
                    >
                        <h4><?php esc_html_e('User Roles', 'breakdance'); ?></h4>
                        <fieldset>
                        <?php foreach ($roles as $key => $role) : ?>
                            <label for="breakdance-maintenance-user-role-<?php echo $key; ?>">
                                <input
                                    type="checkbox"
                                    name="user-roles[]"
                                    value="<?php echo $key; ?>"
                                    id="breakdance-maintenance-user-role-<?php echo $key; ?>"
                                    <?php echo in_array($key, (array) optionsGetter('user-roles')) ? 'checked' : ''; ?>
                                />
                                <span>
                                <?php
                                    echo esc_html($role);
                                ?>
                                </span>
                            </label>
                            <br />
                        <?php endforeach; ?>
                        </fieldset>
                    </div>
                </td>
            </tr>
            <tr>
                <th class='valign-th-middle' scope="row"><?php esc_html_e('URL param', 'breakdance'); ?></th>
                <td>
                    <fieldset>
                        <label for="breakdance-maintenance-enable-url-params">
                            <input
                                type="checkbox"
                                name="url-params"
                                id="breakdance-maintenance-enable-url-params"
                                <?php echo optionsGetter('url-params') === "on" ? "checked" : ""; ?>
                            />
                            <span><?php esc_html_e('Enable URL Param', 'breakdance'); ?></span>
                        </label>
                        <br />
                        <code><?php echo esc_html(get_home_url()); ?>?</code>
                        <input
                            name="url-params-key"
                            type="text"
                            placeholder="<?php esc_attr_e('key', 'breakdance'); ?>"
                            value="<?php echo esc_attr((string) optionsGetter('url-params-key')); ?>"
                        >
                        <code> =</code>
                        <input
                            name="url-params-value"
                            type="text"
                            placeholder="<?php esc_attr_e('value', 'breakdance'); ?>"
                            value="<?php echo esc_attr((string) optionsGetter('url-params-value')); ?>"
                        />
                        <?php if (optionsGetter('url-params') === 'on') : ?>
                            <button
                                type="button"
                                class="button"
                                id="breakdance-maintenance-url-params-copy-button"
                                data-clipboard-text="<?php echo getGeneratedURLParamsURL(); ?>"
                            >
                                <?php esc_html_e('Copy', 'breakdance'); ?>
                            </button>
                        <?php endif; ?>
                    </fieldset>
                    <p class="description">
                        <small>
                            <em><?php esc_html_e('Users with any of the URL parameters defined above set will have access to the site.', 'breakdance'); ?></em>
                        </small>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <p class="submit">
        <input
            type="submit"
            name="breakdance-maintenance-mode-submit"
            id="submit"
            class="button button-primary"
            value="<?php esc_attr_e('Save Changes', 'breakdance'); ?>"
        />
    </p>
</form>


<script>
    (function ($) {
        const $rolesBox = $('#breakdance-maintenance-user-roles');
        const $rolesSelectorInput = $('#breakdance-maintenance-roles-selector-input');

        $rolesSelectorInput.on('change', function () {
            $value = $(this).val();

            if ($value === 'auth-with-status') {
                $rolesBox.show();
            } else {
                $rolesBox.hide();
            }
        });
    }(jQuery));
</script>

<?php if (optionsGetter('url-params') === 'on') : ?>
<script>
    (function ($) {
        const $copyButton = $('#breakdance-maintenance-url-params-copy-button');

        $copyButton.click(function () {
            const self = $(this);
            const originalText = self.text();

            self.text('Copied');
            setTimeout(function () {
                self.text(originalText);
            }, 1500)
        });

        new ClipboardJS('.button');
    }(jQuery));
</script>
<?php endif; ?>
