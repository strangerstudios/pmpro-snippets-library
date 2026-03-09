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

function pmpro_snippet_hide_free_levels_from_levels_array( $levels ) {
	if ( empty( $levels ) || ! is_array( $levels ) ) {
		return $levels;
	}

	$levels = array_filter(
		$levels,
		static function( $level ) {
			return ! pmpro_isLevelFree( $level );
		}
	);

	// Re-index the array (optional, but keeps output tidy).
	return array_values( $levels );
}
add_filter( 'pmpro_levels_array', 'pmpro_snippet_hide_free_levels_from_levels_array' );