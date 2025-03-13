<?php
/**
 * BCC Additional Email Addresses on Member or Admin Notifications
 * This will allow you to BCC additional email addresses for member and admin notifications.
 *
 * Ensure line 24 is changed to your preferred BCC email address
 *
 * title: BCC Additional Email Addresses on Member or Admin Notifications
 * layout: snippet
 * collection: email
 * category: bcc
 * link: https://www.paidmembershipspro.com/bcc-additional-email-addresses-on-member-or-admin-notifications/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_email_headers_admin_emails( $headers, $email ) {

	// BCC emails already going to admin_email.
	if ( strpos( $email->template, '_admin' ) !== false ) {
		$headers[] = 'Bcc:' . 'otheremail@domain.com';
		// $headers[] = 'Bcc:' . 'otheremail@domain.com,two@domain.com,three@domain.com'; //Example with multiple BCC emails
	}

	return $headers;
}
add_filter( 'pmpro_email_headers', 'my_pmpro_email_headers_admin_emails', 10, 2 );
