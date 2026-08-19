<?php
	/**
	 * @var array $propertiesData
	 */

	$args = array(
			'delimiter' => '<div class="bde-woo-breadcrumb_delimiter"></div>',
			'before' => '<span class="bde-woo-breadcrumb-item">',
			'after' => '</span>',
	);
?>
<?php woocommerce_breadcrumb( $args ); ?>
