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
	$user_levels = pmpro_getMembershipLevelsForUser( $user->ID );
	if ( empty( $user_levels ) ) {
		return;
	}

	// Check if there is a sponsoring level.
	$sponsoring_level = false;
	foreach ( $user_levels as $user_level ) {
		if ( pmprosm_isMainLevel( $user_level->id ) ) {
			$sponsoring_level = $user_level->id;
			break;
		}
	}

	if ( empty( $sponsoring_level ) ) {
		return;
	}

	// Check if the user already has a sponsor code.
	$code_id = pmprosm_getCodeByUserID( $user->ID );
	if ( ! empty( $code_id ) ) {
		return;
	}

	// Create a sponsor code for the user.
	$pmprosm_values = pmprosm_getValuesByMainLevel( $sponsoring_level );
	if ( ! empty( $pmprosm_values['max_seats' ) {
		$seats = $pmprosm_values['max_seats'];
	} elseif (! empty( $pmprosm_values['seats'] ) {
		$seats = $pmprosm_values['seats'];
	} else {
		// No seats specified.
		return;
	}
	pmprosm_createSponsorCode( $user->ID, $sponsoring_level, $seats );
	update_user_meta( $user->ID, 'pmprosm_seats', $seats );
}

add_action( 'wp_login', 'my_pmprosm_create_sponsor_code_if_needed', 10, 2 );
