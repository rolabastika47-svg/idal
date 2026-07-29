<h2><?php esc_html_e('Performance', 'breakdance'); ?></h2>
<form action="" method="post">
    <?php wp_nonce_field('breakdance_admin_bloat-eliminator_tab'); ?>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <?php esc_html_e('Gutenberg Blocks CSS', 'breakdance'); ?>
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-gutenberg-blocks-css">
                            <input type="checkbox" name="gutenberg-blocks-css" id="breakdance-bloat-eliminator-gutenberg-blocks-css" <?php echo in_array('gutenberg-blocks-css', $bloatOptions) ? ' checked' : ''; ?> />
                            <span><?php esc_html_e('Remove Gutenberg Blocks CSS', 'breakdance'); ?></span>
                        </label>
                    </fieldset>
                    <p class='description'>
                        <?php esc_html_e('The Gutenberg Block Editor loads 11kb of CSS even if you don\'t use it. Remove it if you\'re not using the Gutenberg Block Editor.', 'breakdance'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e('XML-RPC Pingbacks', 'breakdance'); ?>
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-xml-rpc">
                            <input type="checkbox" name="xml-rpc" id="breakdance-bloat-eliminator-xml-rpc" <?php echo in_array('xml-rpc', $bloatOptions) ? ' checked' : ''; ?> />
                            <span><?php esc_html_e('Remove & Disable Pingbacks', 'breakdance'); ?></span>
                        </label>
                    </fieldset>
                    <p class='description'>
                        <?php esc_html_e('Say goodbye to pingback spam. This option removes the XML-RPC pingback information from the <head>, and disables the WP Pingback system.', 'breakdance'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e('WP Emoji', 'breakdance'); ?>
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-wp-emoji">
                            <input type="checkbox" name="wp-emoji" id="breakdance-bloat-eliminator-wp-emoji" <?php echo in_array('wp-emoji', $bloatOptions) ? ' checked' : ''; ?> />
                            <span><?php esc_html_e('Disable built-in WordPress JavaScript for rendering emojis.', 'breakdance'); ?></span>
                        </label>
                    </fieldset>
                    <p class='description'>
                        <?php esc_html_e('WordPress loads a 10kb JavaScript to handle emojis on every page of your website, even if you don\'t use emojis. Modern browsers support emojis out-of-the-box, with no need for JavaScript.', 'breakdance'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e('Dashicons', 'breakdance'); ?>
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-wp-dashicons">
                            <input type="checkbox" name="wp-dashicons" id="breakdance-bloat-eliminator-wp-dashicons" <?php echo in_array('wp-dashicons', $bloatOptions) ? ' checked' : ''; ?> />
                            <span><?php esc_html_e('Disable admin icons for logged out users', 'breakdance'); ?></span>
                        </label>
                    </fieldset>
                    <p class='description'>
                        <?php esc_html_e('WordPress loads its admin panel icons for all users, even though they are typically only needed for logged in users that have access to the admin panel.', 'breakdance'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e('OEmbed', 'breakdance'); ?>
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-wp-oembed">
                            <input type="checkbox" name="wp-oembed" id="breakdance-bloat-eliminator-wp-oembed" <?php echo in_array('wp-oembed', $bloatOptions) ? ' checked' : ''; ?> />
                            <span><?php esc_html_e('Disable OEmbed', 'breakdance'); ?></span>
                        </label>
                    </fieldset>
                    <p class='description'>
                        <?php esc_html_e('Disables the automatic embedding of some content such as YouTube videos, Tweets, etc. when pasting the URL into your blog posts.', 'breakdance'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e('RSD Links', 'breakdance'); ?>
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-rsd-links">
                            <input type="checkbox" name="rsd-links" id="breakdance-bloat-eliminator-rsd-links" <?php echo in_array('rsd-links', $bloatOptions) ? ' checked' : ''; ?> />
                            <span><?php esc_html_e('Disable Really Simple Discovery feature', 'breakdance'); ?></span>

                        </label>
                    </fieldset>
                    <p class='description'>
                        <?php esc_html_e('Really Simple Discovery is a spec from 2003 related to desktop blog-publishing applications. It is also often used by XML-RPC clients to find out information about your WordPress blog.', 'breakdance'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e('WLW Link', 'breakdance'); ?>
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-wlw-link">
                            <input type="checkbox" name="wlw-link" id="breakdance-bloat-eliminator-wlw-link" <?php echo in_array('wlw-link', $bloatOptions) ? ' checked' : ''; ?> />
                            <span><?php esc_html_e('Disable Windows Live Writer Link', 'breakdance'); ?></span>
                        </label>
                    </fieldset>
                    <p class="description"><?php esc_html_e('Windows Live Writer was a desktop blog-publishing application. It was EOL in 2012, and completely discontinued in 2017.', 'breakdance'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e('REST API', 'breakdance'); ?>
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-rest-api">
                            <input type="checkbox" name="rest-api" id="breakdance-bloat-eliminator-rest-api" <?php echo in_array('rest-api', $bloatOptions) ? ' checked' : ''; ?> />
                            <span><?php esc_html_e('Disable WP REST metadata', 'breakdance'); ?></span>
                        </label>
                    </fieldset>
                    <p class='description'>
                        <?php esc_html_e('This option removes WP REST metadata from your <head>. It does not disable the REST API.', 'breakdance'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e('WP Generator', 'breakdance'); ?>
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-wp-generator">
                            <input type="checkbox" name="wp-generator" id="breakdance-bloat-eliminator-wp-generator" <?php echo in_array('wp-generator', $bloatOptions) ? ' checked' : ''; ?> />
                            <span><?php esc_html_e('Disable WordPress Generator Meta Tag', 'breakdance'); ?></span>
                        </label>
                    </fieldset>
                    <p class='description'>
                        <?php esc_html_e('Stop WordPress from adding the <meta name="generator" content="WordPress x.x.x"> tag to your head.', 'breakdance'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e('Remove Shortlink', 'breakdance'); ?>
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-shortlink">
                            <input type="checkbox" name="shortlink" id="breakdance-bloat-eliminator-shortlink" <?php echo in_array('shortlink', $bloatOptions) ? ' checked' : ''; ?> />
                            <span><?php esc_html_e('Disable Shortlink Tag', 'breakdance'); ?></span>
                        </label>
                    </fieldset>
                    <p class='description'>
                        <?php esc_html_e('WordPress includes a <link rel=shortlink ...> into the head if a shortlink is defined for the current page.', 'breakdance'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e('Relational Links', 'breakdance'); ?>
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-rel-links">
                            <input type="checkbox" name="rel-links" id="breakdance-bloat-eliminator-rel-links" <?php echo in_array('rel-links', $bloatOptions) ? ' checked' : ''; ?> />
                            <span><?php esc_html_e('Disable Relational Links For Single Posts', 'breakdance'); ?></span>
                        </label>
                    </fieldset>
                    <p class='description'>
                        <?php esc_html_e('For single posts, WordPress places relational links in the head for the posts adjacent to the current post.', 'breakdance'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e('RSS Feed', 'breakdance'); ?>
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-feed-links">
                            <input type="checkbox" name="feed-links" id="breakdance-bloat-eliminator-feed-links" <?php echo in_array('feed-links', $bloatOptions) ? ' checked' : ''; ?> />
                            <span><?php esc_html_e('Disable RSS Links', 'breakdance'); ?></span>
                        </label>
                    </fieldset>
                    <p class='description'>
                        <?php esc_html_e('This option removes the RSS feed information from the <head>. It does not disable the RSS feeds.', 'breakdance'); ?>
                    </p>
                </td>
            </tr>

        </tbody>
    </table>

    <p class="submit">
        <input type="submit" name="breakdance-bloat-eliminator-settings" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'breakdance'); ?>" />
    </p>
</form>
