<?php
/**
 * Change recurring payment reminder emails from 7 days to other.
 *
 * title: Change Additional Recurring Payment Reminder Emails from 7 days to other
 * layout: snippet
 * collection: email
 * category: recurring reminder, emails
 * link: https://www.paidmembershipspro.com/recurring-payment-reminder-emails/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_change_recurring_emails( $emails ) {

	// Remove the email that is sent 7 days before subscription payment.
	unset( $emails[7] );
  
	// Change to 14 to send a reminder email to how many days before the subscription payment.
	// The email template to be used is 'membership_recurring'.
	$emails[14] = 'membership_recurring';
	return $emails;
}
add_filter( 'pmpro_upcoming_recurring_payment_reminder', 'my_pmpro_change_recurring_emails', 10, 1 );
