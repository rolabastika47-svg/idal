<?php
/**
 * Procurement notices Apply-button behavior helpers.
 *
 * Behavior is controlled per procurement notice via ACF/meta field `enable_popup` (true/false).
 * - If enable_popup is true: Apply opens popup.
 * - If enable_popup is false:
 *   - If procurement_link is set: Apply opens external link in a new tab.
 *   - If procurement_link is empty: Apply button is not shown.
 *
 * @package OMSAR
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read true/false field `enable_popup` for a procurement notice.
 *
 * @param int $post_id Procurement notice post ID.
 * @return bool
 */
function omsar_procurement_notice_enable_popup( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return false;
	}

	$value = null;
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( 'enable_popup', $post_id );
	} else {
		$value = get_post_meta( $post_id, 'enable_popup', true );
	}

	// ACF true/false can be: true/false, 1/0, '1'/'0'.
	return (bool) ( is_string( $value ) ? (int) $value : $value );
}

/**
 * Normalized external apply URL from `procurement_link` field.
 *
 * @param int $post_id Procurement notice post ID.
 * @return string Escaped URL safe for attributes, or empty if unset/invalid.
 */
function omsar_get_procurement_notice_apply_link( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return '';
	}

	$raw = '';
	if ( function_exists( 'get_field' ) ) {
		$raw = get_field( 'procurement_link', $post_id );
	} else {
		$raw = get_post_meta( $post_id, 'procurement_link', true );
	}

	// Support ACF URL field return formats.
	if ( is_array( $raw ) ) {
		if ( isset( $raw['url'] ) && is_string( $raw['url'] ) ) {
			$raw = $raw['url'];
		} elseif ( isset( $raw['value'] ) && is_string( $raw['value'] ) ) {
			$raw = $raw['value'];
		} else {
			$raw = '';
		}
	}

	$raw = is_string( $raw ) ? trim( $raw ) : '';
	if ( $raw === '' ) {
		return '';
	}

	// Allow mailto too (used by some procurements).
	$url = esc_url_raw( $raw, array( 'http', 'https', 'mailto' ) );

	/**
	 * Filter the procurement external apply URL after sanitization.
	 *
	 * @param string $url     Sanitized URL or empty.
	 * @param int    $post_id Post ID.
	 */
	$url = apply_filters( 'omsar_procurement_notice_apply_url', $url, $post_id );

	if ( ! is_string( $url ) || $url === '' ) {
		return '';
	}

	return esc_url( $url, array( 'http', 'https', 'mailto' ) );
}

/**
 * Whether Apply should open the popup (only for notices that render an Apply control).
 *
 * @param int $post_id Procurement notice post ID.
 * @return bool
 */
function omsar_procurement_notice_should_open_popup( $post_id ) {
	return omsar_procurement_notice_enable_popup( $post_id );
}

/**
 * Print the procurement email Apply modal once per request.
 *
 * Safe to call from multiple templates/widgets.
 */
function omsar_procurement_render_apply_modal_once() {
	static $rendered = false;
	if ( $rendered ) {
		return;
	}
	$rendered = true;

	$apply_modal_title       = function_exists( 'pll__' ) ? pll__( 'Apply' ) : __( 'Apply', 'omsar' );
	$apply_modal_message     = function_exists( 'pll__' ) ? pll__( 'Enter your email to apply for this procurement notice.' ) : __( 'Enter your email to apply for this procurement notice.', 'omsar' );
	$apply_modal_email_label = function_exists( 'pll__' ) ? pll__( 'Email Address' ) : __( 'Email Address', 'omsar' );
	$apply_modal_submit      = function_exists( 'pll__' ) ? pll__( 'Submit' ) : __( 'Submit', 'omsar' );
	$apply_modal_close_label = function_exists( 'pll__' ) ? pll__( 'Close' ) : __( 'Close', 'omsar' );
	?>
	<div id="omsar-procurement-apply-modal" class="omsar-procurement-apply-modal" role="dialog" aria-labelledby="omsar-procurement-apply-modal-title" aria-hidden="true">
		<div class="omsar-procurement-apply-modal-overlay"></div>
		<div class="omsar-procurement-apply-modal-content">
			<button type="button" class="omsar-procurement-apply-modal-close" aria-label="<?php echo esc_attr( $apply_modal_close_label ); ?>">
				<span aria-hidden="true">&times;</span>
			</button>
			<div class="omsar-procurement-apply-modal-body">
				<h2 id="omsar-procurement-apply-modal-title" class="omsar-procurement-apply-modal-title">
					<?php echo esc_html( $apply_modal_title ); ?>
				</h2>
				<p class="omsar-procurement-apply-modal-message">
					<?php echo esc_html( $apply_modal_message ); ?>
				</p>
				<form id="omsar-procurement-apply-form" class="omsar-procurement-apply-form">
					<input type="hidden" name="procurement_id" id="omsar-procurement-apply-id" value="">
					<div class="omsar-procurement-apply-form-group">
						<label for="omsar-procurement-apply-email" class="omsar-procurement-apply-label">
							<?php echo esc_html( $apply_modal_email_label ); ?>
						</label>
						<input type="email" id="omsar-procurement-apply-email" name="email" class="omsar-procurement-apply-input" required aria-required="true" aria-describedby="omsar-procurement-apply-email-error">
						<span id="omsar-procurement-apply-email-error" class="omsar-procurement-apply-error" role="alert" aria-live="polite"></span>
					</div>
					<div class="omsar-procurement-apply-form-actions">
						<button type="submit" class="omsar-procurement-apply-submit-btn">
							<?php echo esc_html( $apply_modal_submit ); ?>
						</button>
					</div>
				</form>
				<div id="omsar-procurement-apply-success" class="omsar-procurement-apply-success" role="alert" aria-live="polite" style="display: none;"></div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Render the Apply control for procurement cards / single templates.
 *
 * @param int    $post_id     Procurement notice ID.
 * @param string $apply_label Label (translated).
 * @param string $status      Normalized status: open|closed|cancelled.
 * @param string $context     'card' or 'single'.
 */
function omsar_render_procurement_apply_button( $post_id, $apply_label, $status, $context = 'card' ) {
	if ( 'open' !== $status ) {
		return;
	}

	$post_id = (int) $post_id;
	$link    = omsar_get_procurement_notice_apply_link( $post_id );
	if ( ! omsar_procurement_notice_enable_popup( $post_id ) && $link === '' ) {
		return;
	}

	$should_popup = omsar_procurement_notice_should_open_popup( $post_id );

	$wrapper = ( 'single' === $context ) ? 'omsar-procurement-single-actions' : 'omsar-procurement-card-button';

	printf(
		'<div class="%1$s"><button type="button" class="omsar-procurement-apply-button" data-procurement-id="%2$s" data-procurement-link="%3$s" data-procurement-popup="%4$s">%5$s</button></div>',
		esc_attr( $wrapper ),
		esc_attr( (string) $post_id ),
		esc_attr( $link ),
		esc_attr( $should_popup ? '1' : '0' ),
		esc_html( $apply_label )
	);
}

