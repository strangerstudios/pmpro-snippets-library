<?php
/**
 * Filter the body of the restricted message shown on protected content.
 * 
 * title: Filter the body of the restricted message shown on protected content.
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, non-member
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_custom_no_access_message_body( $body, $level_ids ) {
	$body = "<p><strong>You must have one of the following membership levels to access this content:</strong></p><p>!!levels!!</p>";
	return $body;
}
add_filter( 'pmpro_no_access_message_body', 'my_pmpro_custom_no_access_message_body', 10, 2 );
