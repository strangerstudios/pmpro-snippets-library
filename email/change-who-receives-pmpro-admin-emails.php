<?php
/**
 * Change the recipient for all admin-related emails in Paid Memberships Pro.
 *
 * title: Change Admin Email Recipient for PMPro Admin emails.
 * layout: snippet
 * collection: email
 * category: admin
 * link: https://www.paidmembershipspro.com/change-pmpro-admin-email-recipient/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_change_admin_email_recipients( $user_email, $email ) {
	if ( strpos( $email->template, "_admin" ) !== false ) {
		$user_email = 'memberadmin@someemail.co'; // Change your email address here.
    }

	return $user_email;
}
add_filter( 'pmpro_email_recipient', 'my_pmpro_change_admin_email_recipients', 10, 2 );
