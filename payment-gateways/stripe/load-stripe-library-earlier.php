<?php

/**
 * An updated version of the load Stripe Library version code earlier than other third-party plugins
 * This should work with PMPro Version 2.10 and above
 * Learn more at https://www.paidmembershipspro.com/fix-library-conflict/
 *
 * title: Load Stripe Library Earlier
 * layout: snippet
 * collection: payment-gateways, stripe
 * category: libraries
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_load_pmpro_stripe_library_early() {
	if ( defined( 'PMPRO_DIR' ) ) {
		require_once( PMPRO_DIR . "/includes/lib/Stripe/init.php" );
	}
}
add_action( 'init', 'my_load_pmpro_stripe_library_early', 0 );
