<?php
/**
* Change Paid Memberships Pro Email Subjects
*
* title: Change PMPro Email Subjects
* layout: snippet
* collection: email
* category: email templates
* link: https://www.paidmembershipspro.com/changing-pmpro-email-subjects/
*
* You can add this recipe to your site by creating a custom plugin
* or using the Code Snippets plugin available for free in the WordPress repository.
* Read this companion article for step-by-step directions on either method.
* https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
*/
function my_pmpro_email_subject( $subject, $email ) {		
	//only checkout emails
	if ( $email->template == 'checkout_free' )	{
		$subject = 'Thank you, ' . $email->data["name"] . ', for using ' . get_bloginfo( 'name' );
	}

	return $subject;
}

add_filter( 'pmpro_email_subject', 'my_pmpro_email_subject', 10, 2 );
