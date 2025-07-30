<?php
/**
 * Disable the PMPro SSL redirect logic.
 *
 * title: Completely disable the SSL redirect
 * layout: snippet
 * collection: misc
 * category: checkout, ssl
 * link: https://www.paidmembershipspro.com/debugging-https-ssl-issues/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_disable_https_redirect() {
	remove_action( 'wp', 'pmpro_besecure', 2 );
	remove_action( 'login_init', 'pmpro_besecure', 2 );
}
add_action( 'init', 'my_pmpro_disable_https_redirect', 20 );
