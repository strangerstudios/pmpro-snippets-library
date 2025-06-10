<?php
/**
 * Example customizations plugin file that enqueues a stylesheet for Paid Memberships Pro.
 *
 * title: Example Customizations Plugin With Stylesheet
 * layout: snippet
 * collection: misc
 * category: customizations
 * link: https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/*
Plugin Name: PMPro Customizations
Plugin URI: https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
Description: Customizations for my Paid Memberships Pro Setup
Version: 1.0.0
Author: My Website
Author URI: https://www.mywebsite.com
*/

// Now start placing your customization code below this line

define( 'PMPRO_CUSTOMIZATIONS_VERSION', '1.0.0' );

/**
 * Enqueue a custom stylesheet for Paid Memberships Pro.
 */
function pmpro_customizations_enqueue_styles() {
	// Enqueue the custom stylesheet.
	wp_enqueue_style( 'pmpro-customizations-styles', plugin_dir_url( __FILE__ ) . 'css/pmpro-customizations.css', array( ), PMPRO_CUSTOMIZATIONS_VERSION );
}
add_action( 'wp_enqueue_scripts', 'pmpro_customizations_enqueue_styles' );
