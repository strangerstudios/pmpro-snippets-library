<?php
/**
 * Only show the discount code fields if a code is passed in via URL paramter
 *
 * title: Hide the Discount Code Fields
 * layout: snippet
 * collection: checkout
 * category: discount-codes
 * link: https://www.paidmembershipspro.com/hide-the-discount-code-fields/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_show_discount_code_hide_fields( $show ) {
	if ( empty( $_REQUEST['discount_code'] ) ) {
		$show = false;
	}
	return $show;
}
add_filter( 'pmpro_show_discount_code', 'my_pmpro_show_discount_code_hide_fields' );
