<?php
/**
 * Exclude additional user roles from tracking when using the PMPro Google Analytics Integration.
 *
 * title: Exclude Additional User Roles from Tracking Using the PMPro Google Analytics Integration.
 * layout: snippet
 * collection: add-ons, pmpro-google-analytics
 * category: analytics
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_custom_pmproga4_dont_track_by_role() {
	// The roles to hide. Change this line to add other named roles.
	$roles_to_hide = array( 'editor', 'author' );

	// Get the current user
	$current_user = wp_get_current_user();

	// Get current user roles.
	$roles = ( array ) $current_user->roles;

	return ! empty( array_intersect( $roles, $roles_to_hide ) );
}
add_filter( 'pmproga4_dont_track', 'my_custom_pmproga4_dont_track_by_role' );
