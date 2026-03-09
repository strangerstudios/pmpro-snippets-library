<?php
/**
 * Change the statement descriptor for Stripe charges.
 * This is the message shown on the customer's credit card statement.
 *
 * Note: If your descriptor includes invalid characters or
 * more than 22 characters, checkout will fail. See:
 * https://docs.stripe.com/get-started/account/statement-descriptors#requirements
 *
 * title: Set a Unique Statement Descriptor for Payments Through the Stripe Gateway
 * layout: snippet
 * collection: payment-gateways, stripe
 * category: libraries
 * link: https://www.paidmembershipspro.com/set-a-unique-statement-descriptor-for-payments-through-the-stripe/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_update_stripe_statement_descriptor( $params, $order, $customer = null ) {
	// Your statement descriptor.
	$statement_descriptor = 'Business - Product';

	if ( null !== $customer ) {
		// Set the descriptor for one-time payments via Stripe Checkout.
		if ( isset( $params['mode'] ) && 'payment' === $params['mode'] ) {
			if ( ! isset( $params['payment_intent_data'] ) || ! is_array( $params['payment_intent_data'] ) ) {
				$params['payment_intent_data'] = array();
			}
			$params['payment_intent_data']['statement_descriptor'] = $statement_descriptor;
		}
	} else {
		// Set the descriptor for onsite payments.
		$params['statement_descriptor'] = $statement_descriptor;
	}

	return $params;
}
add_filter( 'pmpro_stripe_payment_intent_params', 'my_pmpro_update_stripe_statement_descriptor', 10, 2 );
add_filter( 'pmpro_stripe_checkout_session_parameters', 'my_pmpro_update_stripe_statement_descriptor', 10, 3 );
