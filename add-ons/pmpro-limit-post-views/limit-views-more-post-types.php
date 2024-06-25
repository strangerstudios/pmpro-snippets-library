<?php
/**
 * Add more post types to the Limit Post Views fuctionality.
 *
 * title: Add more post types to the Limit Post Views fuctionality.
 * layout: snippet
 * category: limit post views, banner
 * link: https://www.paidmembershipspro.com/add-ons/pmpro-limit-post-views/#post-types
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmprolpv_add_my_post_types( $post_types ) {
	// Add the page post type to Limit Post Views logic.
	$post_types[] = 'page';
	// $post_types[] = 'custom-post-type'; /* Example of how you can add more $post_types */

	return $post_types;	
}
add_filter( 'pmprolpv_post_types', 'my_pmprolpv_add_my_post_types', 10 );
