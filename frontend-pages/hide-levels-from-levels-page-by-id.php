<?php
/**
 * Hide levels from the level select page based off specific level ID's.
 *
 * title: Hiding Specific Levels (by ID) from the Membership Levels Display
 * layout: snippet
 * collection: frontend-pages
 * category: levels, level-page
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_levels_array( $levels ) {
	// a comma-separated list of the levels to hide.
	$hiddenlevels = array( 1, 3 );

	// build the filtered levels array.
	$newlevels = array();
	foreach ( $levels as $key => $level ) {
		if ( ! in_array( $level->id, $hiddenlevels ) ) {
			$newlevels[ $key ] = $level;
		}
	}
	return $newlevels;
}
add_filter( 'pmpro_levels_array', 'my_pmpro_levels_array' );
