<?php
/**
 * Remove the "Home" breadcrumb from the Memberlite theme.
 *
 * title: Remove Home From Memberlite Breadcrumbs
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

function my_memberlite_hide_home_breadcrumb( $memberlite_hide_home_breadcrumb ) {
	return true;
}
add_filter( 'memberlite_hide_home_breadcrumb', 'my_memberlite_hide_home_breadcrumb' );
