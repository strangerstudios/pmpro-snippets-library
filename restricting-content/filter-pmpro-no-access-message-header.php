<?php
/**
 * Filter the header of the restricted message shown on protected content.
 * 
 * title: Filter the header of the restricted message shown on protected content.
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, non-member
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_custom_no_access_message_header( $header, $level_ids ) {
	$header = "Join The Must Love Dogs Community For Access";
	return $header;
}
add_filter( 'pmpro_no_access_message_header', 'my_pmpro_custom_no_access_message_header', 10, 2 );
