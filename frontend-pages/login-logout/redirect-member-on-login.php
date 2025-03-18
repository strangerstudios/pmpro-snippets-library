<?php
/**
 * Redirect members to a specific page when logging in.
 * 
 * title: Redirect members on login.
 * layout: snippet
 * collection: misc
 * category: login, redirect
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_login_redirect_url( $redirect_to, $request, $user ) {
	//if logged in and a member, send to members page
	if ( pmpro_hasMembershipLevel( NULL, $user->ID ) ) {
		$redirect_to = '/members/';
    } 

    return $redirect_to;
}
add_filter( 'pmpro_login_redirect_url', 'my_pmpro_login_redirect_url', 10, 3 );
