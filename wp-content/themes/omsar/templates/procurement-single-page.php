<?php
/*
Template Name: Procurement Single Page
Description: Display a single procurement notice. You can select a procurement notice via the "Selected Procurement Notice" custom field on the page, or pass a procurement_id query parameter.
*/
get_header();

// Get procurement notice ID from page custom field, query parameter, or URL slug
$procurement_id = null;
$page_id = get_the_ID();

// First, try to get from page custom field
if ( function_exists( 'get_field' ) ) {
	$procurement_id = get_field( 'selected_procurement_notice', $page_id );
	// If ACF field returns an object/array, extract the ID
	if ( is_object( $procurement_id ) && isset( $procurement_id->ID ) ) {
		$procurement_id = $procurement_id->ID;
	} elseif ( is_array( $procurement_id ) && isset( $procurement_id['ID'] ) ) {
		$procurement_id = $procurement_id['ID'];
	}
}

// If not found, try query parameter
if ( empty( $procurement_id ) && isset( $_GET['procurement_id'] ) ) {
	$procurement_id = absint( $_GET['procurement_id'] );
}

// If still not found, try to get from URL slug (for cleaner URLs)
if ( empty( $procurement_id ) ) {
	global $wp;
	$current_url = home_url( add_query_arg( array(), $wp->request ) );
	$request_uri = $_SERVER['REQUEST_URI'];
	
	// Get the page slug
	$page_slug = get_post_field( 'post_name', $page_id );
	
	// Extract potential procurement slug from URL
	// Example: /procurement-page/procurement-notice-slug/
	if ( $page_slug && strpos( $request_uri, '/' . $page_slug . '/' ) !== false ) {
		$url_parts = explode( '/' . $page_slug . '/', $request_uri );
		if ( isset( $url_parts[1] ) && ! empty( $url_parts[1] ) ) {
			$potential_slug = trim( $url_parts[1], '/' );
			// Try to find procurement notice by slug
			$procurement_post_by_slug = get_page_by_path( $potential_slug, OBJECT, 'procurement_notices' );
			if ( $procurement_post_by_slug ) {
				$procurement_id = $procurement_post_by_slug->ID;
			}
		}
	}
}

// Validate procurement ID
if ( empty( $procurement_id ) ) {
	?>
	<div id="primary" class="content-area">
		<div class="container static-height">
			<div class="row g-0 g-md-1 g-lg-2">
				<main id="main" class="site-main col-12">
					<article class="omsar-procurement-single">
						<div class="alert alert-warning">
							<?php echo function_exists( 'pll__' ) ? pll__( 'Please select a procurement notice or provide a procurement_id parameter.' ) : __( 'Please select a procurement notice or provide a procurement_id parameter.', 'omsar' ); ?>
						</div>
					</article>
				</main>
			</div>
		</div>
	</div>
	<?php
	get_footer();
	return;
}

// Get the procurement notice post
$procurement_post = get_post( $procurement_id );

// Validate post exists and is a procurement notice
if ( ! $procurement_post || $procurement_post->post_type !== 'procurement_notices' ) {
	?>
	<div id="primary" class="content-area">
		<div class="container pt-4 static-height">
			<div class="row g-0 g-md-1 g-lg-2">
				<main id="main" class="site-main col-12">
					<article class="omsar-procurement-single">
						<div class="alert alert-error">
							<?php echo function_exists( 'pll__' ) ? pll__( 'Procurement notice not found.' ) : __( 'Procurement notice not found.', 'omsar' ); ?>
						</div>
					</article>
				</main>
			</div>
		</div>
	</div>
	<?php
	get_footer();
	return;
}

// Set up global post data
global $post;
$post = $procurement_post;
setup_postdata( $post );

$post_id = $procurement_id;

// Get current language
$current_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
$is_rtl = ( $current_lang === 'ar' || is_rtl() );

