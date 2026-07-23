<?php
/**
 * Don't enlarge the "excerpt" content on single posts in Memberlite.
 *
 * title: Remove Enlarged Font for Memberlite Excerpt
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

function my_memberlite_excerpt_larger( $memberlite_excerpt_larger ) {
	return false;
}
add_filter( 'memberlite_excerpt_larger', 'my_memberlite_excerpt_larger' );
