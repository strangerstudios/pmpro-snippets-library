<?php
/**
 * Automatically apply a discount code to Paid Memberships Pro checkout.
 *
 * Learn more at https://www.paidmembershipspro.com/automatic-discount-code/
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
function my_pmpro_apply_discount_code( $discount_code, $level_id ) {
	$code = '4A54F5BFC7'; //Change code here. Hint: Use a discount code for all levels to automatically apply it.
	if ( pmpro_checkDiscountcode( $code, $level_id ) ) {
		return $code;
	}

	return $discount_code;
}

add_filter( 'pmpro_default_discount_code',  'my_pmpro_apply_discount_code', 10, 2 );
