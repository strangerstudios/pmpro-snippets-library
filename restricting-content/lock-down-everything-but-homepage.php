<?php
/**
 * Lock Down Everything But Homepage for Non-Users
 *
 * title: Redirect to login or homepage if user is logged out or not a member
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, redirects
 * link: https://www.paidmembershipspro.com/lock-down-everything-but-homepage-for-non-users/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_template_redirect() {
	global $current_user;

	$okay_pages = array( pmpro_getOption( 'billing_page_id' ), pmpro_getOption( 'account_page_id' ), pmpro_getOption( 'levels_page_id' ), pmpro_getOption( 'checkout_page_id' ), pmpro_getOption( 'confirmation_page_id' ) );

	// if the user doesn't have a membership, send them home
	if ( ! $current_user->ID
	   && ! is_home()
	   && ! is_page( $okay_pages )
	   && ! strpos( $_SERVER['REQUEST_URI'], 'login' ) ) {
		wp_redirect( home_url( 'wp-login.php?redirect_to=' . urlencode( $_SERVER['REQUEST_URI'] ) ) );
	} elseif ( is_page()
		   && ! is_home()
		   && ! is_page( $okay_pages )
		   && ! $current_user->membership_level->ID ) {
		wp_redirect( home_url() );
	}
}
add_action( 'template_redirect', 'my_template_redirect' );
