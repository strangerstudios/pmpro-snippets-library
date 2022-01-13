<?php
/**
 * Filter what level options may be selected based on the current member's active level. 
 * This example allows you to show/hide specific levels on the Membership Levels page.
 * 
 * title: Dynamically Display Certain Levels Based on the Current User’s Active Level
 * layout: snippet
 * collection: frontend-pages
 * category: levels, level-page
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function dynamic_pmpro_levels_array( $levels ) {	
	// Get all the levels
	$levels = pmpro_getAllLevels( false, true );
	
	// remove levels 1, 5, 6, and 7 user has level 1.
	if ( pmpro_hasMembershipLevel( '1' ) ) {
		unset( $levels['1'] );
		unset( $levels['5'] );
		unset( $levels['6'] );
		unset( $levels['7'] );
	}
	// remove levels 1, 2, 3, and 4 if user has level 2.
	if ( pmpro_hasMembershipLevel( '2' ) ) {
		unset( $levels['1'] );
		unset( $levels['2'] );
		unset( $levels['3'] );
		unset( $levels['4'] );
	}
	// only show levels 1 and 2 if user has no level.
	if ( ! pmpro_hasMembershipLevel( ) ) {
		unset( $levels['3'] );
		unset( $levels['4'] );
		unset( $levels['5'] );
		unset( $levels['6'] );
		unset( $levels['7'] );
	}	
	return $levels;
}
add_filter( 'pmpro_levels_array', 'dynamic_pmpro_levels_array', 10, 2 );