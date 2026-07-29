<?php

namespace Breakdance\Admin\SettingsPage\ElementsTab;

use function Breakdance\Admin\SettingsPage\addTab;
use function Breakdance\Data\get_global_option;
use function Breakdance\Data\set_global_option;
use function Breakdance\Util\is_post_request;

add_action('breakdance_register_admin_settings_page_register_tabs', '\Breakdance\Admin\SettingsPage\ElementsTab\register');

/**
 * Register the Elements tab in Breakdance Settings
 *
 * @return void
 */
function register()
{
    addTab('Elements', 'elements', '\Breakdance\Admin\SettingsPage\ElementsTab\tab', 4900);
}

/**
 * Render Elements tab
 *
 * @return void
 */
function tab()
{
    $nonce_action = 'breakdance_admin_elements_visibility_tab';

    if (is_post_request() && check_admin_referer($nonce_action)) {
        $hidden = isset($_POST['breakdance_hidden_elements']) && is_array($_POST['breakdance_hidden_elements'])
            ? array_map(
                function ($v) {
                    /** @psalm-suppress PossiblyInvalidArgument */
                    return sanitize_text_field(wp_unslash((string) $v));
                },
                (array) $_POST['breakdance_hidden_elements']
            )
            : [];

        // Save selected classnames as the hidden elements list
        set_global_option('builder_hidden_elements', array_values(array_unique($hidden)));
    }

    /** @var string[] $savedHidden */
    $savedHidden = get_global_option('builder_hidden_elements') ?: [];

    // Build the list of available elements with labels for the UI
    $elements = \Breakdance\Elements\get_element_classnames();

    /** @var array{label:string,value:string}[] $elementsForUi */
    $elementsForUi = array_map(
        function ($elementClassName) {
            /** @psalm-suppress InvalidStringClass */
            return [
                'value' => $elementClassName,
                'label' => $elementClassName::name(),
            ];
        },
        $elements
    );

    // Sort by human label for easier scanning
    usort(
        $elementsForUi,
        /**
         * @param array{label:string,value:string} $a
         * @param array{label:string,value:string} $b
         */
        fn($a, $b) => strcasecmp($a['label'], $b['label'])
    );
    ?>
    <h2>Elements</h2>
    <form action="" method="post">
        <?php wp_nonce_field($nonce_action); ?>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row" class="valign-th-middle">
                        Hidden Elements
                    </th>
                    <td>
                        <p class="description" style="margin-bottom: 12px;">Select elements to hide from the Add panel in the builder. Hidden elements will not appear in the Add panel, but existing instances in designs remain functional.</p>

                        <div style="display:flex; gap:24px; align-items: center; margin-bottom: 12px;">
                            <input type="text" id="bd-elements-filter" placeholder="Filter elements…" style="max-width: 360px; width: 100%;">
                            <button type="button" class="button" id="bd-hide-select-all">Select All</button>
                            <button type="button" class="button" id="bd-hide-clear-all">Clear All</button>
                        </div>
                        <div id="bd-elements-list" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 8px; max-height: 480px; overflow: auto; padding: 8px; border: 1px solid #f0f0f1; border-radius: 4px; background: #fff;">
                            <?php foreach ($elementsForUi as $item): ?>
                                <?php
                                    $value = $item['value'];
                                    $label = $item['label'];
                                    $checked = in_array($value, $savedHidden, true) ? 'checked' : '';
                                ?>
                                <label class="bd-element-item" style="display:flex; align-items:center; gap:8px; padding:6px 8px; border-radius:4px;">
                                    <input type="checkbox" name="breakdance_hidden_elements[]" value="<?php echo esc_attr($value); ?>" <?php echo $checked; ?>>
                                    <span data-label="<?php echo esc_attr($label); ?>" data-value="<?php echo esc_attr($value); ?>">
                                        <?php echo esc_html($label); ?>
                                        <small style="display:block; color:#777;">(<?php echo esc_html($value); ?>)</small>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <script>
                            (function() {
                                const filter = document.getElementById('bd-elements-filter');
                                const list = document.getElementById('bd-elements-list');
                                const selectAllBtn = document.getElementById('bd-hide-select-all');
                                const clearAllBtn = document.getElementById('bd-hide-clear-all');

                                if (!filter || !list) return;

                                function normalize(s) { return (s || '').toLowerCase(); }

                                filter.addEventListener('input', () => {
                                    const q = normalize(filter.value);
                                    const items = list.querySelectorAll('.bd-element-item');

                                    items.forEach((item) => {
                                        const span = item.querySelector('span');
                                        const label = normalize(span.getAttribute('data-label'));
                                        const value = normalize(span.getAttribute('data-value'));
                                        const match = !q || label.indexOf(q) !== -1 || value.indexOf(q) !== -1;
                                        item.style.display = match ? '' : 'none';
                                    });
                                });

                                function setAll(checked) {
                                    const checkboxes = list.querySelectorAll('input[type="checkbox"]');
                                    checkboxes.forEach((cb) => cb.checked = checked);
                                }

                                if (selectAllBtn) {
                                    selectAllBtn.addEventListener('click', () => setAll(true));
                                }

                                if (clearAllBtn) {
                                    clearAllBtn.addEventListener('click', () => setAll(false));
                                }
                            })();
                        </script>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
        </p>
    </form>
<?php
}

/**
 * Filter builder elements based on saved hidden list.
 * This removes hidden classnames from the visible elements array.
 *
 * @param string[] $elements
 * @return string[]
 */
function filter_builder_elements($elements)
{
    /** @var mixed $hidden */
    $hidden = get_global_option('builder_hidden_elements') ?: [];

    if (!is_array($hidden) || count($hidden) === 0) {
        return $elements;
    }

    return array_values(array_diff($elements, $hidden));
}
add_filter('breakdance_builder_elements', '\Breakdance\Admin\SettingsPage\ElementsTab\filter_builder_elements', 10, 1);