// Get custom fields
$reference_no = '';
$publication_custom_date = '';
$submission_deadline = '';
$procurement_description = '';
$procurement_description_ar = '';
$procurement_title_ar = '';
$procurement_documents = '';
$procurement_documents_ar = '';
	$apply_link = '';
	$is_cancelled = false;

	if ( function_exists( 'get_field' ) ) {
		$reference_no = get_field( 'reference_no', $post_id );
		$publication_custom_date = get_field( 'publication_custom_date', $post_id );
		$submission_deadline = get_field( 'submission_deadline', $post_id );
		$procurement_description = get_field( 'procurement_description', $post_id );
		$procurement_description_ar = get_field( 'procurement_description_ar', $post_id );
		$procurement_title_ar = get_field( 'procurement_title_ar', $post_id );
		$procurement_documents = get_field( 'procurement_documents', $post_id );
		$procurement_documents_ar = get_field( 'procurement_documents_ar', $post_id );
		$apply_link = get_field( 'procurement_link', $post_id );
		$is_cancelled = get_field( 'is_cancelled', $post_id );
	} else {
		// Fallback to post meta
		$reference_no = get_post_meta( $post_id, 'reference_no', true );
		$publication_custom_date = get_post_meta( $post_id, 'publication_custom_date', true );
		$submission_deadline = get_post_meta( $post_id, 'submission_deadline', true );
		$procurement_description = get_post_meta( $post_id, 'procurement_description', true );
		$procurement_description_ar = get_post_meta( $post_id, 'procurement_description_ar', true );
		$procurement_title_ar = get_post_meta( $post_id, 'procurement_title_ar', true );
		$procurement_documents = get_post_meta( $post_id, 'procurement_documents', true );
		$procurement_documents_ar = get_post_meta( $post_id, 'procurement_documents_ar', true );
		$apply_link = get_post_meta( $post_id, 'procurement_link', true );
		$is_cancelled = get_post_meta( $post_id, 'is_cancelled', true );
	}

// Title: post title for English, procurement_title_ar for Arabic (fallback to post title)
$display_title = $is_rtl && ! empty( $procurement_title_ar ) ? $procurement_title_ar : get_the_title( $post_id );

// Description: procurement_description for English, procurement_description_ar for Arabic (fallback to EN)
$display_description = $is_rtl && ! empty( $procurement_description_ar ) ? $procurement_description_ar : $procurement_description;

// Documents: procurement_documents for English, procurement_documents_ar for Arabic (fallback to EN)
// These are repeater fields, each row has document_file (or document_file_ar) which is a File ID
// Get the repeater field directly based on language
if ( $is_rtl && ! empty( $procurement_documents_ar ) ) {
	$display_documents_repeater = $procurement_documents_ar;
} else {
	$display_documents_repeater = $procurement_documents;
}

// If using ACF, ensure we get the repeater properly
if ( function_exists( 'get_field' ) && empty( $display_documents_repeater ) ) {
	// Try to get the repeater field directly
	$repeater_field_name = $is_rtl ? 'procurement_documents_ar' : 'procurement_documents';
	$display_documents_repeater = get_field( $repeater_field_name, $post_id );
}

// Get computed status from dates and is_cancelled
if ( function_exists( 'omsar_get_procurement_status' ) ) {
	$status = omsar_get_procurement_status( $post_id );
} elseif ( function_exists( 'omsar_compute_procurement_status_from_values' ) ) {
	$status = omsar_compute_procurement_status_from_values( $publication_custom_date, $submission_deadline, $is_cancelled );
} else {
	$status = 'closed';
}

// Format dates
$formatted_pub_date = '';
$formatted_deadline = '';

if ( $publication_custom_date ) {
	$publication_date_str = trim( (string) $publication_custom_date );
	if ( preg_match( '/^\d{1,2}\/\d{1,2}\/\d{4}$/', $publication_date_str ) ) {
		$date_obj = DateTime::createFromFormat( 'd/m/Y', $publication_date_str );
		if ( $date_obj !== false ) {
			$formatted_pub_date = $date_obj->format( 'd/m/Y' );
		} else {
			$formatted_pub_date = $publication_custom_date;
		}
	} elseif ( is_numeric( $publication_custom_date ) && strlen( $publication_date_str ) == 8 ) {
		$date_obj = DateTime::createFromFormat( 'Ymd', $publication_date_str );
		if ( $date_obj !== false ) {
			$formatted_pub_date = $date_obj->format( 'd/m/Y' );
		} else {
			$formatted_pub_date = date( 'd/m/Y', $publication_custom_date );
		}
	} elseif ( is_numeric( $publication_custom_date ) ) {
		$formatted_pub_date = date( 'd/m/Y', $publication_custom_date );
	} else {
		$timestamp = strtotime( $publication_custom_date );
		$formatted_pub_date = $timestamp ? date( 'd/m/Y', $timestamp ) : $publication_custom_date;
	}
}

