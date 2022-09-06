<?php
/**
 * Redirect non-members away from specific URLs.
 * Becareful, the code will redirect any URL *containing* 
 * the strings in the $urls array. E.g., if /not-locked/members/
 * is public and you have /members/ in the list, it will still
 * redirect non-members away from that URL.
 *
 * title: Redirect non-members away from specific URLs.
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, redirects
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_redirect_nonmembers() {
	// Make sure PMPro is active.
	if ( ! function_exists( 'pmpro_hasMembershipLevel' ) ) {
		return;
	}
	
	// Ignore members. Change to check for specific levels.
	if ( pmpro_hasMembershipLevel() ) {
		return;
	}
	
	// Ignore admins.
	if ( current_user_can( 'manage_options' ) ) {
		return;
	}
	
	// Update this array.
	$not_allowed = array(
		"/members/",
		"/groups/",
		"/groups/create/"
	);
	
	// Get the current URI.
	$uri = $_SERVER['REQUEST_URI'];
		
	// If we're on one of those URLs, redirect away.
	foreach( $not_allowed as $check ) {
		if( strpos( strtolower( $uri ), strtolower( $check ) ) !== false ) {
			// Go to levels page. Change if wanted.
			wp_redirect( pmpro_url( 'levels' ) );
			exit;
		}
	}
}
add_action( 'template_redirect', 'my_redirect_nonmembers' );