<?php
/**
 * Override the starting location for the Membership Map
 *
 * title: Override the default start location for the Membership Map
 * layout: snippet
 * collection: pmpro-member-directory
 * category: maps
 * link: https://www.paidmembershipspro.com/overriding-the-pmpro-membership-map-default-starting-point/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method:
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

// Override the default map start coordinates for a specific PMPro Map.
add_filter( 'pmpromd_override_first_marker', '__return_true');

// Override the default map start coordinates for a specific map.
function mypmromd_override_default_map_start( $coordinates, $map_id ) {
	$coordinates = array(
		'lat' => -34.397,
		'lng' => 150.644,
	);

	return $coordinates;
}
add_filter( 'pmpromd_default_map_start', 'mypmromd_override_default_map_start', 10, 2 );