if ( $submission_deadline ) {
	$submission_deadline_str = trim( (string) $submission_deadline );
	$str_length = strlen( $submission_deadline_str );
	
	if ( preg_match( '/^\d{1,2}\/\d{1,2}\/\d{4}$/', $submission_deadline_str ) ) {
		$date_obj = DateTime::createFromFormat( 'd/m/Y', $submission_deadline_str );
		if ( $date_obj !== false ) {
			$formatted_deadline = $date_obj->format( 'd/m/Y' );
		} else {
			$formatted_deadline = $submission_deadline;
		}
	} elseif ( $str_length == 8 && ctype_digit( $submission_deadline_str ) ) {
		$date_obj = DateTime::createFromFormat( 'Ymd', $submission_deadline_str );
		if ( $date_obj !== false ) {
			$formatted_deadline = $date_obj->format( 'd/m/Y' );
		} else {
			$formatted_deadline = $submission_deadline;
		}
	} elseif ( $str_length == 10 && ctype_digit( $submission_deadline_str ) ) {
		$formatted_deadline = date( 'd/m/Y', (int) $submission_deadline );
	} else {
		$deadline_timestamp = strtotime( $submission_deadline );
		if ( $deadline_timestamp !== false ) {
			$formatted_deadline = date( 'd/m/Y', $deadline_timestamp );
		} else {
			$date_obj = DateTime::createFromFormat( 'Y-m-d', $submission_deadline_str );
			if ( $date_obj !== false ) {
				$formatted_deadline = $date_obj->format( 'd/m/Y' );
			} else {
				$formatted_deadline = $submission_deadline;
			}
		}
	}
}

// Get notice_type_taxonomy terms
$notice_type_terms = array();
if ( function_exists( 'omsar_get_acf_taxonomy_terms' ) ) {
	$notice_type_terms = omsar_get_acf_taxonomy_terms( 'notice_type_taxonomy', $post_id );
} elseif ( function_exists( 'get_field' ) ) {
	$taxonomy_value = get_field( 'notice_type_taxonomy', $post_id );
	if ( ! empty( $taxonomy_value ) ) {
		if ( is_array( $taxonomy_value ) ) {
			foreach ( $taxonomy_value as $item ) {
				if ( is_object( $item ) && isset( $item->term_id ) ) {
					$notice_type_terms[] = $item;
				} elseif ( is_numeric( $item ) ) {
					$term = get_term( $item );
					if ( $term && ! is_wp_error( $term ) ) {
						$notice_type_terms[] = $term;
					}
				} elseif ( is_array( $item ) && isset( $item['term_id'] ) ) {
					$term = get_term( $item['term_id'] );
					if ( $term && ! is_wp_error( $term ) ) {
						$notice_type_terms[] = $term;
					}
				}
			}
		} elseif ( is_object( $taxonomy_value ) && isset( $taxonomy_value->term_id ) ) {
			$notice_type_terms[] = $taxonomy_value;
		} elseif ( is_numeric( $taxonomy_value ) ) {
			$term = get_term( $taxonomy_value );
			if ( $term && ! is_wp_error( $term ) ) {
				$notice_type_terms[] = $term;
			}
		}
	}
}

// Get status labels
$open_label = function_exists( 'pll__' ) ? pll__( 'Open' ) : __( 'Open', 'omsar' );
$closed_label = function_exists( 'pll__' ) ? pll__( 'Closed' ) : __( 'Closed', 'omsar' );
$cancelled_label = function_exists( 'pll__' ) ? pll__( 'Cancelled' ) : __( 'Cancelled', 'omsar' );
$apply_label = function_exists( 'pll__' ) ? pll__( 'Apply' ) : __( 'Apply', 'omsar' );

$opening_label = function_exists( 'pll__' ) ? pll__( 'Opening Date' ) : __( 'Opening Date', 'omsar' );
$closing_label = function_exists( 'pll__' ) ? pll__( 'Closing Date' ) : __( 'Closing Date', 'omsar' );

// Get status label
if ( $status === 'cancelled' ) {
	$status_label = $cancelled_label;
} elseif ( $status === 'closed' ) {
	$status_label = $closed_label;
} else {
	$status_label = $open_label;
}

?>

