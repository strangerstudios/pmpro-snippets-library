<?php
/**
 * 
 * title: Change body text of email according to level checkout
 * layout: snippet-example
 * collection: email
 * category: email
 * 
 * note: Assumes level ID is 1.
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
 
 function my_pmpro_email_body( $body, $email ) {

	if( $email->data[ 'membership_id' ] == "1" && strpos( $email->template, "checkout" ) !== false ) {
		$body = 'Change this text to desired text.';
	}
 
	return $body;
}
add_filter( "pmpro_email_body", "my_pmpro_email_body", 10, 2 );
