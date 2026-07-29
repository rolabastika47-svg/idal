<?php
/**
 * Breakdance Recruitments AJAX handlers.
 *
 * @package OmsarBreakdanceElements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_omsar_bd_load_more_recruitments', 'omsar_bd_load_more_recruitments' );
add_action( 'wp_ajax_nopriv_omsar_bd_load_more_recruitments', 'omsar_bd_load_more_recruitments' );

/**
 * Load more / filter recruitments for the Breakdance element.
 */
function omsar_bd_load_more_recruitments() {
	check_ajax_referer( 'omsar_bd_recruitments_nonce', 'nonce' );

	$page              = isset( $_POST['page'] ) ? (int) $_POST['page'] : 1;
	$per_page          = isset( $_POST['per_page'] ) ? (int) $_POST['per_page'] : 9;
	$orderby           = isset( $_POST['orderby'] ) ? sanitize_text_field( wp_unslash( $_POST['orderby'] ) ) : 'meta_value';
	$order             = isset( $_POST['order'] ) ? sanitize_text_field( wp_unslash( $_POST['order'] ) ) : 'DESC';
	$background_type   = isset( $_POST['background_type'] ) ? sanitize_text_field( wp_unslash( $_POST['background_type'] ) ) : 'image';
	$background_image  = isset( $_POST['background_image'] ) ? esc_url_raw( wp_unslash( $_POST['background_image'] ) ) : '';
	$background_overlay = isset( $_POST['background_overlay'] ) && $_POST['background_overlay'] === 'yes';
	$status_filter     = isset( $_POST['status_filter'] ) ? sanitize_text_field( wp_unslash( $_POST['status_filter'] ) ) : 'all';

	if ( $background_type === 'image' && empty( $background_image ) ) {
		$background_image = get_template_directory_uri() . '/assets/images/project-2.png';
	}

	$query = new WP_Query(
		array(
			'post_type'              => 'recruitments',
			'posts_per_page'         => -1,
			'post_status'            => 'publish',
			'update_post_term_cache' => false,
			'no_found_rows'          => true,
		)
	);

	$all_posts = $query->posts;
	if ( ! empty( $all_posts ) ) {
		$all_posts = omsar_sort_recruitments_by_status_priority( $all_posts );
	}
	if ( $status_filter !== 'all' ) {
		$all_posts = omsar_filter_recruitments_by_status( $all_posts, $status_filter );
	}

	$offset              = ( $page - 1 ) * $per_page;
	$paginated_posts     = array_slice( $all_posts, $offset, $per_page );
	$total_found         = count( $all_posts );
	$posts_on_this_page  = count( $paginated_posts );
	$total_loaded_so_far = $offset + $posts_on_this_page;
	$has_more            = ( $total_found > $total_loaded_so_far );

	if ( empty( $paginated_posts ) ) {
		if ( $status_filter === 'open' ) {
			$labels          = omsar_bd_recruitments_get_labels();
			$no_results_html = '<div class="omsar-recruitment-item omsar-recruitment-empty-state omsar-recruitment-no-open-positions" data-status="open"><p class="omsar-recruitment-empty-message">' . esc_html( $labels['no_open_message'] ) . '</p></div>';
		} else {
			$no_results_text = function_exists( 'pll__' ) ? pll__( 'No results found.' ) : __( 'No results found.', 'omsar' );
			$no_results_html = '<div class="omsar-recruitment-item"><p>' . esc_html( $no_results_text ) . '</p></div>';
		}

		wp_send_json_success(
			array(
				'html'      => $no_results_html,
				'has_more'  => false,
				'next_page' => $page + 1,
				'total'     => $total_found,
				'loaded'    => 0,
				'count'     => 0,
			)
		);
	}

	$current_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
	$card_args    = array(
		'background_type'    => $background_type,
		'background_image'   => $background_image,
		'background_overlay' => $background_overlay,
		'is_arabic'          => ( $current_lang === 'ar' || is_rtl() ),
		'labels'             => omsar_bd_recruitments_get_labels(),
	);

	ob_start();
	foreach ( $paginated_posts as $post ) {
		omsar_bd_render_recruitment_card( $post->ID, $card_args );
	}
	$html = ob_get_clean();

	if ( ! is_string( $html ) ) {
		$html = '';
	}

	wp_send_json_success(
		array(
			'html'      => $html,
			'has_more'  => $has_more,
			'next_page' => $page + 1,
			'total'     => $total_found,
			'loaded'    => $total_loaded_so_far,
			'count'     => $posts_on_this_page,
		)
	);
}
