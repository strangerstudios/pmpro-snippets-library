<?php
/**
 * Automatically apply a discount code to Paid Memberships Pro checkout.
 * 
 * Adjust the discount code that should apply to all checkouts.
 * 
 * title: Automatically apply discount code at checkout
 * layout: snippet-example
 * collection: discount-codes
 * category: checkout
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_apply_discount_code() {
	// Check if we're on the checkout page without a discount code already set.
	if ( empty( $_REQUEST['level'] ) || ! empty( $_REQUEST['discount_code'] ) ) {
		return;
	}

	$code = 'SE217EA4DA7'; //Change code here. Hint: Use a discount code for all levels to automatically apply it.

	if ( pmpro_checkDiscountCode( $code, $_REQUEST['level'] ) ) {
		$_REQUEST['discount_code'] = $code;
	}

}
add_action( 'init', 'my_pmpro_apply_discount_code' );
