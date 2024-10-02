<?php
/**
 * Allow PMPro Series to take new post types, such as a Custom Post Type.
 *
 * title: Allow Series take New Post Types
 * layout: snippet
 * collection: add-ons, pmpro-series
 * category: custom post types
 * url: https://www.paidmembershipspro.com/add-custom-post-types-and-pages-to-your-dripped-membership-series-content/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_add_post_type_to_series( $post_types ) {
	$post_types[] = 'my_cpt_name';

	return $post_types;
}
add_filter( 'pmpros_post_types', 'my_pmpro_add_post_type_to_series', 10, 1 );
