<?php

namespace Breakdance\Themeless;

use function Breakdance\BrowseMode\isRequestFromBrowserIframe;
use function Breakdance\Data\get_global_option;
use function Breakdance\isRequestFromBuilderIframe;
use function Breakdance\isRequestFromBuilderSsr;
use function Breakdance\Themeless\ThemeDisabler\is_theme_disabled;
use function Breakdance\Themeless\ThemeDisabler\is_zero_theme_enabled;

/**
 * @param int|\WP_Post|null $postId
 * @param int|null $repeaterItemNodeId
 * @return bool
 */
function shouldEnableSkipLink($postId = null, $repeaterItemNodeId = null)
{
    // Disable skip link for repeater items and global blocks.
    if ($repeaterItemNodeId) {
        return false;
    }

    // New installs will have the skip link enabled by default.
    if (get_global_option('enable_skip_link') !== 'yes') {
        return false;
    }

    if (isRequestFromBuilderIframe() || isRequestFromBuilderSsr() || isRequestFromBrowserIframe()) {
        return false;
    }

    $postType = get_post_type($postId);

    if ($postType === BREAKDANCE_HEADER_POST_TYPE || $postType === BREAKDANCE_FOOTER_POST_TYPE) {
        return false;
    }

    return true;
}

function outputSkipLink()
{
    if (!shouldEnableSkipLink()) return;
    $prefix = BREAKDANCE_MODE === 'oxygen' ? 'oxy' : 'bde';
    ?>
    <a href="#<?php echo $prefix; ?>-main" class="<?php echo $prefix; ?>-skip-link"><?php echo esc_html((string) get_global_option('skip_link_text') ?: __('Skip to main content', 'breakdance')); ?></a>
    <?php
}

/**
 * @param string $content
 * @param int|\WP_Post|null $postId
 * @param int|null $repeaterItemNodeId
 * @return string
 */
function maybeWrapInMainTag($content, $postId = null, $repeaterItemNodeId = null)
{
    /**
     * @psalm-suppress TooManyArguments
     */
    if (!bdox_run_filters('breakdance_render_wrap_in_main_tag', true, $postId, $repeaterItemNodeId)) {
        return $content;
    }

    $skipLinkId = BREAKDANCE_MODE === 'oxygen' ? 'oxy-main' : 'bde-main';

    if (is_theme_disabled() || is_zero_theme_enabled()) {
        return shouldEnableSkipLink($postId, $repeaterItemNodeId)
            ? "<main id='" . $skipLinkId . "'>{$content}</main>"
            : $content;
    }

    // Most themes already add the `<main>` tag, so we avoid duplicating it.
    // This is based on assumption, as there’s no reliable way to detect it.
    return shouldEnableSkipLink($postId, $repeaterItemNodeId)
        ? "<div class='breakdance' id='" . $skipLinkId . "'>{$content}</div>"
        : "<div class='breakdance'>{$content}</div>";
}
