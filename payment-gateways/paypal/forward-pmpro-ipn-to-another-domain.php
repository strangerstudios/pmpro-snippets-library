<?php
/**
 *  Forward PMPro PayPal IPNs to another domain.
 * 
 * Each domain will process the IPNs. The IPN handlers should be setup to ignore
 * messages that aren't for that site. PMPro does this.
 * This is useful if you have 2 different sites using the same PayPal account
 * and the IPN is setup to go to a PMPro site.
 * Add this to a custom plugin on the PMPro site the IPN hits.
 * Update the domain/url to the IPN you want to forward to.
 * The pmprodev_gateway_debug_setup check makes sure this DOESN'T run if you have the
 * PMPro Toolkit plugin enabled, i.e. you are on a staging site.
 * 
 * 
 * 
 * title: Add custom field to checkout
 * layout: snippet-example
 * collection: payment-gateways/paypal
 * category: paypal, ipn
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_forward_ipn() {
	if ( ! function_exists( 'pmprodev_gateway_debug_setup' ) ) {
		$params = array(
			'body'        => $_POST,
			'sslverify'   => false,
			'timeout'     => 60,
			'httpversion' => '1.1',
			'compress'    => false,
			'decompress'  => false,
			'user-agent'  => 'paypal-ipn/',
		);

		wp_safe_remote_post(
			'https://yourdomain.com/wp-admin/admin-ajax.php?action=ipnhandler', //Change this URL to the URL you want to receive your IPN request data.
			$params
		);
	}
}

add_action( 'wp_ajax_nopriv_ipnhandler', 'my_pmpro_forward_ipn', 5 );
add_action( 'wp_ajax_ipnhandler', 'my_pmpro_forward_ipn', 5 );
