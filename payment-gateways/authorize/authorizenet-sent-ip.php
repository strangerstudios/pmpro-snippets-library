<?php
/**
 * Send user's IP to authorize.net for fraud detection.
 *
 * title: Send user's IP to authorize.net for fraud detection.
 * layout: snippet
 * collection: payment-gateways, authorize.net
 * category: IP
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function my_pmpro_authorizenet_post_values_send_ip( $post_values ) {
   $post_values['x_customer_ip'] = $_SERVER['REMOTE_ADDR'];
   return $post_values;
 }
 add_filter( 'pmpro_authorizenet_post_values', 'my_pmpro_authorizenet_post_values_send_ip' );
