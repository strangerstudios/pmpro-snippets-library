<?php 
/**
 * Redirect members and non-members on logout to specified pages
 * Learn more at https://www.paidmembershipspro.com/redirect-logout/
 *
 * title: Redirect member on logout
 * layout: snippet
 * collection: misc
 * category: logout, redirect
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_redirect_members_on_logout( $user_id ) {
	if ( function_exists( 'pmpro_hasMembershipLevel' ) ) {
		// Redirect users with membership level 1 to /level-1 page
		if ( pmpro_hasMembershipLevel( 1, $user_id ) ) {
			wp_redirect( '/level-1' );
			exit;
		} else {
			// Redirect non-members and members with other levels to /other-levels page.
			wp_redirect( '/other-levels' );
			exit;
		}
	}
}
add_action( 'wp_logout', 'my_pmpro_redirect_members_on_logout' );
