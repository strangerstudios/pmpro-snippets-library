<?php
/**
 * Add a specific Merchant Account ID to the Braintree sale array when creating a transaction.
 *
 * title: Set the Merchant Account ID for Braintree Transactions
 * layout: snippet
 * collection: payment-gateways, braintree
 * category: transaction
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_braintree_transaction_sale_array( $braintree_sale_array ) {
	$braintree_sale_array['merchant_account_id'] => 'your_merchant_account_id';
	return $braintree_sale_array;
}
 add_filter( 'pmpro_braintree_transaction_sale_array', 'my_pmpro_braintree_transaction_sale_array' );

