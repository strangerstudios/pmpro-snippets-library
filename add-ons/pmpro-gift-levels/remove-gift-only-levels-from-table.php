<?php
/**
 * This recipe removes levels you can get with a gift code only from the levels table
 *
 * title: Remove gift-only levels from table
 * layout: snippet
 * collection: add-ons
 * category: pmpro-gift-levels
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Remove gift-only levels from table
 */
function my_pmpro_remove_gift_only_levels_from_table( $levels ) {
	global $pmprogl_require_gift_code;

	if ( is_array( $pmprogl_require_gift_code ) ) {
		foreach ( $pmprogl_require_gift_code as $gift_level_id ) {
			unset( $levels[ $gift_level_id ] );
		}
	}

	return $levels;
}

add_filter( 'pmpro_levels_array', 'my_pmpro_remove_gift_only_levels_from_table' );
