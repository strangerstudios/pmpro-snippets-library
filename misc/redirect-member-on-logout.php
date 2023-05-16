<?php 
/**
 *  Redirect member to home page when they're logging out.
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
function my_pmpro_redirect_member_on_logout( $user_id ) {
	// Redirect active members that belong to level IDs 1, 2, or 3 to the home page on logout. Adjust this array accordingly.
	if ( function_exists( 'pmpro_hasMembershipLevel' ) && pmpro_hasMembershipLevel( array( '1', '2', '3' ), $user_id ) ) {
		wp_safe_redirect( home_url() );
		exit;
	}
}
add_action( 'wp_logout', 'my_pmpro_redirect_member_on_logout', 10, 1 );
