<?php
/**
 * Restrict PMPro email logging to specific recipient addresses.
 * Only emails sent to the specified addresses will be recorded in the log.
 * Useful for auditing or debugging specific members' communications.
 *
 * title: Log Emails for Specific Recipients in the PMPro Email Log
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
function my_pmpro_log_specific_recipients( $should_log, $email_data ) {
	// Add all recipients you want to audit.
	$recipients = array(
		'member@example.com',
		'admin@example.com',
		'test@example.com',
	);

	return in_array( $email_data['email_to'], $recipients, true );
}
add_filter( 'pmpro_should_log_email', 'my_pmpro_log_specific_recipients', 10, 2 );
