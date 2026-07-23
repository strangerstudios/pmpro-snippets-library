<?php
/**
 * Remove the "Category" or "Archive" prefix from the masthead title on Memberlite category and tag archive pages.
 * Add other post type archives or taxonomy labels to the array in the function.
 * If your site is in a language other than English, you may need to adjust these values.
 *
 * title: Remove the Category or Archive Prefix from Memberlite Masthead
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

function my_memberlite_get_the_archive_title_prefix( $prefix ) {
	if ( in_array( $prefix, array( 'Archives:', 'Category:' ) ) ) {
		$prefix = '';
	}
	return $prefix;
}
add_filter( 'get_the_archive_title_prefix', 'my_memberlite_get_the_archive_title_prefix' );