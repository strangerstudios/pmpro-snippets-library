<?php
/**
 * BCC Admin Notifications Based on Membership Level
 * 
 * Change line 25 to the level you want to make this change too.
 * Change line 26 Change email address for your BCC address
 *
 * title: BCC Admin Notifications Based on Membership Level
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

function my_pmpro_email_headers( $headers, $email ) {
	// Set default BCC address to the site admin email.
	$admin_email = get_bloginfo( 'admin_email' );

	// Override BCC address for specific membership level.
	if ( intval( $email->data['membership_id'] ) === 5 ) {
		$admin_email = 'someemail@email.com';

		// Example: send to multiple emails (comma-separated list).
		// $admin_email = 'first@email.com, second@email.com, third@email.com';
	}

	// Add BCC only if the email isn't already going to the admin.
	if ( $email->email !== get_bloginfo( 'admin_email' ) ) {
		$headers[] = 'Bcc: ' . $admin_email;
	}

	return $headers;
}
add_filter( 'pmpro_email_headers', 'my_pmpro_email_headers', 10, 2 );