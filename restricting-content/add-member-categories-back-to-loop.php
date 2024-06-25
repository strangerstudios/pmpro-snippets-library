<?php
/**
 * Add specific members-only categories back to main loop when filtering searches and archives
 *
 * title: Add specific members-only categories back to main loop when filtering searches and archives
 * layout: snippet
 * collection: restricting-content
 * category: content, categories
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_category_filter( $query ) {

	// Set the members-only category IDs NOT to filter. Update this!
	$not_hidden_cat_ids = array( 1, 10 );
	
	// If post__not_in is not set, bail.
	if ( empty( $query->get( 'post__not_in' ) ) ) {
		return;
	}	
	
	// Disable filters to avoid loops.
	remove_filter( 'pre_get_posts', 'pmpro_search_filter' );
	remove_filter( 'pre_get_posts', 'my_pmpro_category_filter' );

	// Static var to cache the posts IDs in these cats.
	static $cat_posts = null;
	
	// Get the IDs of all posts in those cats.
	if ( ! isset( $cat_posts ) ) {
		$cat_posts = get_posts( array( 'category' => $not_hidden_cat_ids, 'numberposts' => -1, 'fields' => 'ids' ) );
	}

	// Remove the post ids from the post__not_in query var.
	$query->set( 'post__not_in', array_diff( $query->get( 'post__not_in' ), $cat_posts ) );

	// Reenable filters.
	add_filter( 'pre_get_posts', 'pmpro_search_filter' );
	add_filter( 'pre_get_posts', 'my_pmpro_category_filter' );

	return $query;
}

$filterqueries = pmpro_getOption( 'filterqueries' );

if( ! empty( $filterqueries ) ){
	add_filter( 'pre_get_posts', 'my_pmpro_category_filter', 15 );
}
