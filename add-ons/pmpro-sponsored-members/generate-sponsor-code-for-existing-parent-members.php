<?php

/**
 * When a user logs in, check if they have a sponsoring level but don't have
 * a sponsor code. If this is the case, create one.
 *
 * title: Generate sponsor code for existing members on a parent membership level
 * layout: snippet
 * collection: add-ons, pmpro-sponsored-members
 * category: child account, parent account
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function my_pmprosm_create_sponsor_code_if_needed( $user_login, $user ) {
	if ( ! function_exists( 'pmpro_getMembershipLevelsForUser' ) || ! function_exists( 'pmprosm_isMainLevel' ) ) {
		return;
	}
	 
	// Get the user's membership level.
	$user_level = pmpro_getMembershipLevelForUser( $user->ID );
	if ( empty( $user_level ) ) {
		return;
	}

	// Make sure that the level is a sponsoring level.
	if ( ! pmprosm_isMainLevel( $user_level->id ) ) {
		return;
	}

	// Check if the user already has a sponsor code.
	$code_id = pmprosm_getCodeByUserID( $user->ID );
	if ( ! empty( $code_id ) ) {
		return;
	}

	// Create a sponsor code for the user.
	$pmprosm_values = pmprosm_getValuesByMainLevel( $user_level->id );
  // TODO: Change 'max_seats' to 'seats' if needed. Depends on your Sponsored Members setup.
	pmprosm_createSponsorCode( $user->ID, $user_level->id, $pmprosm_values['max_seats'] );
	update_user_meta( $user->ID, 'pmprosm_seats', $pmprosm_values['max_seats'] );
}

add_action( 'wp_login', 'my_pmprosm_create_sponsor_code_if_needed', 10, 2 );
