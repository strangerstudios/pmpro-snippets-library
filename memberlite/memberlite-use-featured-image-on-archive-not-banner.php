<?php
/**
 * Remove the Memberlite custom banner image filter conditionally on the blog homepage.
 * Your blog entries will then show the featured image instead of the banner image.
 *
 * title: Use Featured Image on Archive entires not the Banner image.
 * layout: snippet
 * collection: memberlite
 * category: display, featured image
 * link: tbd
 * 
 * This recipe prevents the Memberlite theme from replacing the banner image on the homepage
 * with the custom level banner image set for membership levels.
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method:
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_memberliteconditionally_remove_memberlite_banner_image() {
	if ( is_home() ) {
		remove_filter( 'memberlite_get_banner_image', 'memberlite_maybe_get_custom_banner_image', 10 );
	}
}
add_action( 'template_redirect', 'my_memberliteconditionally_remove_memberlite_banner_image' );

