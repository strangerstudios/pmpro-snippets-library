<?php
/**
 * Add Australian GST to your membership site
 *
 * title: Custom tax structure where all levels except level 1 have 7.25% tax if billing state is CA.
 * layout: snippet
 * collection: checkout
 * category: tax, AU
 * link: https://www.paidmembershipspro.com/australian-gst/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

//Update line 21 below to your preferred dynamic meta description.
function my_pmpro_filter_wpseo_metadesc($description) {
	global $pmpro_pages, $pmpro_level;
    if( is_page($pmpro_pages['checkout']) ) {
		  $description = $pmpro_level->description;
    }
    return $description;	
}
add_filter('wpseo_metadesc', 'my_pmpro_filter_wpseo_metadesc');

