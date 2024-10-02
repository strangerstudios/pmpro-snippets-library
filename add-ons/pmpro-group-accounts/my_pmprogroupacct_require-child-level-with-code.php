<?php
/**
 * Restrict checkout for a level to require a Group Account code.
 * 
 * title: Restrict checkout for a level to require a Group Account code.
 * layout: snippet
 * collection: add-ons, pmpro-group-accounts
 * category: levels, checkout
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_require_group_code_to_register( $pmpro_continue_registration ) {
	// If there is already a checkout error, bail.
	if ( ! $pmpro_continue_registration ) {
		return $pmpro_continue_registration;
	}
	
	$required_levels = array( 2 ); // Add the level IDs that require a group code here.
	$pmpro_level = pmpro_getLevelAtCheckout();
	if ( in_array( $pmpro_level->id, $required_levels ) && empty( $_REQUEST['pmprogroupacct_group_code'] ) ) {
		pmpro_setMessage("You must use a valid group code to register for this level.", "pmpro_error");
		return false;
	}
	
	return $pmpro_continue_registration;
}
add_filter( 'pmpro_registration_checks', 'my_pmpro_require_group_code_to_register' );