<div id="primary" class="content-area">
	<div class="container pt-4 static-height">
		<div class="row g-0 g-md-1 g-lg-2">
			<main id="main" class="site-main col-12">
				<article id="post-<?php echo esc_attr( $post_id ); ?>" class="omsar-procurement-single">
					
					<div class="omsar-procurement-single-header">
						<span class="omsar-procurement-status-badge omsar-status-<?php echo esc_attr( $status ); ?>">
							<?php echo esc_html( $status_label ); ?>
						</span>
					</div>
					
					<div class="omsar-procurement-fields-row">
						<?php if ( ! empty( $reference_no ) ) : ?>
						<div class="omsar-procurement-single-field omsar-procurement-field-inline">
							<strong class="omsar-procurement-field-label"><?php echo function_exists( 'pll__' ) ? pll__( 'Reference No.' ) : __( 'Reference No.', 'omsar' ); ?>:</strong>
							<span class="omsar-procurement-field-value"><?php echo esc_html( $reference_no ); ?></span>
						</div>
						<?php endif; ?>
						
						<?php if ( ! empty( $notice_type_terms ) ) : ?>
						<div class="omsar-procurement-single-field omsar-procurement-field-inline">
							<strong class="omsar-procurement-field-label"><?php echo function_exists( 'pll__' ) ? pll__( 'Notice Type' ) : __( 'Notice Type', 'omsar' ); ?>:</strong>
							<span class="omsar-procurement-field-value">
								<?php
								$term_names = array();
								foreach ( $notice_type_terms as $term ) {
									if ( $term && ! is_wp_error( $term ) ) {
										$term_names[] = esc_html( $term->name );
									}
								}
								echo implode( ', ', $term_names );
								?>
							</span>
						</div>
						<?php endif; ?>
						
						<?php if ( $formatted_pub_date || $formatted_deadline ) : ?>
						<div class="omsar-procurement-single-field omsar-procurement-field-inline">
							<!-- <strong class="omsar-procurement-field-label"><?php //echo function_exists( 'pll__' ) ? pll__( 'Dates' ) : __( 'Dates', 'omsar' ); ?>:</strong> -->
							<div class="omsar-procurement-field-value omsar-procurement-date-range">
								<?php if ( $formatted_pub_date ) : ?>
									<div class="omsar-procurement-date-row">
										<i class="bi bi-calendar2-check"></i>
										<span class="omsar-procurement-date-label"><?php echo esc_html( $opening_label ); ?>:</span>
										<span class="omsar-procurement-date-value"><?php echo esc_html( $formatted_pub_date ); ?></span>
									</div>
								<?php endif; ?>
								<?php if ( $formatted_deadline ) : ?>
									<div class="omsar-procurement-date-row">
										<i class="bi bi-calendar2-x"></i>
										<span class="omsar-procurement-date-label"><?php echo esc_html( $closing_label ); ?>:</span>
										<span class="omsar-procurement-date-value"><?php echo esc_html( $formatted_deadline ); ?></span>
									</div>
								<?php endif; ?>
							</div>
						</div>
						<?php endif; ?>
					</div>
					
					<?php
					// Handle repeater field: procurement_documents (or procurement_documents_ar)
					// Each row has document_file (or document_file_ar) which is a File ID
					$has_documents = false;
					$repeater_field_name = $is_rtl ? 'procurement_documents_ar' : 'procurement_documents';
					
					// Check if we have repeater rows using ACF's have_rows() function (more reliable)
					if ( function_exists( 'have_rows' ) && have_rows( $repeater_field_name, $post_id ) ) {
						$has_documents = true;
					} elseif ( ! empty( $display_documents_repeater ) && is_array( $display_documents_repeater ) ) {
						$has_documents = true;
					}
					?>
					
					<?php if ( ! empty( $display_description ) || $has_documents ) : ?>
					<div class="omsar-procurement-single-field">
						<strong class="omsar-procurement-field-label"><?php echo function_exists( 'pll__' ) ? pll__( 'Description' ) : __( 'Description', 'omsar' ); ?>:</strong>
						<div class="omsar-procurement-field-value omsar-procurement-description">
							<?php if ( ! empty( $display_description ) ) : ?>
								<?php echo wp_kses_post( wpautop( $display_description ) ); ?>
							<?php endif; ?>
							
							<?php if ( $has_documents ) : ?>
							<div class="omsar-procurement-documents">
							<?php
							// Use ACF's have_rows() if available (recommended way)
							if ( function_exists( 'have_rows' ) && have_rows( $repeater_field_name, $post_id ) ) {
								while ( have_rows( $repeater_field_name, $post_id ) ) {
									the_row();
									
									// Get document_file or document_file_ar sub-field
									$document_file_id = '';
									if ( $is_rtl ) {
										$document_file_id = get_sub_field( 'document_file_ar' );
										if ( empty( $document_file_id ) ) {
											$document_file_id = get_sub_field( 'document_file' );
										}
									} else {
										$document_file_id = get_sub_field( 'document_file' );
									}
									
									// Handle if ACF returns object/array instead of ID
									if ( is_array( $document_file_id ) && isset( $document_file_id['ID'] ) ) {
										$document_file_id = $document_file_id['ID'];
									} elseif ( is_object( $document_file_id ) && isset( $document_file_id->ID ) ) {
										$document_file_id = $document_file_id->ID;
									}
									
									// Convert to integer if it's numeric
									if ( is_numeric( $document_file_id ) ) {
										$document_file_id = intval( $document_file_id );
									}
									
									// Display document if we have a valid file ID
									if ( ! empty( $document_file_id ) && is_numeric( $document_file_id ) && $document_file_id > 0 ) {
										$doc_url = wp_get_attachment_url( $document_file_id );
										
										if ( $doc_url ) {
											$doc_title = get_the_title( $document_file_id );
											$doc_filename = basename( get_attached_file( $document_file_id ) );
											
											$link_text = ! empty( $doc_title ) && $doc_title !== 'Attachment' ? $doc_title : ( ! empty( $doc_filename ) ? $doc_filename : __( 'Download', 'omsar' ) );
											
											echo '<a href="' . esc_url( $doc_url ) . '" class="omsar-procurement-document-link" target="_blank" rel="noopener noreferrer">';
											echo '<i class="bi bi-file-earmark-pdf"></i> ' . esc_html( $link_text );
											echo '</a>';
										}
									}
								}
							} else {
								// Fallback: Loop through repeater rows array
								foreach ( $display_documents_repeater as $row ) {
								// Get document_file or document_file_ar from the row
								$document_file_id = '';
								
								// Determine which field to use based on language
								if ( $is_rtl ) {
									// For Arabic, try document_file_ar first, fallback to document_file
									if ( isset( $row['document_file_ar'] ) && ! empty( $row['document_file_ar'] ) ) {
										$document_file_id = $row['document_file_ar'];
									} elseif ( isset( $row['document_file'] ) && ! empty( $row['document_file'] ) ) {
										$document_file_id = $row['document_file'];
									}
								} else {
									// For English, use document_file
									if ( isset( $row['document_file'] ) && ! empty( $row['document_file'] ) ) {
										$document_file_id = $row['document_file'];
									}
								}
								
								// Handle if ACF returns object/array instead of ID
								if ( is_array( $document_file_id ) ) {
									// ACF file field might return array with 'ID' key
									if ( isset( $document_file_id['ID'] ) ) {
										$document_file_id = $document_file_id['ID'];
									} elseif ( isset( $document_file_id['id'] ) ) {
										$document_file_id = $document_file_id['id'];
									}
								} elseif ( is_object( $document_file_id ) ) {
									// ACF file field might return object with ID property
									if ( isset( $document_file_id->ID ) ) {
										$document_file_id = $document_file_id->ID;
									} elseif ( isset( $document_file_id->id ) ) {
										$document_file_id = $document_file_id->id;
									}
								}
								
								// Convert to integer if it's numeric string
								if ( is_numeric( $document_file_id ) ) {
									$document_file_id = intval( $document_file_id );
								}
								
								// If we have a valid file ID, get the URL and display it
								if ( ! empty( $document_file_id ) && is_numeric( $document_file_id ) && $document_file_id > 0 ) {
									$doc_url = wp_get_attachment_url( $document_file_id );
									
									if ( $doc_url ) {
										$doc_title = get_the_title( $document_file_id );
										$doc_filename = basename( get_attached_file( $document_file_id ) );
										
										// Use title, filename, or fallback text
										$link_text = ! empty( $doc_title ) && $doc_title !== 'Attachment' ? $doc_title : ( ! empty( $doc_filename ) ? $doc_filename : __( 'Download', 'omsar' ) );
										
										echo '<a href="' . esc_url( $doc_url ) . '" class="omsar-procurement-document-link" target="_blank" rel="noopener noreferrer">';
										echo '<i class="bi bi-file-earmark-pdf"></i> ' . esc_html( $link_text );
										echo '</a>';
									}
								}
							}
							}
							?>
							</div>
							<?php endif; ?>
						</div>
					</div>
					<?php endif; ?>
					
					<?php
					if ( function_exists( 'omsar_render_procurement_apply_button' ) ) {
						omsar_render_procurement_apply_button( $post_id, $apply_label, $status, 'single' );
					}
					?>
					
				</article>
			</main>
		</div>
	</div>
</div>

<?php
if ( function_exists( 'omsar_procurement_render_apply_modal_once' ) ) {
	omsar_procurement_render_apply_modal_once();
}
?>

<?php
// Reset post data
wp_reset_postdata();

get_footer();
?>
