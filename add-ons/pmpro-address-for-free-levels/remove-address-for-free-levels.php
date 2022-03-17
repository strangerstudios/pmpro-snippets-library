<?php
/**
 * Remove address fields for Free Levels, limiting the addon scope to the Offsite Gateway only.
 *
 * title: Remove address for Free Levels.
 * layout: snippet
 * collection: add-ons, pmpro-address-for-free-levels
 * category: custom-fields
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_remove_address_fields_for_free_levels() {
	global $pmpro_level;

	if ( pmpro_is_checkout() ) {
		if ( pmpro_isLevelFree( $pmpro_level ) ) {
			remove_action( 'pmpro_checkout_boxes', 'pmproaffl_pmpro_checkout_boxes_require_address' );
			remove_action( 'init', 'pmproaffl_init_include_address_fields_at_checkout', 30 );
			remove_action( 'pmpro_required_billing_fields', 'pmproaffl_pmpro_required_billing_fields', 30 );
			remove_action( 'pmpro_paypalexpress_session_vars', 'pmproaffl_pmpro_paypalexpress_session_vars' );
			remove_action( 'pmpro_before_send_to_twocheckout', 'pmproaffl_pmpro_paypalexpress_session_vars', 10, 2 );
			remove_action( 'pmpro_checkout_before_change_membership_level', 'pmproaffl_pmpro_checkout_before_change_membership_level', 5, 2 );
			remove_action( 'pmpro_checkout_preheader', 'pmproaffl_init_load_session_vars', 5 );
			remove_filter( 'pmpro_checkout_order_free', 'pmproaffl_pmpro_checkout_order_free' );
			remove_action( 'pmpro_after_checkout', 'pmproaffl_pmpro_after_checkout' );
		}
	}
}

add_action( 'template_redirect', 'my_pmpro_remove_address_fields_for_free_levels' );
