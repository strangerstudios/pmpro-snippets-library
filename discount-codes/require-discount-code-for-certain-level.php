<?php
/**
 * Require a Discount Code to Checkout for a Certain Level
 * 
 * title: require discount code for a certain level
 * layout: snippet-example
 * collection: discount-codes
 * category: checkout
 * link: https://www.paidmembershipspro.com/require-a-discount-code-to-checkout-for-a-certain-level/
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_registration_checks_require_code_to_register( $pmpro_continue_registration ) {
	// Only bother if things are okay so far.
	if ( ! $pmpro_continue_registration ) {
		return $pmpro_continue_registration;
	}

	global $pmpro_level, $discount_code;

	if ( $pmpro_level->id == 1 && empty( $discount_code ) ) {
		// Level 1 and no discount code: block registration.
		pmpro_setMessage( 'You must use a valid discount code to register for this level.', 'pmpro_error' );
		return false;
	}

	// To require a specific discount code, modify this conditional:
	// if ( $pmpro_level->id == 1 && ( empty( $discount_code ) || trim( strtoupper( $discount_code ) ) !== 'REQUIRED_CODE' ) )

	return $pmpro_continue_registration;
}
add_filter( 'pmpro_registration_checks', 'my_pmpro_registration_checks_require_code_to_register' );
