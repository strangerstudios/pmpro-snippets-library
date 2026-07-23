<?php
/**
 * Example of removing selectors from the default list for Memberlite primary background color elements.
 *
 * title: Remove Selector from Memberlite Defaults
 * layout: snippet
 * collection: memberlite
 * category: design
 * link: https://www.paidmembershipspro.com/add-remove-css-selector/
 *
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_memberlite_defaults_remove_selectors( $memberlite_defaults ) {
	$memberlite_defaults['color_primary_background_elements'] = '#mobile-navigation, #mobile-navigation-height-col, .btn_primary, .btn_primary:link, .menu-toggle, .bg_primary, .banner_primary, .has-color-primary-background-color';
	return $memberlite_defaults;
}
add_filter( 'memberlite_defaults', 'my_pmpro_memberlite_defaults_remove_selectors' );
