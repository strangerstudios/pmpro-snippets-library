<?php
/**
 * Show PMPro Pages in Site Search
 * 
 * title: Show PMPro Pages In Site Search
 * layout: snippet
 * collection: frontend-pages
 * category: search
 * link: https://www.paidmembershipspro.com/show-in-search/
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_remove_pmpro_search_filter_pmpro_pages() {
	remove_filter( 'pre_get_posts', 'pmpro_search_filter_pmpro_pages' );
}
add_action( 'init', 'my_remove_pmpro_search_filter_pmpro_pages', 9 );
