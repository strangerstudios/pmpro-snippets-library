<?php
/**
 * Change the PMPro Admin Email recipient and optionally add a BCC for all admin-related emails.
 *
 * title: Change Admin Email Recipient and Add BCC
 * layout: snippet-example
 * collection: email-notifications
 * category: admin-emails
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * SETTINGS
 */
define( 'MY_PMPRO_ADMIN_EMAIL_TO', 'memberadmin@someemail.co' ); // Set to the desired admin email recipient.
// define( 'MY_PMPRO_ADMIN_EMAIL_BCC', 'bccaddress@someemail.co' ); // Uncomment and set to add a BCC address.

function my_pmpro_change_admin_email_recipients( $user_email, $email ) {
	// Only target admin-related PMPro emails.
	if ( strpos( $email->template, '_admin' ) === false ) {
		return $user_email;
	}

	return MY_PMPRO_ADMIN_EMAIL_TO;
}
add_filter( 'pmpro_email_recipient', 'my_pmpro_change_admin_email_recipients', 10, 2 );

function my_pmpro_add_admin_email_bcc( $headers, $email ) {
	// Only target admin-related PMPro emails.
	if ( strpos( $email->template, '_admin' ) === false ) {
		return $headers;
	}

	// Skip if BCC is not defined.
	if ( ! defined( 'MY_PMPRO_ADMIN_EMAIL_BCC' ) ) {
		return $headers;
	}

	$headers[] = 'Bcc: ' . MY_PMPRO_ADMIN_EMAIL_BCC;
	return $headers;
}
add_filter( 'pmpro_email_headers', 'my_pmpro_add_admin_email_bcc', 10, 2 );