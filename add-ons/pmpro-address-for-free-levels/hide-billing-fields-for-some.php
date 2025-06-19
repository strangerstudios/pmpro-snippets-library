<?php
/**
 * Hide billing fields from certain levels when added using the 
 * "Capture Name & Address for Free Levels
 * 
 * title: Hide billing fields for some.
 * layout: snippet
 * collection: add-ons
 * category: pmpro-address-for-free-levels
 * link: https://www.paidmembershipspro.com/exclude-billing-fields-from-some-membership-levels-at-checkout/
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_hide_billing_fields_for_levels() {
	$levels_to_hide = array( 1, 3, 5 ); // Change these to match your level IDs.
	$level = pmpro_getLevelAtCheckout();

	if ( is_object( $level ) && isset( $level->id ) && in_array( (int) $level->id, $levels_to_hide, true ) ) {
		remove_action( 'init', 'pmproaffl_init_include_address_fields_at_checkout', 30 );
		remove_action( 'pmpro_checkout_boxes', 'pmproaffl_pmpro_checkout_boxes_require_address' );
		remove_action( 'pmpro_required_billing_fields', 'pmproaffl_pmpro_required_billing_fields', 30 );
		remove_action( 'pmpro_paypalexpress_session_vars', 'pmproaffl_pmpro_paypalexpress_session_vars' );
		remove_action( 'pmpro_before_send_to_twocheckout', 'pmproaffl_pmpro_paypalexpress_session_vars', 10, 2 );
		remove_action( 'pmpro_checkout_before_change_membership_level', 'pmproaffl_pmpro_checkout_before_change_membership_level', 5, 2 );
		remove_action( 'pmpro_checkout_preheader', 'pmproaffl_init_load_session_vars', 5 );
		remove_filter( 'pmpro_checkout_order_free', 'pmproaffl_pmpro_checkout_order_free' );
	}
}
add_action( 'init', 'my_pmpro_hide_billing_fields_for_levels' );
