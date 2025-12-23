<?php
/**
 * This recipe changes the MAXFAILEDPAYMENTS limit for PayPal Express
 *
 * 
 * title: Change MAXFAILEDPAYMENTS Limit for PayPal Express
 * layout: snippet
 * collection: payment-gateways/paypal
 * category: paypal, recurring
 * link: https://www.paidmembershipspro.com/automatically-cancel-membership-after-x-failed-payments/
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function mypmpro_max_failed_payments_adjust( $nvpStr, $order ){

    // Change MAXFAILEDPAYMENTS from default 1 to ?, e.g., 4
    // Note: PayPal allows a maximum value of 999
	$nvpStr = str_replace( '&MAXFAILEDPAYMENTS=1', '&MAXFAILEDPAYMENTS=4', $nvpStr );

	return $nvpStr;

}
add_filter( 'pmpro_create_recurring_payments_profile_nvpstr', 'mypmpro_max_failed_payments_adjust', 10, 2 );
