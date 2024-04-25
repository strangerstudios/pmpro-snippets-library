<?php
/**
 * Cancel subscriptions when a recurring payment fails. Need for some gateways like Authorize.net.
 * 
 * title: Cancel subscriptions when a recurring payment fails.
 * layout: snippet
 * collection: payment-gateways
 * category: IP, anti-fraud
 * link: https://www.paidmembershipspro.com/automatically-cancel-membership-after-x-failed-payments/
 * 
 * This code recipe cancels a user's membership when a recurring payment fails. This is useful for gateways like Authorize.net that do not automatically cancel subscriptions when a payment fails.
 * Edit your billing failure email template to mention that users are cancelled upon failure. 
 * Note: This cancels on the first failure. To cancel only when cancelling at Stripe, see this gist: https://gist.github.com/strangerstudios/5093710	
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/*
  
*/
function my_pmpro_subscription_payment_failed($order)
{
	//cancel the membership
	pmpro_cancelMembershipLevel( $order->membership_id, $order->user_id );
}
add_action("pmpro_subscription_payment_failed", "my_pmpro_subscription_payment_failed");
