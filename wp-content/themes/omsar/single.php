<?php
/**
 * The template for displaying all single posts
 *
 * This is the template that displays single posts
 *
 * @package OMSAR
 */

get_header();

// Start the loop
while (have_posts()) :
    the_post();
?>

<div id="primary" class="content-area">
    <div class="container pt-4 static-height">
        <div class="row g-0 g-md-1 g-lg-2">
            <main id="main" class="site-main col-12">
                
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                    <?php
                    // Check if this post is built with Elementor
                    $is_elementor_page = false;
                    $post_id = get_the_ID();
                    $post_sc_description = function_exists('get_field')
                        ? get_field('post_sc_description', $post_id)
                        : '';
                    if (!is_string($post_sc_description) || trim($post_sc_description) === '') {
                        $post_sc_description = get_post_meta($post_id, 'post_sc_description', true);
                    }
                    $post_sc_description = is_string($post_sc_description) ? trim($post_sc_description) : '';
                    if ($post_id && did_action('elementor/loaded') && class_exists('\Elementor\Plugin')) {
                        if (isset(\Elementor\Plugin::$instance) && isset(\Elementor\Plugin::$instance->db)) {
                            try {
                                $is_elementor = \Elementor\Plugin::$instance->db->is_built_with_elementor($post_id);
                                if ($is_elementor === true) {
                                    $is_elementor_page = true;
                                }
                            } catch (Exception $e) {
                                $is_elementor_page = false;
                            } catch (Error $e) {
                                $is_elementor_page = false;
                            }
                        }
                    }
                    ?>
                    
                    <?php if (!$is_elementor_page && has_post_thumbnail()) : ?>
                        <!-- Thumbnail and Content Side by Side Layout -->
                        <div class="row g-3 g-md-4 mb-4">
                            <!-- Thumbnail Column (50%) -->
                            <div class="col-12 col-md-6">
                                <div class="post-publish-date mb-3">
                                    <i class="bi bi-calendar3"></i>
                                    <time datetime="<?php 
                                        $custom_date = get_post_meta(get_the_ID(), 'custom_date', true);
                                        $post_type_option = get_post_meta(get_the_ID(), 'post_type_option', true);

                                        if (!empty($custom_date) && in_array($post_type_option, ['events', 'workshops'])) {
                                            echo esc_attr(date('c', strtotime($custom_date)));
                                        } else {
                                            echo esc_attr(get_the_date('c'));
                                        }
                                    ?>">
                                        <?php 
                                        if (!empty($custom_date) && in_array($post_type_option, ['events', 'workshops'])) {
                                            echo esc_html(date('d-m-y', strtotime($custom_date)));
                                        } else {
                                            echo esc_html(get_the_date('d-m-y'));
                                        }
                                        ?>
                                    </time>
                                </div>
                                <div class="post-thumbnail">
                                    <?php the_post_thumbnail('large', array('class' => 'img-fluid w-100')); ?>
                                </div>
                            </div>
                            
                            <!-- Content Column (50%) -->
                            <div class="col-12 col-md-6">

                                <?php
                                // Only display badges if NOT using Elementor
                                // Get ACF taxonomy field values
                                $pillar_terms = omsar_get_acf_taxonomy_terms('pillar_option');
                                $status_terms = omsar_get_acf_taxonomy_terms('project_status_field');
                                
                                // Display badges if any terms exist
                                if (!empty($pillar_terms) || !empty($status_terms)) : ?>
                                    <div class="single-post-badges mb-4">
                                        <?php if (!empty($pillar_terms)) : ?>
                                            <?php foreach ($pillar_terms as $term) : ?>
                                                <span class="badge bg-primary me-2 mb-2"><?php echo esc_html($term->name); ?></span>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($status_terms)) : ?>
                                            <?php foreach ($status_terms as $term) : ?>
                                                <span class="badge bg-secondary me-2 mb-2"><?php echo esc_html($term->name); ?></span>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="post-custom-fields mb-4">
                                    <?php
                                    // Get custom field values
                                    $cost = get_post_meta(get_the_ID(), 'cost', true);
                                    $source_of_fund = get_post_meta(get_the_ID(), 'source_of_fund', true);
                                    $scope = get_post_meta(get_the_ID(), 'scope', true);
                                    $specific_objectives = get_post_meta(get_the_ID(), 'specific_objectives', true);
                                    $results_to_be_achieved = get_post_meta(get_the_ID(), 'results_to_be_achieved', true);
                                    $sector = get_post_meta(get_the_ID(), 'sector', true);
                                    $executing_agency = get_post_meta(get_the_ID(), 'executing_agency', true);
                                    $regions = get_post_meta(get_the_ID(), 'regions', true);
                                    $directorate = get_post_meta(get_the_ID(), 'directorate', true);

                                    // Display each field if it has a value
                                    if (!empty($cost)) :
                                        echo '<p><strong>'; pll_e('Cost'); echo ':</strong> ' . esc_html($cost) . '</p>';
                                    endif;

                                    if (!empty($source_of_fund)) :
                                        echo '<p><strong>'; pll_e('Source of Fund'); echo ':</strong> ' . $source_of_fund . '</p>';
                                    endif;

                                    if (!empty($scope)) :
                                        echo '<p><strong>'; pll_e('Scope'); echo ':</strong> ' .$scope . '</p>';
                                    endif;

                                    if (!empty($specific_objectives)) :
                                        echo '<p><strong>'; pll_e('Specific Objectives'); echo ':</strong> ' . $specific_objectives . '</p>';
                                    endif;

                                    if (!empty($results_to_be_achieved)) :
                                        echo '<p><strong>'; pll_e('Results to be Achieved'); echo ':</strong> ' . $results_to_be_achieved . '</p>';
                                    endif;

                                    if (!empty($sector)) :
                                        echo '<p><strong>'; pll_e('Sector'); echo ':</strong> ' . $sector . '</p>';
                                    endif;

                                    if (!empty($executing_agency)) :
                                        echo '<p><strong>'; pll_e('Executing Agency'); echo ':</strong> ' . $executing_agency . '</p>';
                                    endif;

                                    if (!empty($regions)) :
                                        echo '<p><strong>'; pll_e('Regions'); echo ':</strong> ' . $regions . '</p>';
                                    endif;

                                    if (!empty($directorate)) :
                                        echo '<p><strong>'; pll_e('Directorate'); echo ':</strong> ' . $directorate . '</p>';
                                    endif;
                                    ?>
                                </div>

                                <div class="entry-content">
                                    <?php
                                    the_content();

                                    wp_link_pages(array(
                                        'before' => '<div class="page-links">' . esc_html__('Pages:', 'omsar'),
                                        'after'  => '</div>',
                                    ));

                                    // Check for custom field 'project_attachment_file'
                                    $attachment_id = get_post_meta(get_the_ID(), 'project_attachment_file', true);

                                    if ($attachment_id) {
                                        // Get full file path
                                        $file_path = get_attached_file($attachment_id);
                                        // Extract just the filename
                                        $file_name = basename($file_path);

                                        echo '<div class="project-attachment">';
                                        echo  esc_html($file_name);
                                        echo '</div>';
                                    }
                                    ?>
                                </div>

                                <?php
                                // Documents repeater (ACF): 'documents' with sub-field 'pdf' (file ID)
                                $documents = get_field('documents');
                                if (!empty($documents) && is_array($documents)) : ?>
                                    <div class="single-post-documents entry-documents mt-4">
                                        <ul class="documents-list list-unstyled">
                                            <?php foreach ($documents as $row) :
                                                $pdf_id = isset($row['pdf']) ? $row['pdf'] : 0;
                                                if (empty($pdf_id)) continue;
                                                $url = wp_get_attachment_url($pdf_id);
                                                if (empty($url)) continue;
                                                $filename = basename(get_attached_file($pdf_id));
                                                if (empty($filename)) $filename = get_the_title($pdf_id) ?: __('Document', 'omsar');
                                                ?>
                                                <li class="document-item mb-2">
                                                    <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" class="document-link">
                                                        <i class="bi bi-file-earmark-pdf" aria-hidden="true"></i> <?php echo esc_html($filename); ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>


                                 <?php
                                // Documents repeater (ACF): 'documents' with sub-field 'pdf' (file ID)
                                $documents = get_field('resources_repeater');
                                if (!empty($documents) && is_array($documents)) : ?>
                                    <div class="single-post-documents entry-documents mt-4">
                                        <ul class="documents-list list-unstyled">
                                            <?php foreach ($documents as $row) :
                                                $pdf_id = isset($row['document_resource']) ? $row['document_resource'] : 0;
                                                if (empty($pdf_id)) continue;
                                                $url = wp_get_attachment_url($pdf_id);
                                                if (empty($url)) continue;
                                                $filename = basename(get_attached_file($pdf_id));
                                                if (empty($filename)) $filename = get_the_title($pdf_id) ?: __('Document', 'omsar');
                                                ?>
                                                <li class="document-item mb-2">
                                                    <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" class="document-link">
                                                        <i class="bi bi-file-earmark-pdf" aria-hidden="true"></i> <?php echo esc_html($filename); ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>


                            </div>
                        </div>

                        <?php if ($post_sc_description !== '') : ?>
                            <div class="post-sc-description entry-content mb-4">
                                <?php echo apply_filters('the_content', $post_sc_description); ?>
                            </div>
                        <?php endif; ?>
                    <?php elseif (!$is_elementor_page) : ?>
                        <!-- No Thumbnail - Content Only -->
                        <div class="post-publish-date mb-3">
                            <i class="bi bi-calendar3"></i>
                            <time datetime="<?php 
                                $custom_date = get_post_meta(get_the_ID(), 'custom_date', true);
                                $post_type_option = get_post_meta(get_the_ID(), 'post_type_option', true);

                                if (!empty($custom_date) && in_array($post_type_option, ['events', 'workshops'])) {
                                    echo esc_attr(date('c', strtotime($custom_date)));
                                } else {
                                    echo esc_attr(get_the_date('c'));
                                }
                            ?>">
                                <?php 
                                if (!empty($custom_date) && in_array($post_type_option, ['events', 'workshops'])) {
                                    echo esc_html(date('d-m-y', strtotime($custom_date)));
                                } else {
                                    echo esc_html(get_the_date('d-m-y'));
                                }
                                ?>
                            </time>
                        </div>

                        <?php
                        // Only display badges if NOT using Elementor
                        // Get ACF taxonomy field values
                        $pillar_terms = omsar_get_acf_taxonomy_terms('pillar_option');
                        $status_terms = omsar_get_acf_taxonomy_terms('project_status_field');
                        
                        // Display badges if any terms exist
                        if (!empty($pillar_terms) || !empty($status_terms)) : ?>
                            <div class="single-post-badges mb-4">
                                <?php if (!empty($pillar_terms)) : ?>
                                    <?php foreach ($pillar_terms as $term) : ?>
                                        <span class="badge bg-primary me-2 mb-2"><?php echo esc_html($term->name); ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                
                                <?php if (!empty($status_terms)) : ?>
                                    <?php foreach ($status_terms as $term) : ?>
                                        <span class="badge bg-secondary me-2 mb-2"><?php echo esc_html($term->name); ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="post-custom-fields mb-4">
                            <?php
                            // Get custom field values
                            $cost = get_post_meta(get_the_ID(), 'cost', true);
                            $source_of_fund = get_post_meta(get_the_ID(), 'source_of_fund', true);
                            $scope = get_post_meta(get_the_ID(), 'scope', true);
                            $specific_objectives = get_post_meta(get_the_ID(), 'specific_objectives', true);
                            $results_to_be_achieved = get_post_meta(get_the_ID(), 'results_to_be_achieved', true);
                            $sector = get_post_meta(get_the_ID(), 'sector', true);
                            $executing_agency = get_post_meta(get_the_ID(), 'executing_agency', true);
                            $regions = get_post_meta(get_the_ID(), 'regions', true);
                            $directorate = get_post_meta(get_the_ID(), 'directorate', true);

                            // Display each field if it has a value
                            if (!empty($cost)) :
                                echo '<p><strong>'; pll_e('Cost'); echo ':</strong> ' . esc_html($cost) . '</p>';
                            endif;

                            if (!empty($source_of_fund)) :
                                echo '<p><strong>'; pll_e('Source of Fund'); echo ':</strong> ' . $source_of_fund . '</p>';
                            endif;

                            if (!empty($scope)) :
                                echo '<p><strong>'; pll_e('Scope'); echo ':</strong> ' .$scope . '</p>';
                            endif;

                            if (!empty($specific_objectives)) :
                                echo '<p><strong>'; pll_e('Specific Objectives'); echo ':</strong> ' . $specific_objectives . '</p>';
                            endif;

                            if (!empty($results_to_be_achieved)) :
                                echo '<p><strong>'; pll_e('Results to be Achieved'); echo ':</strong> ' . $results_to_be_achieved . '</p>';
                            endif;

                            if (!empty($sector)) :
                                echo '<p><strong>'; pll_e('Sector'); echo ':</strong> ' . $sector . '</p>';
                            endif;

                            if (!empty($executing_agency)) :
                                echo '<p><strong>'; pll_e('Executing Agency'); echo ':</strong> ' . $executing_agency . '</p>';
                            endif;

                            if (!empty($regions)) :
                                echo '<p><strong>'; pll_e('Regions'); echo ':</strong> ' . $regions . '</p>';
                            endif;

                            if (!empty($directorate)) :
                                echo '<p><strong>'; pll_e('Directorate'); echo ':</strong> ' . $directorate . '</p>';
                            endif;
                            ?>
                        </div>

                        <div class="entry-content">
                            <?php
                            the_content();

                            wp_link_pages(array(
                                'before' => '<div class="page-links">' . esc_html__('Pages:', 'omsar'),
                                'after'  => '</div>',
                            ));

                            // Check for custom field 'project_attachment_file'
                            $attachment_id = get_post_meta(get_the_ID(), 'project_attachment_file', true);

                            if ($attachment_id) {
                                // Get full file path
                                $file_path = get_attached_file($attachment_id);
                                // Extract just the filename
                                $file_name = basename($file_path);

                                echo '<div class="project-attachment">';
                                echo  esc_html($file_name);
                                echo '</div>';
                            }
                            ?>
                        </div>

                        <?php if ($post_sc_description !== '') : ?>
                            <div class="post-sc-description entry-content mb-4">
                                <?php echo apply_filters('the_content', $post_sc_description); ?>
                            </div>
                        <?php endif; ?>
                    <?php else : ?>
                        <!-- Elementor Page - Content Only -->
                        <div class="entry-content">
                            <?php
                            the_content();

                            wp_link_pages(array(
                                'before' => '<div class="page-links">' . esc_html__('Pages:', 'omsar'),
                                'after'  => '</div>',
                            ));

                            // Check for custom field 'project_attachment_file'
                            $attachment_id = get_post_meta(get_the_ID(), 'project_attachment_file', true);

                            if ($attachment_id) {
                                // Get full file path
                                $file_path = get_attached_file($attachment_id);
                                // Extract just the filename
                                $file_name = basename($file_path);

                                echo '<div class="project-attachment">';
                                echo  esc_html($file_name);
                                echo '</div>';
                            }
                            ?>
                        </div>

                        <?php if ($post_sc_description !== '') : ?>
                            <div class="post-sc-description entry-content mb-4">
                                <?php echo apply_filters('the_content', $post_sc_description); ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>


                    <?php
                    // Display categories and tags as badges (only for non-Elementor pages)
                    if (!$is_elementor_page) :
                        // Get categories and tags
                        $categories = get_the_category();
                        $tags = get_the_tags();
                        
                        // Filter out "Uncategorized" and "Uncategorized-ar" categories
                        if (!empty($categories) && !is_wp_error($categories)) {
                            $categories = array_filter($categories, function($category) {
                                $slug = strtolower($category->slug);
                                return $slug !== 'uncategorized' && $slug !== 'uncategorized-ar';
                            });
                        }
                        
                        // Display badges if there are categories (after filtering) or tags
                        if ((!empty($categories) && !is_wp_error($categories)) || (!empty($tags) && !is_wp_error($tags))) : ?>
                            <div class="single-post-taxonomy-badges mb-4 mt-4">
                                <?php if (!empty($categories) && !is_wp_error($categories)) : ?>
                                    <?php foreach ($categories as $category) : ?>
                                        <span class="badge bg-primary me-2 mb-2"><?php echo esc_html($category->name); ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                
                                <?php if (!empty($tags) && !is_wp_error($tags)) : ?>
                                    <?php foreach ($tags as $tag) : ?>
                                        <span class="badge bg-primary me-2 mb-2"><?php echo esc_html($tag->name); ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif;
                    endif; ?>

                    <?php if (has_tag()) : ?>
                        <footer class="entry-footer">
                            <div class="tags-links">
                                <i class="bi bi-tags"></i>
                                <?php 
                                // Get tags and output with links but without color/underline styling
                                $tags = get_the_tags();
                                if ($tags) {
                                    $tag_links = array();
                                    foreach ($tags as $tag) {
                                        $tag_url = get_tag_link($tag->term_id);
                                        $tag_links[] = '<a href="' . esc_url($tag_url) . '" style="text-decoration: none; color: inherit;">' . esc_html($tag->name) . '</a>';
                                    }
                                    echo implode(', ', $tag_links);
                                }
                                ?>
                            </div>
                        </footer>
                    <?php endif; ?>

                </article>

            </main>
        </div>
    </div>
</div>

<?php
endwhile; // End of the loop.

get_footer();

