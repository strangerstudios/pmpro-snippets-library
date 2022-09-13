<?php
/**
 * Add Sponsored discount code to Paid Memberships Pro Zapier Integration data.

 * title: Add sponsored discount code to Zapier
 * layout: snippet
 * collection: add-ons, pmpro-zapier
 * category: discount-code
 *
 * This runs on the "After Checkout" trigger. 
 * Requires Sponsored/Group Members Add On - https://www.paidmembershipspro.com/add-ons/pmpro-sponsored-members/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_add_sponsored_code_zapier( $data, $user_id, $level, $order ) {

	// Bail if Sponsored Members not available.
	if ( ! function_exists( 'pmprosm_getCodeByUserID' ) ) {
		return $data;
	}

	$code_id = pmprosm_getCodeByUserID( $user_id );

	if ( ! empty( $code_id ) ) {
		$discount_code          = pmprosm_getDiscountCodeByCodeID( $code_id );
		$data['sponsored_code'] = $discount_code->code;
	}

	return $data;
}
add_filter( 'pmproz_after_checkout_data', 'my_pmpro_add_sponsored_code_zapier', 10, 4 );
