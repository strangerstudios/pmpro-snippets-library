<?php
/**
 * Set the locale for the Stripe Checkout session so that the user checks out
 * in Stripe Checkout in the locale set in their user settings or the site locale
 * if the user has no locale set.
 *
 * title: Set the locale for the Stripe Checkout session.
 * layout: snippet
 * collection: payment-gateways, stripe
 * category: libraries
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_stripe_checkout_parameters_user_locale( $params, $order, $customer ) {
	// Get the User Locale or WordPress Locale.
	$wp_locale = get_user_locale( $order->user_id ); // defaults to site locale if user has no locale set.

	// Convert WordPress locale to Stripe locale.
	$stripe_locale    = strtolower( substr( $wp_locale, 0, 2 ) ); // Example: convert 'pl_PL' to 'pl'.
	$params['locale'] = $stripe_locale; // Add the locale as a Stripe Checkout parameter.
	return $params;
}
add_filter('pmpro_stripe_checkout_session_parameters', 'my_pmpro_stripe_checkout_parameters_user_locale', 10, 3);