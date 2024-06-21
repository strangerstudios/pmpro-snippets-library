<?php
/**
 * Lockdown an entire site except for the home page
 *
 * title: Lockdown an entire site except for the home page
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, redirects
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_template_redirect_lock_entire_site(){
	// If the user has a membership level, we don't need to lock down the site.
	if ( ! function_exists( 'pmpro_hasMembershipLevel' ) || pmpro_hasMembershipLevel() ) {
		return;
	}

	// If the user is on an $okay_pages page, the home page, or the login page, we dont' need to redirect.
	$okay_pages = array(
		pmpro_getOption( 'billing_page_id' ), 
		pmpro_getOption( 'account_page_id' ), 
		pmpro_getOption( 'levels_page_id' ), 
		pmpro_getOption( 'checkout_page_id' ), 
		pmpro_getOption( 'confirmation_page_id' )
    );
	if ( is_home() || is_page( $okay_pages ) || strpos( $_SERVER['REQUEST_URI'], "login" ) ) {
		return;
	}
	
	// 	The user needs to be redirected.
	if( is_user_logged_in() ) {
		wp_redirect( home_url() );
	} else {		
		wp_redirect( home_url( "wp-login.php?redirect_to=" . urlencode( $_SERVER['REQUEST_URI'] ) ) );
	}	
	exit;
}
add_action( 'template_redirect', 'my_template_redirect_lock_entire_site' );
