<?php
/**
 * Add a "Manage Subscription at PayPal" link to the Membership Account page
 * and the PMPro Billing page for subscriptions managed by the PMPro PayPal gateway.
 *
 * title: Add "Manage Subscription at PayPal" Link to Account and Billing Pages
 * layout: snippet
 * collection: add-ons/pmpro-paypal
 * category: pmpro-paypal, account-page, billing-page
 * link: https://www.paidmembershipspro.com/manage-subscription-at-paypal-action-link/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Add the "Manage Subscription at PayPal" link to the member action links on
 * the Membership Account page and the Billing page memberships section.
 */
function my_pmpro_member_action_links_add_paypal( $pmpro_member_action_links, $level_id ) {
	global $current_user;

	// Retrieve subscriptions for the current user and level.
	$subscriptions = PMPro_Subscription::get_subscriptions_for_user( $current_user->ID, $level_id );

	// Check if there are any subscriptions and use the first one.
	if ( ! empty( $subscriptions ) ) {
		$subscription = $subscriptions[0];

		// Check if the subscription is managed by the PMPro PayPal gateway.
		if ( $subscription->get_gateway() === 'paypal' ) {
			$pmpro_member_action_links['paypal'] = sprintf(
				'<a id="pmpro_actionlink-paypal" href="%s" target="_blank">%s</a>',
				esc_url( 'https://www.paypal.com/myaccount/autopay/' ),
				esc_html__( 'Manage Subscription at PayPal', 'pmpro-snippets-library' )
			);
		}
	}

	return $pmpro_member_action_links;
}
add_filter( 'pmpro_member_action_links', 'my_pmpro_member_action_links_add_paypal', 10, 2 );

/**
 * Add the "Manage Subscription at PayPal" link to the subscription info card
 * on the Billing page. This hook is used because the billing update form is not
 * shown for PayPal subscriptions (the gateway does not support payment_method_updates).
 * Output is an <li> because pmpro_billing_bullets_top fires inside a <ul>.
 */
function my_pmpro_billing_bullets_top_paypal_link() {
	global $pmpro_billing_subscription;

	if ( empty( $pmpro_billing_subscription ) ) {
		return;
	}

	if ( $pmpro_billing_subscription->get_gateway() !== 'paypal' ) {
		return;
	}

	printf(
		'<li><a href="%s" target="_blank">%s</a></li>',
		esc_url( 'https://www.paypal.com/myaccount/autopay/' ),
		esc_html__( 'Manage Subscription at PayPal', 'pmpro-snippets-library' )
	);
}
add_action( 'pmpro_billing_bullets_top', 'my_pmpro_billing_bullets_top_paypal_link' );
