<?php
/**
 * Redirect WooCommerce IPN requests to the PMPro IPN handler as well.
 *
 * This is useful if you use WooCommerce with PayPal and also use PMPro with PayPal.
 * PayPal can only send IPN notifications to one URL, so use this snippet on a WooCommerce
 * site to also pass the IPN to PMPro for processing.
 *
 * title: Redirect WooCommerce PayPal IPN Requests to PMPro
 * layout: snippet
 * collection: payment-gateways/paypal
 * category: paypal, ipn, woocommerce
 * link: https://www.paidmembershipspro.com/redirect-another-ecommerce-paypal-ipn-to-pmpro/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_ipn_redirect() {
	if ( ! empty( $_REQUEST['wc-api'] ) && defined( 'PMPRO_DIR' ) ) {
		require_once( PMPRO_DIR . '/services/ipnhandler.php' );
		exit;
	}
}
add_action( 'init', 'my_ipn_redirect' );
