<?php
/**
 * Set a the default header and body font for the Memberlite theme.
 * Note: These fonts must be one of the available named fonts in the theme.
 *
 * title: Set a Default Header and Body Font for Memberlite
 * layout: snippet
 * collection: memberlite
 * category: design
 *
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_theme_mod_memberlite_webfonts( $fonts ) {
	return 'Open-Sans_Inter';
}
add_filter( 'theme_mod_memberlite_webfonts', 'my_theme_mod_memberlite_webfonts' );
