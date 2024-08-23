<?php
/**
 * Restrict a level sign up to require Group code.
 * 
 * title: Restrict a level sign up to require Group code. 
 * layout: snippet
 * collection: add-ons, pmpro-group-accounts
 * category: levels, checkout
 * url: none
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
	// change line 24 to the level you want to restrict.	
	// if level = 2 and there is no group code, then show an error message
	$pmpro_level = pmpro_getLevelAtCheckout();
	if ( $pmpro_level->id == 2 && empty( $_REQUEST['pmprogroupacct_group_code'] ) ) {
		pmpro_setMessage("You must use a valid group code to register for this level.", "pmpro_error");
		return false;
	}
	
	return $pmpro_continue_registration;
}
add_filter( 'pmpro_registration_checks', 'my_pmpro_require_group_code_to_register' );