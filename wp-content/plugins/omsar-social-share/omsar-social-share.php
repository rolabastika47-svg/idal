<?php
/**
 * Plugin Name: OMSAR Social Share
 * Plugin URI: https://ids.com.lb
 * Description: Custom social media sharing plugin with configurable options. Allows users to share content on Facebook, Twitter, LinkedIn, WhatsApp, and copy links.
 * Version: 1.0.0
 * Author: IDS Company
 * Author URI: https://ids.com.lb
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: omsar-social-share
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Define plugin constants
define('OMSAR_SOCIAL_SHARE_VERSION', '1.0.0');
define('OMSAR_SOCIAL_SHARE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('OMSAR_SOCIAL_SHARE_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main Plugin Class
 */
class OMSAR_Social_Share {
    
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * Get instance of this class
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Admin hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        
        // Frontend hooks
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('wp_footer', array($this, 'add_inline_scripts'));
        
        // Template hooks
        add_filter('omsar_page_banner_row_content', array($this, 'add_share_button_to_banner_row'), 10, 1);
        add_action('omsar_after_page_banner', array($this, 'render_share_modal'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('Social Share Settings', 'omsar-social-share'),
            __('Social Share', 'omsar-social-share'),
            'manage_options',
            'omsar-social-share',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Register plugin settings
     */
    public function register_settings() {
        // Register settings
        register_setting('omsar_social_share_settings', 'omsar_social_share_enable_facebook');
        register_setting('omsar_social_share_settings', 'omsar_social_share_enable_twitter');
        register_setting('omsar_social_share_settings', 'omsar_social_share_enable_linkedin');
        register_setting('omsar_social_share_settings', 'omsar_social_share_enable_whatsapp');
        register_setting('omsar_social_share_settings', 'omsar_social_share_enable_copy_link');
        register_setting('omsar_social_share_settings', 'omsar_social_share_post_types');
        register_setting('omsar_social_share_settings', 'omsar_social_share_position');
        
        // Style settings
        register_setting('omsar_social_share_settings', 'omsar_social_share_btn_bg_color');
        register_setting('omsar_social_share_settings', 'omsar_social_share_btn_text_color');
        register_setting('omsar_social_share_settings', 'omsar_social_share_btn_border_radius');
        register_setting('omsar_social_share_settings', 'omsar_social_share_btn_padding');
        register_setting('omsar_social_share_settings', 'omsar_social_share_btn_hover_bg_color');
        register_setting('omsar_social_share_settings', 'omsar_social_share_btn_font_size');
        register_setting('omsar_social_share_settings', 'omsar_social_share_btn_border_width');
        register_setting('omsar_social_share_settings', 'omsar_social_share_btn_border_color');
        register_setting('omsar_social_share_settings', 'omsar_social_share_btn_border_style');
        register_setting('omsar_social_share_settings', 'omsar_social_share_btn_hover_border_color');
        
        // Add settings section
        add_settings_section(
            'omsar_social_share_main_section',
            __('Social Media Options', 'omsar-social-share'),
            array($this, 'render_section_description'),
            'omsar-social-share'
        );
        
        // Add settings fields
        add_settings_field(
            'omsar_social_share_enable_facebook',
            __('Enable Facebook', 'omsar-social-share'),
            array($this, 'render_checkbox_field'),
            'omsar-social-share',
            'omsar_social_share_main_section',
            array('option' => 'omsar_social_share_enable_facebook', 'label' => __('Enable Facebook sharing', 'omsar-social-share'))
        );
        
        add_settings_field(
            'omsar_social_share_enable_twitter',
            __('Enable Twitter', 'omsar-social-share'),
            array($this, 'render_checkbox_field'),
            'omsar-social-share',
            'omsar_social_share_main_section',
            array('option' => 'omsar_social_share_enable_twitter', 'label' => __('Enable Twitter sharing', 'omsar-social-share'))
        );
        
        add_settings_field(
            'omsar_social_share_enable_linkedin',
            __('Enable LinkedIn', 'omsar-social-share'),
            array($this, 'render_checkbox_field'),
            'omsar-social-share',
            'omsar_social_share_main_section',
            array('option' => 'omsar_social_share_enable_linkedin', 'label' => __('Enable LinkedIn sharing', 'omsar-social-share'))
        );
        
        add_settings_field(
            'omsar_social_share_enable_whatsapp',
            __('Enable WhatsApp', 'omsar-social-share'),
            array($this, 'render_checkbox_field'),
            'omsar-social-share',
            'omsar_social_share_main_section',
            array('option' => 'omsar_social_share_enable_whatsapp', 'label' => __('Enable WhatsApp sharing', 'omsar-social-share'))
        );
        
        add_settings_field(
            'omsar_social_share_enable_copy_link',
            __('Enable Copy Link', 'omsar-social-share'),
            array($this, 'render_checkbox_field'),
            'omsar-social-share',
            'omsar_social_share_main_section',
            array('option' => 'omsar_social_share_enable_copy_link', 'label' => __('Enable copy link functionality', 'omsar-social-share'))
        );
        
        add_settings_field(
            'omsar_social_share_post_types',
            __('Post Types', 'omsar-social-share'),
            array($this, 'render_post_types_field'),
            'omsar-social-share',
            'omsar_social_share_main_section'
        );
        
        add_settings_field(
            'omsar_social_share_position',
            __('Button Position', 'omsar-social-share'),
            array($this, 'render_position_field'),
            'omsar-social-share',
            'omsar_social_share_main_section'
        );
        
        // Add style section
        add_settings_section(
            'omsar_social_share_style_section',
            __('Button Style Settings', 'omsar-social-share'),
            array($this, 'render_style_section_description'),
            'omsar-social-share'
        );
        
        add_settings_field(
            'omsar_social_share_btn_bg_color',
            __('Background Color', 'omsar-social-share'),
            array($this, 'render_color_field'),
            'omsar-social-share',
            'omsar_social_share_style_section',
            array('option' => 'omsar_social_share_btn_bg_color', 'default' => '#192D50', 'description' => __('Button background color', 'omsar-social-share'))
        );
        
        add_settings_field(
            'omsar_social_share_btn_text_color',
            __('Text Color', 'omsar-social-share'),
            array($this, 'render_color_field'),
            'omsar-social-share',
            'omsar_social_share_style_section',
            array('option' => 'omsar_social_share_btn_text_color', 'default' => '#ffffff', 'description' => __('Button text color', 'omsar-social-share'))
        );
        
        add_settings_field(
            'omsar_social_share_btn_hover_bg_color',
            __('Hover Background Color', 'omsar-social-share'),
            array($this, 'render_color_field'),
            'omsar-social-share',
            'omsar_social_share_style_section',
            array('option' => 'omsar_social_share_btn_hover_bg_color', 'default' => '#23375a', 'description' => __('Button background color on hover', 'omsar-social-share'))
        );
        
        add_settings_field(
            'omsar_social_share_btn_border_radius',
            __('Border Radius', 'omsar-social-share'),
            array($this, 'render_text_field'),
            'omsar-social-share',
            'omsar_social_share_style_section',
            array('option' => 'omsar_social_share_btn_border_radius', 'default' => '8px', 'description' => __('Button border radius (e.g., 8px, 12px, 50% for round)', 'omsar-social-share'))
        );
        
        add_settings_field(
            'omsar_social_share_btn_padding',
            __('Padding', 'omsar-social-share'),
            array($this, 'render_text_field'),
            'omsar-social-share',
            'omsar_social_share_style_section',
            array('option' => 'omsar_social_share_btn_padding', 'default' => '10px 20px', 'description' => __('Button padding (e.g., 10px 20px)', 'omsar-social-share'))
        );
        
        add_settings_field(
            'omsar_social_share_btn_font_size',
            __('Font Size', 'omsar-social-share'),
            array($this, 'render_text_field'),
            'omsar-social-share',
            'omsar_social_share_style_section',
            array('option' => 'omsar_social_share_btn_font_size', 'default' => '14px', 'description' => __('Button font size (e.g., 14px, 16px)', 'omsar-social-share'))
        );
        
        add_settings_field(
            'omsar_social_share_btn_border_width',
            __('Border Width', 'omsar-social-share'),
            array($this, 'render_text_field'),
            'omsar-social-share',
            'omsar_social_share_style_section',
            array('option' => 'omsar_social_share_btn_border_width', 'default' => '0px', 'description' => __('Button border width (e.g., 0px, 1px, 2px). Set to 0px to remove border.', 'omsar-social-share'))
        );
        
        add_settings_field(
            'omsar_social_share_btn_border_color',
            __('Border Color', 'omsar-social-share'),
            array($this, 'render_color_field'),
            'omsar-social-share',
            'omsar_social_share_style_section',
            array('option' => 'omsar_social_share_btn_border_color', 'default' => '#e0e0e0', 'description' => __('Button border color', 'omsar-social-share'))
        );
        
        add_settings_field(
            'omsar_social_share_btn_border_style',
            __('Border Style', 'omsar-social-share'),
            array($this, 'render_border_style_field'),
            'omsar-social-share',
            'omsar_social_share_style_section'
        );
        
        add_settings_field(
            'omsar_social_share_btn_hover_border_color',
            __('Hover Border Color', 'omsar-social-share'),
            array($this, 'render_color_field'),
            'omsar-social-share',
            'omsar_social_share_style_section',
            array('option' => 'omsar_social_share_btn_hover_border_color', 'default' => '#d0d0d0', 'description' => __('Button border color on hover', 'omsar-social-share'))
        );
    }
    
    /**
     * Render section description
     */
    public function render_section_description() {
        echo '<p>' . __('Enable or disable social media sharing options. Unchecked options will not appear in the share modal.', 'omsar-social-share') . '</p>';
    }
    
    /**
     * Render style section description
     */
    public function render_style_section_description() {
        echo '<p>' . __('Customize the appearance of the share button. Leave fields empty to use default values.', 'omsar-social-share') . '</p>';
    }
    
    /**
     * Render checkbox field
     */
    public function render_checkbox_field($args) {
        $option = get_option($args['option'], '1');
        $checked = checked('1', $option, false);
        echo '<label><input type="checkbox" name="' . esc_attr($args['option']) . '" value="1" ' . $checked . '> ' . esc_html($args['label']) . '</label>';
    }
    
    /**
     * Render post types field
     */
    public function render_post_types_field() {
        $selected_types = get_option('omsar_social_share_post_types', array('projects'));
        $post_types = get_post_types(array('public' => true), 'objects');
        
        echo '<div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fff;">';
        foreach ($post_types as $post_type) {
            $checked = in_array($post_type->name, $selected_types) ? 'checked' : '';
            echo '<label style="display: block; margin-bottom: 8px;">';
            echo '<input type="checkbox" name="omsar_social_share_post_types[]" value="' . esc_attr($post_type->name) . '" ' . $checked . '> ';
            echo esc_html($post_type->label . ' (' . $post_type->name . ')');
            echo '</label>';
        }
        echo '</div>';
        echo '<p class="description">' . __('Select which post types should display the share button.', 'omsar-social-share') . '</p>';
    }
    
    /**
     * Render position field
     */
    public function render_position_field() {
        $position = get_option('omsar_social_share_position', 'banner');
        ?>
        <select name="omsar_social_share_position">
            <option value="banner" <?php selected($position, 'banner'); ?>><?php _e('Banner (Top Right)', 'omsar-social-share'); ?></option>
            <option value="content" <?php selected($position, 'content'); ?>><?php _e('After Content', 'omsar-social-share'); ?></option>
            <option value="both" <?php selected($position, 'both'); ?>><?php _e('Both', 'omsar-social-share'); ?></option>
        </select>
        <p class="description"><?php _e('Choose where to display the share button.', 'omsar-social-share'); ?></p>
        <?php
    }
    
    /**
     * Render color field
     */
    public function render_color_field($args) {
        $value = get_option($args['option'], $args['default']);
        $field_id = esc_attr($args['option']);
        ?>
        <div style="display: flex; align-items: center; gap: 10px;">
            <input type="color" id="<?php echo $field_id; ?>_color" value="<?php echo esc_attr($value); ?>" style="width: 80px; height: 35px;" onchange="document.getElementById('<?php echo $field_id; ?>_text').value = this.value;">
            <input type="text" id="<?php echo $field_id; ?>_text" name="<?php echo esc_attr($args['option']); ?>" value="<?php echo esc_attr($value); ?>" placeholder="<?php echo esc_attr($args['default']); ?>" style="width: 120px;" onchange="document.getElementById('<?php echo $field_id; ?>_color').value = this.value;">
        </div>
        <p class="description"><?php echo esc_html($args['description']); ?></p>
        <?php
    }
    
    /**
     * Render text field
     */
    public function render_text_field($args) {
        $value = get_option($args['option'], $args['default']);
        ?>
        <input type="text" name="<?php echo esc_attr($args['option']); ?>" value="<?php echo esc_attr($value); ?>" placeholder="<?php echo esc_attr($args['default']); ?>" style="width: 200px;">
        <p class="description"><?php echo esc_html($args['description']); ?></p>
        <?php
    }
    
    /**
     * Render border style field
     */
    public function render_border_style_field() {
        $value = get_option('omsar_social_share_btn_border_style', 'solid');
        $styles = array(
            'solid' => __('Solid', 'omsar-social-share'),
            'dashed' => __('Dashed', 'omsar-social-share'),
            'dotted' => __('Dotted', 'omsar-social-share'),
            'double' => __('Double', 'omsar-social-share'),
            'groove' => __('Groove', 'omsar-social-share'),
            'ridge' => __('Ridge', 'omsar-social-share'),
            'inset' => __('Inset', 'omsar-social-share'),
            'outset' => __('Outset', 'omsar-social-share'),
            'none' => __('None', 'omsar-social-share')
        );
        ?>
        <select name="omsar_social_share_btn_border_style" style="width: 200px;">
            <?php foreach ($styles as $style_value => $style_label) : ?>
                <option value="<?php echo esc_attr($style_value); ?>" <?php selected($value, $style_value); ?>><?php echo esc_html($style_label); ?></option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php _e('Button border style', 'omsar-social-share'); ?></p>
        <?php
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('omsar_social_share_settings');
                do_settings_sections('omsar-social-share');
                submit_button(__('Save Settings', 'omsar-social-share'));
                ?>
            </form>
            
            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2><?php _e('Plugin Information', 'omsar-social-share'); ?></h2>
                <p><strong><?php _e('Version:', 'omsar-social-share'); ?></strong> <?php echo OMSAR_SOCIAL_SHARE_VERSION; ?></p>
                <p><strong><?php _e('Developed by:', 'omsar-social-share'); ?></strong> <a href="https://ids.com.lb" target="_blank">IDS Company</a></p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Enqueue plugin assets
     */
    public function enqueue_assets() {
        $enabled_post_types = get_option('omsar_social_share_post_types', array('projects'));
        $current_post_type = get_post_type();
        
        if (!in_array($current_post_type, $enabled_post_types)) {
            return;
        }
        
        // Enqueue CSS
        wp_enqueue_style(
            'omsar-social-share-css',
            OMSAR_SOCIAL_SHARE_PLUGIN_URL . 'assets/css/social-share.css',
            array(),
            OMSAR_SOCIAL_SHARE_VERSION
        );
        
        // Add custom inline styles
        $this->add_custom_styles();
    }
    
    /**
     * Add custom inline styles based on settings
     */
    private function add_custom_styles() {
        $bg_color = get_option('omsar_social_share_btn_bg_color', '#192D50');
        $text_color = get_option('omsar_social_share_btn_text_color', '#ffffff');
        $hover_bg_color = get_option('omsar_social_share_btn_hover_bg_color', '#23375a');
        $border_radius = get_option('omsar_social_share_btn_border_radius', '8px');
        $padding = get_option('omsar_social_share_btn_padding', '10px 20px');
        $font_size = get_option('omsar_social_share_btn_font_size', '14px');
        $border_width = get_option('omsar_social_share_btn_border_width', '0px');
        $border_color = get_option('omsar_social_share_btn_border_color', '#e0e0e0');
        $border_style = get_option('omsar_social_share_btn_border_style', 'solid');
        $hover_border_color = get_option('omsar_social_share_btn_hover_border_color', '#d0d0d0');
        
        // Build custom CSS
        $custom_css = "
        .btn-share-banner {
            background-color: {$bg_color} !important;
            color: {$text_color} !important;
            border-radius: {$border_radius} !important;
            padding: {$padding} !important;
            font-size: {$font_size} !important;
            border-width: {$border_width} !important;
            border-style: {$border_style} !important;
            border-color: {$border_color} !important;
        }
        .btn-share-banner:hover {
            background-color: {$hover_bg_color} !important;
            color: {$text_color} !important;
            border-color: {$hover_border_color} !important;
        }
        ";
        
        wp_add_inline_style('omsar-social-share-css', $custom_css);
    }
    
    /**
     * Add inline scripts
     */
    public function add_inline_scripts() {
        $enabled_post_types = get_option('omsar_social_share_post_types', array('projects'));
        $current_post_type = get_post_type();
        
        if (!in_array($current_post_type, $enabled_post_types)) {
            return;
        }
        
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Copy link functionality
            $(document).on('click', '.omsar-social-share-copy', function(e) {
                e.preventDefault();
                var url = $(this).data('url');
                var $button = $(this);
                var $copyText = $button.find('.copy-text');
                var $copiedText = $button.find('.copied-text');
                
                // Copy to clipboard
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(function() {
                        $button.addClass('copied');
                        $copyText.hide();
                        $copiedText.show();
                        
                        setTimeout(function() {
                            $button.removeClass('copied');
                            $copyText.show();
                            $copiedText.hide();
                        }, 2000);
                    }).catch(function(err) {
                        fallbackCopyTextToClipboard(url, $button, $copyText, $copiedText);
                    });
                } else {
                    fallbackCopyTextToClipboard(url, $button, $copyText, $copiedText);
                }
            });
            
            function fallbackCopyTextToClipboard(text, $button, $copyText, $copiedText) {
                var textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                
                try {
                    var successful = document.execCommand('copy');
                    if (successful) {
                        $button.addClass('copied');
                        $copyText.hide();
                        $copiedText.show();
                        
                        setTimeout(function() {
                            $button.removeClass('copied');
                            $copyText.show();
                            $copiedText.hide();
                        }, 2000);
                    }
                } catch (err) {
                    console.error('Fallback: Oops, unable to copy', err);
                }
                
                document.body.removeChild(textArea);
            }
        });
        </script>
        <?php
    }
    
