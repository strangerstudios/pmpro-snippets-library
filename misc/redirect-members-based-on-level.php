<?php
/**
 * Redirect members to a specific page based on their membership level when logging in.
 *
 * title: Redirect members on login per level
 * layout: snippet
 * collection: misc
 * category: login, redirect
 * link: https://www.paidmembershipspro.com/redirect-members-to-pages-based-on-their-level/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_login_redirect_per_membership_level( $redirect_to, $request, $user ) {
	// Is there a user to check?
	if ( ! empty( $user->ID ) ) {
		// Check membership level.
		if ( pmpro_hasMembershipLevel( 1, $user->ID ) ) {
			return home_url( '/level-one/' );
		} elseif ( pmpro_hasMembershipLevel( 2, $user->ID ) ) {
			return home_url( '/level-two/' );
		} elseif ( pmpro_hasMembershipLevel( 3, $user->ID ) ) {
			return home_url( '/level-three/' );
		} else {
			return home_url();
		}
	}

	// Return the default redirect if no conditions are met.
	return $redirect_to;
}
add_filter( 'login_redirect', 'my_login_redirect_per_membership_level', 10, 3 );
