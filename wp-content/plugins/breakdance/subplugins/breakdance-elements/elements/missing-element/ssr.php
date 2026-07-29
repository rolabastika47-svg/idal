<?php

use function Breakdance\BreakdanceOxygen\Strings\__bdox;
use function Breakdance\isRequestFromBuilderSsr;

if (!\Breakdance\Permissions\hasMinimumPermission('edit')) {
    echo esc_html(sprintf('<!-- This element is missing. Please open the page in %s and check the browser console for details. -->', __bdox('plugin_name')), 'breakdance-elements');
} else {
?>
    <div class="bde-missing-element__message">
        <?php if (isRequestFromBuilderSsr()) { ?>
            <p><?php echo esc_html__('This element is missing. Please check the browser console for details.', 'breakdance-elements'); ?></p>
        <?php } else { ?>
            <p><?php echo sprintf('This element is missing. Please open the page in %s and check the browser console for details.', __bdox('plugin_name')); ?></p>
        <?php } ?>
        <p><?php echo wp_kses(
            sprintf(__('Have questions? We\'re here to help. <a href="%s" target="_blank">Click here</a> to contact support.', 'breakdance-elements'), 'https://breakdance.com/support/'),
            [
                'a' => [
                    'href' => [],
                    'target' => [],
                ],
            ]
        ); ?></p>
    </div>
<?php
}
