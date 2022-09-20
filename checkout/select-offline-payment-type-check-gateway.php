<?php
/**
 * Let Users Select Offline Payment Methods Using the "Pay by Check" Gateway
 *
 * title: Let Users Select Offline Payment Methods Using the "Pay by Check" Gateway
 * layout: snippet
 * collection: checkout
 * category: check
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Update the order with the chosen payment type and optional payment type description.
 */
function my_pmpro_add_payment_type_to_order( $order ) {

	// Bail if payment gateway isn't check.
	if ( 'check' != $order->gateway ) {
		return;
	}

	if ( isset( $_REQUEST['payment_type']) && 'other' !== $_REQUEST['payment_type'] ) {
		$order->payment_type = esc_attr( $_REQUEST['payment_type'] );
	}

	$order->saveOrder();
}
add_action( 'pmpro_added_order', 'my_pmpro_add_payment_type_to_order' );
