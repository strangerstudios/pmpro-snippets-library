<?php
/**
 * Send additional recurring payment reminder emails during the scheduled cron.
 *
 * title: Send Additional Recurring Payment Reminder Emails
 * layout: snippet
 * collection: email
 * category: cron jobs, emails
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_upcoming_recurring_payment_reminder( $emails ) {
	$emails = array(
		30	=> 'membership_recurring',
		3	=> 'membership_recurring',
	);
	return $emails;
}
add_filter( 'pmpro_upcoming_recurring_payment_reminder', 'my_pmpro_upcoming_recurring_payment_reminder', 10 );
