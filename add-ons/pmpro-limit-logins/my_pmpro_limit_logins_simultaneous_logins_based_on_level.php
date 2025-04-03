<?php
/**
 * Change the amount of simultaneous logins allowed based on the user's membership level.
 *
 * title: Different logins limits based on membership level
 * collection: add-ons
 * category: pmpro-limit-logins
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_limit_logins_simultaneous_logins_based_on_level( $num ) {
	// Bail if PMPro not activated or function not available.
	if ( ! function_exists( 'pmpro_hasMembershipLevel' ) ) {
		return $num;
	}

	if ( pmpro_hasMembershipLevel( '1' ) ) {
		$num = 3;
	}

	if ( pmpro_hasMembershipLevel( '2' ) ) {
		$num = 5;
	}

	return $num;
}
add_filter( 'pmpro_limit_logins_number_simultaneous_logins', 'my_pmpro_limit_logins_simultaneous_logins_based_on_level' );
