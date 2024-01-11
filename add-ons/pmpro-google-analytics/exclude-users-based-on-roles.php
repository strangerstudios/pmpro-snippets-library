<?php
/**
 * Exclude Users With Editor or Author Role From Tracking from  the Google Analytics Integration tracking
 * title: Capture Unique Post Data as Custom Dimensions Using the Google Analytics Integration Add On
 * layout: snippet
 * collection: add-ons, pmpro-google-analytics
 * category: users
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Determine whether or not current user has editor or author role.
 *
 * @return bool true if user has the roles and false otherwise.
 */
function my_custom_dont_track_function() {
	//The roles to hide. Change as needed.
	$roles_to_hide = array( 'editor', 'author' );
	//Get the current user
	$current_user = wp_get_current_user();
	//Get current user roles.
	$roles = ( array ) $current_user->roles;

	return !empty( array_intersect($roles, $roles_to_hide) );
}

add_filter( 'pmproga4_dont_track', 'my_custom_dont_track_function' );