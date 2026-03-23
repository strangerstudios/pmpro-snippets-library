<?php
/**
 * Restrict PMPro email logging to a single recipient address.
 * Only emails sent to the specified address will be recorded in the log.
 * Useful for auditing or debugging a specific member's communications.
 *
 * title: Log Emails for a Specific Recipient in the PMPro Email Log
 * layout: snippet
 * collection: email
 * category: email-log
 * link: [add post URL after publish]
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_log_specific_recipient( $should_log, $email_data ) {
	// Replace with the email address you want to audit.
	$recipient = 'member@example.com';

	return ( $email_data['email_to'] === $recipient );
}
add_filter( 'pmpro_should_log_email', 'my_pmpro_log_specific_recipient', 10, 2 );
