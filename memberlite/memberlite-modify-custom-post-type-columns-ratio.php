<?php
/**
 * Modify the Memberlite columns ratio and remove sidebars for a custom post type.
 * Adjust line 19 to match the custom post type(s) to adjust.
 *
 * title: Modify the Memberlite Columns Ratio and Sidebars for a Custom Post Type
 * layout: snippet
 * collection: memberlite
 * category: design
 *
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_memberlite_pmpro_courses_columns_ratio( $r, $location ) {
	if ( is_singular( array( 'pmpro_course', 'pmpro_lesson' ) ) ) {
		add_filter( 'memberlite_get_sidebar', '__return_false' );
		if ( empty( $location ) ) {
			$r = 12; // Set to 12 (no sidebar).
		}
	}
	return $r;
}
add_filter( 'memberlite_columns_ratio', 'my_memberlite_pmpro_courses_columns_ratio', 10, 2 );
