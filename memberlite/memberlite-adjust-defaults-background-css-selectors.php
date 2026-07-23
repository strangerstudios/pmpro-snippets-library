<?php
/**
 * Remove the Memberlite mobile navigation backgrounds from the primary background color defaults.
 *
 * title: Remove Memberlite Default Background CSS Selectors
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

function my_memberlite_custom_memberlite_defaults( $memberlite_defaults ) {
	$memberlite_defaults['color_primary_background_elements'] = '.masthead, .footer-widgets, .btn_primary, .btn_primary:link, .menu-toggle, .bg_primary, .banner_primary, .has-color-primary-background-color';
	return $memberlite_defaults;
}
add_filter( 'memberlite_defaults', 'my_memberlite_custom_memberlite_defaults' );
