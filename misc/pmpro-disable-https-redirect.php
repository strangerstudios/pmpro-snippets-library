<?php
/**
 * Completely disable the SSL redirect
 *
 * title: Completely disable the SSL redirect
 * layout: snippet
 * collection: misc
 * category: checkout, ssl
 *
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Add the following plugin header and code to a blank PHP file.
 */

/*
Plugin Name: PMPro Disable HTTP/HTTPS Redirect
Plugin URI: http://www.paidmembershipspro.com/wp/pmpro-disable-https-redirect/
Description: Disables the function in PMPro that redirects HTTP to HTTPS and vice versa.
Version: 1.0
Author: Stranger Studios
Author URI: http://www.strangerstudios.com
*/

function my_pmpro_disable_https_redirect(){
    
    remove_action( 'wp', 'pmpro_besecure', 2 );
	remove_action( 'login_init', 'pmpro_besecure', 2 );

}
add_action( "init", "my_pmpro_disable_https_redirect", 20 );