    /**
     * Add share button to banner row
     */
    public function add_share_button_to_banner_row($content) {
        $enabled_post_types = get_option('omsar_social_share_post_types', array('projects'));
        $current_post_type = get_post_type();
        $position = get_option('omsar_social_share_position', 'banner');
        
        if (!in_array($current_post_type, $enabled_post_types)) {
            return $content;
        }
        
        if ($position === 'banner' || $position === 'both') {
            $share_text = function_exists('pll__') ? pll__('Share') : __('Share', 'omsar-social-share');
            $content .= '<div class="col-6 d-flex justify-content-end align-items-start">';
            $content .= '<button type="button" class="btn-share-banner" data-bs-toggle="modal" data-bs-target="#omsarSocialShareModal" aria-label="' . esc_attr($share_text) . '">';
            $content .= '<i class="bi bi-share"></i>';
            $content .= '<span>' . esc_html($share_text) . '</span>';
            $content .= '</button>';
            $content .= '</div>';
        }
        
        return $content;
    }
    
    /**
     * Render share modal
     */
    public function render_share_modal() {
        $enabled_post_types = get_option('omsar_social_share_post_types', array('projects'));
        $current_post_type = get_post_type();
        
        if (!in_array($current_post_type, $enabled_post_types)) {
            return;
        }
        
        $this->render_share_modal_content();
    }
    
