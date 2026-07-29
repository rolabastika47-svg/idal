<?php
/**
 * Template Part: Social Share Buttons
 * 
 * Displays social media sharing buttons for posts
 * 
 * @package OMSAR
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Get current post data
global $post;
$post_id = get_the_ID();
$post_title = get_the_title();
$post_url = get_permalink();
$post_excerpt = get_the_excerpt();
$post_image = get_the_post_thumbnail_url($post_id, 'large');

// If no excerpt, use a default description
if (empty($post_excerpt)) {
    $post_excerpt = wp_trim_words(get_the_content(), 20);
}

// Encode URLs for sharing
$encoded_url = urlencode($post_url);
$encoded_title = urlencode($post_title);
$encoded_excerpt = urlencode($post_excerpt);
$encoded_image = urlencode($post_image);

// Social sharing URLs
$facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url;
$twitter_url = 'https://twitter.com/intent/tweet?url=' . $encoded_url . '&text=' . $encoded_title;
$linkedin_url = 'https://www.linkedin.com/sharing/share-offsite/?url=' . $encoded_url;
$whatsapp_url = 'https://wa.me/?text=' . $encoded_title . ' ' . $encoded_url;
$email_url = 'mailto:?subject=' . $encoded_title . '&body=' . $encoded_excerpt . '%20' . $encoded_url;

// Get share text from translations
$share_text = function_exists('pll__') ? pll__('Share') : __('Share', 'omsar');
?>

<div class="social-share-wrapper">
    <div class="social-share-label">
        <span><?php echo esc_html($share_text); ?>:</span>
    </div>
    <div class="social-share-buttons">
        <a href="<?php echo esc_url($facebook_url); ?>" 
           class="social-share-btn social-share-facebook" 
           target="_blank" 
           rel="noopener noreferrer"
           aria-label="Share on Facebook"
           title="Share on Facebook">
            <i class="bi bi-facebook"></i>
            <span>Facebook</span>
        </a>
        
        <a href="<?php echo esc_url($twitter_url); ?>" 
           class="social-share-btn social-share-twitter" 
           target="_blank" 
           rel="noopener noreferrer"
           aria-label="Share on Twitter"
           title="Share on Twitter">
            <i class="bi bi-twitter"></i>
            <span>Twitter</span>
        </a>
        
        <a href="<?php echo esc_url($linkedin_url); ?>" 
           class="social-share-btn social-share-linkedin" 
           target="_blank" 
           rel="noopener noreferrer"
           aria-label="Share on LinkedIn"
           title="Share on LinkedIn">
            <i class="bi bi-linkedin"></i>
            <span>LinkedIn</span>
        </a>
        
        <a href="<?php echo esc_url($whatsapp_url); ?>" 
           class="social-share-btn social-share-whatsapp" 
           target="_blank" 
           rel="noopener noreferrer"
           aria-label="Share on WhatsApp"
           title="Share on WhatsApp">
            <i class="bi bi-whatsapp"></i>
            <span>WhatsApp</span>
        </a>
        
        <button type="button" 
                class="social-share-btn social-share-copy" 
                data-url="<?php echo esc_attr($post_url); ?>"
                aria-label="Copy link"
                title="Copy link">
            <i class="bi bi-link-45deg"></i>
            <span class="copy-text">Copy Link</span>
            <span class="copied-text" style="display: none;"><?php echo function_exists('pll__') ? pll__('Copied!') : __('Copied!', 'omsar'); ?></span>
        </button>
    </div>
</div>

