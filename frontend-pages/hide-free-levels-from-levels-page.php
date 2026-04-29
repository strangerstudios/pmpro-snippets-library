<?php
/**
 * Remove free membership levels from the levels array used by PMPro.
 *
 * title: Hide Free Levels in PMPro Levels Page
 * layout: snippet
 * collection: frontend-pages
 * category: levels, level-page
 * link: https://www.paidmembershipspro.com/hide-free-levels-from-the-membership-levels-page/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_hide_free_levels_from_levels_array( $levels ) {
	// No levels or not an array, return early.
	if ( empty( $levels ) || ! is_array( $levels ) ) {
		return $levels;
	}
	
	$newlevels = array();
	foreach ( $levels as $level ) {
		if ( ! pmpro_isLevelFree( $level ) ) {
			$newlevels[] = $level;
		}
	}
	return $newlevels;
}
add_filter( 'pmpro_levels_array', 'my_pmpro_hide_free_levels_from_levels_array' );