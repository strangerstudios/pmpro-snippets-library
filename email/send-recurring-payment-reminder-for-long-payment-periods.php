<?php
/**
 * Send recurring payment reminder when the last subscription payment was over 6 months ago.
 *
 * title: Send Recurring Payment Reminder Email for Long Payment Periods 
 * layout: snippet
 * collection: email
 * category: recurring reminders, emails
 * link: https://www.paidmembershipspro.com/adjust-recurring-payment-reminder-email-interval-template/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_only_send_recurring_email_for_long_payment_periods( $send_email, $subscription_obj ) {
	// If we are already not sending the email, don't bother checking.
	if ( ! $send_email ) {
		return $send_email;
	}

	// Get the most recent order for the subscription.
	$recent_orders = $subscription_obj->get_orders(
		array(
			'limit' => 1,
			'status' => 'success',
		)
	);
	if ( ! empty( $recent_orders ) ) {
		$recent_order = $recent_orders[0];

		// Get the timestamp for the subscription.
		$subscription_timestamp = $subscription_obj->get_next_payment_date( 'timestamp', false );

		// Get the timestamp 6 months before the next payment date.
		$notification_cutoff = strtotime( '-6 months', $subscription_timestamp );

		// Add two days to the notification cutoff to err on the side of caution RE timezones.
		$notification_cutoff = strtotime( '+2 days', $notification_cutoff );

		// If the oldest order is newer than the notification cutoff, don't send the email.
		if ( $recent_order->timestamp > $notification_cutoff ) {
			$send_email = false;
		}
	}

	return $send_email;
}
add_filter( 'pmpro_send_recurring_payment_reminder_email', 'my_pmpro_only_send_recurring_email_for_long_payment_periods', 10, 2 );