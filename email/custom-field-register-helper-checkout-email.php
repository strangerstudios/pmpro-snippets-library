<?php
/**
 * This will check for !!company_name!! in the emails body and replace it with the 'company_name' metadata (created by Register Helper).
 * 
 * title: Add custom field data to checkout email via register helper
 * layout: snippet-example
 * collection: email
 * category: custom-fields
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
 
function my_pmpro_email_body( $body, $email ) {
	//only checkout emails
	if ( false !== strpos( $email->template, "checkout" ) ) {

		$user = get_user_by( 'email', $email->data['user_email'] );
		$company_name = get_user_meta( $user->ID, 'company', true );

		$body = str_replace( '!!company!!', $company_name, $body );
	}
 
	return $body;
}
add_filter( "pmpro_email_body", "my_pmpro_email_body", 10, 2 );
