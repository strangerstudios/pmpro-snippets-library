<?php
/**
 * Modify the columns ratio for the blog, archives, and posts pages.
 * Use this recipe to have a different sidebar width on your pages and posts.
 *
 * title: Modify the Memberlite Columns Ratio for Blog, Archive, and Posts Pages
 * layout: snippet
 * collection: memberlite
 * category: design
 * link: TBD
 *
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_memberlite_modify_columns_ratio_for_blog( $r, $location ) {
	if ( ! function_exists( 'memberlite_is_blog' ) ) {
		return $r;
	}

	// Check if the current page is a blog page
	$maybe_add_sidebar = memberlite_is_blog();

	if ( $maybe_add_sidebar && ! in_array( $location, array( 'header-right', 'header-left', 'masthead' ), true ) ) {
		// Set layout ratio for the blog main area and sidebar
		$r = ( $location == 'sidebar' ) ? '4' : '8';
	}

	return $r;
}
add_filter( 'memberlite_columns_ratio', 'my_memberlite_modify_columns_ratio_for_blog', 15, 2 );
