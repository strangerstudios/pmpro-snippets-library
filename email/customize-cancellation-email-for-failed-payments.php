<?php
/**
 * Add a {{ cancelled_due_to_payment_failure }} email template variable to the
 * PMPro Cancel emails so you can vary the wording with Liquid syntax when a
 * membership was cancelled because a recurring payment failed.
 * Requires Paid Memberships Pro v3.7+ for Liquid syntax in email templates.
 *
 * Usage in Memberships > Settings > Email Templates > Cancel:
 *
 *   {% if cancelled_due_to_payment_failure %}
 *   Content for members cancelled due to payment failure.
 *   {% else %}
 *   Content for members who initiated cancellation.
 *   {% endif %}
 *
 * Gateway support: needs a gateway that uses PMPro's built-in handling for
 * failed recurring payments, such as Stripe and PayPal. Members on gateways
 * that handle failed payments their own way always get the {% else %} content.
 *
 * title: Customize the Cancellation Email for Memberships Cancelled Due to Failed Payments
 * layout: snippet
 * collection: email
 * category: emails, email templates
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Was this membership cancelled by the gateway after a payment failed?
 *
 * @param int $user_id  The ID of the member.
 * @param int $level_id The cancelled membership level ID.
 * @return bool Whether the cancellation was caused by a failed payment.
 */
function my_pmpro_cancelled_due_to_payment_failure( $user_id, $level_id ) {
	if ( ! class_exists( 'PMPro_Subscription' ) ) {
		return false;
	}

	// Bail if the cancellation was admin/member initiated.
	if ( is_user_logged_in() ) {
		return false;
	}

	// Get the most recent subscription for the cancelled level.
	$subscriptions = PMPro_Subscription::get_subscriptions_for_user(
		$user_id,
		$level_id,
		array( 'active', 'cancelled' )
	);
	if ( empty( $subscriptions ) ) {
		return false;
	}
	$subscription = current( $subscriptions );

	// A failed payment leaves a pending order. Cancelling then flips it to error
	// in PMPro_Subscription::save(), before the cancel email, so accept both.
	$failed_orders = $subscription->get_orders( array( 'status' => array( 'pending', 'error' ) ) );

	foreach ( $failed_orders as $order ) {
		// Only the failure handler writes this meta.
		// A retry reuses the order, so one still incomplete is assumed unresolved.
		if ( get_pmpro_membership_order_meta( $order->id, 'last_failure_email_sent', true ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Add a {{ cancelled_due_to_payment_failure }} variable to the cancel emails.
 *
 * @param array      $data  The email template variables.
 * @param PMProEmail $email The email object being sent.
 * @return array The filtered email template variables.
 */
function my_pmpro_add_payment_failure_email_variable( $data, $email ) {
	// Only affect the cancel emails. Remove 'cancel_admin' if you only want to change the email sent to the member.
	if ( empty( $email->template ) || ! in_array( $email->template, array( 'cancel', 'cancel_admin' ), true ) ) {
		return $data;
	}

	// Default to empty so the variable always exists and never falls through to usermeta lookup.
	$data['cancelled_due_to_payment_failure'] = '';

	// Bail if there is no membership level to check.
	if ( empty( $data['membership_id'] ) ) {
		return $data;
	}

	// Resolve the user from the email data, not the recipient address.
	if ( empty( $data['user_login'] ) ) {
		return $data;
	}
	$user = get_user_by( 'login', $data['user_login'] );
	if ( empty( $user ) ) {
		return $data;
	}

	if ( my_pmpro_cancelled_due_to_payment_failure( $user->ID, (int) $data['membership_id'] ) ) {
		$data['cancelled_due_to_payment_failure'] = '1';
	}

	return $data;
}
add_filter( 'pmpro_email_data', 'my_pmpro_add_payment_failure_email_variable', 10, 2 );
