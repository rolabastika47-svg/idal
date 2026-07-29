<?php
/*
Template Name: Single Minister Page
*/

get_header();

// Display page banner with minister name
// Get the minister ID from ACF field (you can use 'minister' for post object or 'minister_id' for number field)
$minister_id = get_field('minister'); // If using post object field
if (!$minister_id) {
    $minister_id = get_field('minister_id'); // If using number/text field
}

// If minister_id is a post object, get the ID
if (is_object($minister_id) || is_array($minister_id)) {
    $minister_id = is_array($minister_id) ? $minister_id['ID'] : $minister_id->ID;
}

// If still no ID, check URL parameter as fallback
if (!$minister_id && isset($_GET['minister_id'])) {
    $minister_id = intval($_GET['minister_id']);
}

// Validate minister ID and post type
if (!$minister_id || get_post_type($minister_id) !== 'former_ministers') {
    // Show error message or redirect
    echo '<div class="container py-5"><div class="alert alert-warning">';
    echo __('No minister selected or invalid minister ID.', 'omsar');
    echo '</div></div>';
    get_footer();
    exit;
}

// Get the minister post
$minister_post = get_post($minister_id);
if (!$minister_post) {
    echo '<div class="container py-5"><div class="alert alert-warning">';
    echo __('Minister not found.', 'omsar');
    echo '</div></div>';
    get_footer();
    exit;
}

// Setup post data
global $post;
$post = $minister_post;
setup_postdata($post);

// Get current language
$current_lang = function_exists('pll_current_language') ? pll_current_language() : (is_rtl() ? 'ar' : 'en');

// Get ACF fields from the minister post
$minister_title = get_the_title($minister_id);
// Get government field based on current language
if ($current_lang === 'ar') {
    $government = get_field('government_ar', $minister_id);
    // Fallback to English if Arabic version doesn't exist
    if (!$government) {
        $government = get_field('government', $minister_id);
    }
} else {
    $government = get_field('government', $minister_id);
    // Fallback to Arabic if English version doesn't exist
    if (!$government) {
        $government = get_field('government_ar', $minister_id);
    }
}
$assignment_period = get_field('assignment_period', $minister_id);
$end_assignment_period = get_field('end_assignment_period', $minister_id);
// Get description field based on current language
if ($current_lang === 'ar') {
    $description = get_field('description_arabic', $minister_id);
    // Fallback to English if Arabic version doesn't exist
    if (!$description) {
        $description = get_field('description', $minister_id);
    }
} else {
    $description = get_field('description', $minister_id);
    // Fallback to Arabic if English version doesn't exist
    if (!$description) {
        $description = get_field('description_arabic', $minister_id);
    }
}

// Get featured image
$default_image = get_template_directory_uri() . '/assets/images/default-image.png';
$minister_image = get_the_post_thumbnail_url($minister_id, 'full');
// if (!$minister_image) {
//     $minister_image = $default_image;
// }

// Format dates (d M Y format)
$assignment_period_formatted = '';
$end_assignment_period_formatted = '';
$period_display = '';

if ($assignment_period) {
    // ACF date picker returns date in YYYYMMDD format or as string
    if (is_numeric($assignment_period) && strlen($assignment_period) == 8) {
        // Format: YYYYMMDD
        $date_obj = DateTime::createFromFormat('Ymd', $assignment_period);
        if ($date_obj) {
            $assignment_period_formatted = format_date_with_arabic_months($date_obj, $current_lang);
        }
    } else {
        // Try to parse as string
        $date_obj = DateTime::createFromFormat('Y-m-d', $assignment_period);
        if (!$date_obj) {
            $date_obj = DateTime::createFromFormat('d/m/Y', $assignment_period);
        }
        if ($date_obj) {
            $assignment_period_formatted = format_date_with_arabic_months($date_obj, $current_lang);
        } else {
            $assignment_period_formatted = $assignment_period;
        }
    }
}

if ($end_assignment_period) {
    if (is_numeric($end_assignment_period) && strlen($end_assignment_period) == 8) {
        $date_obj = DateTime::createFromFormat('Ymd', $end_assignment_period);
        if ($date_obj) {
            $end_assignment_period_formatted = format_date_with_arabic_months($date_obj, $current_lang);
        }
    } else {
        $date_obj = DateTime::createFromFormat('Y-m-d', $end_assignment_period);
        if (!$date_obj) {
            $date_obj = DateTime::createFromFormat('d/m/Y', $end_assignment_period);
        }
        if ($date_obj) {
            $end_assignment_period_formatted = format_date_with_arabic_months($date_obj, $current_lang);
        } else {
            $end_assignment_period_formatted = $end_assignment_period;
        }
    }
}

// Combine period dates for display
if ($assignment_period_formatted && $end_assignment_period_formatted) {
    $period_display = $assignment_period_formatted . ' - ' . $end_assignment_period_formatted;
} elseif ($assignment_period_formatted) {
    $period_display = $assignment_period_formatted;
}
?>

<section class="minister-profile-section">
    <div class="container">
        <div class="minister-profile-wrapper">
            <div class="row g-3 align-items-start">
                
                <!-- Left Content Column -->
                <div class="col-lg-7 order-2 order-lg-1">
                    <div class="minister-content">
                        <!-- Government & Assignment Period -->
                        <?php if ($government || $period_display) : ?>
                        <div class="minister-meta mb-4">
                            <?php if ($government) : ?>
                            <div class="meta-item">
                                <span class="meta-label"><?php echo function_exists('pll__') ? pll__('Government') : __('Government', 'omsar'); ?>:</span>
                                <span class="meta-value"><?php echo esc_html($government); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($period_display) : ?>
                            <div class="meta-item">
                                <span class="meta-label"><?php echo function_exists('pll__') ? pll__('Assignment Period') : __('Assignment Period', 'omsar'); ?>:</span>
                                <span class="meta-value"><?php echo esc_html($period_display); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Description/Content -->
                        <?php if ($description || get_post_field('post_content', $minister_id)) : ?>
                        <div class="content-section">
                            <?php if ($description) : ?>
                                <div class="minister-description">
                                    <?php echo wp_kses_post($description); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php 
                            $minister_content = get_post_field('post_content', $minister_id);
                            if ($minister_content) : 
                            ?>
                                <div class="minister-content-text">
                                    <?php echo apply_filters('the_content', $minister_content); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Image Column -->
                <?php if ( ! empty( $minister_image ) ) : ?>
                <div class="col-lg-5 order-1 order-lg-2">
                    <div class="minister-image-wrapper">
                        <img src="<?php echo esc_url( $minister_image ); ?>" alt="<?php echo esc_attr( $minister_title ); ?>" class="minister-profile-img">
                    </div>
                </div>
            <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php
// Reset post data
wp_reset_postdata();

get_footer();
?>