    /**
     * Render share modal content
     */
    private function render_share_modal_content() {
        global $post;
        if (!$post) {
            return;
        }
        
        $post_id = $post->ID;
        $post_title = get_the_title();
        $post_url = get_permalink();
        $post_excerpt = get_the_excerpt();
        
        if (empty($post_excerpt)) {
            $post_excerpt = wp_trim_words(get_the_content(), 20);
        }
        
        // Encode URLs
        $encoded_url = urlencode($post_url);
        $encoded_title = urlencode($post_title);
        $encoded_excerpt = urlencode($post_excerpt);
        
        // Get enabled options
        $enable_facebook = get_option('omsar_social_share_enable_facebook', '1');
        $enable_twitter = get_option('omsar_social_share_enable_twitter', '1');
        $enable_linkedin = get_option('omsar_social_share_enable_linkedin', '1');
        $enable_whatsapp = get_option('omsar_social_share_enable_whatsapp', '1');
        $enable_copy_link = get_option('omsar_social_share_enable_copy_link', '1');
        
        // Social URLs
        $facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url;
        $twitter_url = 'https://twitter.com/intent/tweet?url=' . $encoded_url . '&text=' . $encoded_title;
        $linkedin_url = 'https://www.linkedin.com/sharing/share-offsite/?url=' . $encoded_url;
        $whatsapp_url = 'https://wa.me/?text=' . $encoded_title . ' ' . $encoded_url;
        
        // Translations
        $share_text = function_exists('pll__') ? pll__('Share') : __('Share', 'omsar-social-share');
        $share_this_text = function_exists('pll__') ? pll__('Share this') : __('Share this', 'omsar-social-share');
        $copied_text = function_exists('pll__') ? pll__('Copied!') : __('Copied!', 'omsar-social-share');
        $copy_link_text = function_exists('pll__') ? pll__('Copy Link') : __('Copy Link', 'omsar-social-share');
        ?>
        <!-- Social Share Modal -->
        <div class="modal fade" id="omsarSocialShareModal" tabindex="-1" aria-labelledby="omsarSocialShareModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="omsarSocialShareModalLabel">
                            <i class="bi bi-share me-2"></i>
                            <?php echo esc_html($share_text); ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="share-description mb-4"><?php echo esc_html($share_this_text . ': ' . $post_title); ?></p>
                        
                        <div class="social-share-buttons-modal">
                            <?php if ($enable_facebook === '1') : ?>
                            <a href="<?php echo esc_url($facebook_url); ?>" 
                               class="social-share-btn-modal social-share-facebook" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               aria-label="Share on Facebook"
                               title="Share on Facebook">
                                <i class="bi bi-facebook"></i>
                                <span>Facebook</span>
                            </a>
                            <?php endif; ?>
                            
                            <?php if ($enable_twitter === '1') : ?>
                            <a href="<?php echo esc_url($twitter_url); ?>" 
                               class="social-share-btn-modal social-share-twitter" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               aria-label="Share on Twitter"
                               title="Share on Twitter">
                                <i class="bi bi-twitter"></i>
                                <span>Twitter</span>
                            </a>
                            <?php endif; ?>
                            
                            <?php if ($enable_linkedin === '1') : ?>
                            <a href="<?php echo esc_url($linkedin_url); ?>" 
                               class="social-share-btn-modal social-share-linkedin" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               aria-label="Share on LinkedIn"
                               title="Share on LinkedIn">
                                <i class="bi bi-linkedin"></i>
                                <span>LinkedIn</span>
                            </a>
                            <?php endif; ?>
                            
                            <?php if ($enable_whatsapp === '1') : ?>
                            <a href="<?php echo esc_url($whatsapp_url); ?>" 
                               class="social-share-btn-modal social-share-whatsapp" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               aria-label="Share on WhatsApp"
                               title="Share on WhatsApp">
                                <i class="bi bi-whatsapp"></i>
                                <span>WhatsApp</span>
                            </a>
                            <?php endif; ?>
                            
                            <?php if ($enable_copy_link === '1') : ?>
                            <button type="button" 
                                    class="social-share-btn-modal omsar-social-share-copy" 
                                    data-url="<?php echo esc_attr($post_url); ?>"
                                    aria-label="Copy link"
                                    title="Copy link">
                                <i class="bi bi-link-45deg"></i>
                                <span class="copy-text"><?php echo esc_html($copy_link_text); ?></span>
                                <span class="copied-text" style="display: none;"><?php echo esc_html($copied_text); ?></span>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

// Initialize plugin
function omsar_social_share_init() {
    return OMSAR_Social_Share::get_instance();
}

// Start the plugin
omsar_social_share_init();

