<?php
/**
 * Modify the application fee percentage that is charged on Stripe transactions.
 * 
 * title: Adjust Fee to Stranger Studios When Using Stripe Connect
 * layout: snippet
 * collection: payment-gateways, stripe
 * category: fee
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function my_pmpro_set_application_fee_percentage( $application_fee_percentage ) {
 	return 0; // Remove the application fee.
 	//return 2.5; // Increase application fee to 2.5%
 }
 add_filter( 'pmpro_set_application_fee_percentage', 'my_pmpro_set_application_fee_percentage' );
