<?php
/**
 * Add a "Manage Subscription at PayPal" link to the Membership Account > Memberships section for PayPal subscriptions.
 *
 * title: Add "Manage Subscription at PayPal" to Member Action Links
 * layout: snippet
 * collection: frontend-pages
 * category: members-links
 * link: https://www.paidmembershipspro.com/manage-subscription-at-paypal-action-link/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_member_action_links_add_paypal( $pmpro_member_action_links, $level_id ) {
	global $current_user;

	// Retrieve subscriptions for the current user and level.
	$subscriptions = PMPro_Subscription::get_subscriptions_for_user( $current_user->ID, $level_id );

	// Check if there are any subscriptions and use the first one.
	if ( ! empty( $subscriptions ) ) {
		$subscription = $subscriptions[0];

		// Check if the subscription is managed by PayPal.
		if ( $subscription->get_gateway() === 'paypalexpress' ) {
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
