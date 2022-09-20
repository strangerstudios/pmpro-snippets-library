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
 * Add a text field for other payment type description based on the value of the payment_type selected.
 */
function my_pmpro_payment_type_other_conditional_user_field() {
	// Don't break if PMPro is out of date or not loaded.
	if ( ! function_exists( 'pmpro_add_user_field' ) ) {
		return false;
	}
 
	// Store our field settings in an array.
	$fields = array();
	
	// The Payment Type dropdown. If it wasn't added via settings, use this code to add.
	/*
	$fields[] = new PMPro_Field(
		'payment_type',
		'select',
		array(
			'label'			=> 'Payment Type',
			'options' => array(				
				''		=> '',
				'cash'	=> 'Cash',
				'check'	=> 'Check',
				'other'	=> 'Other',
			)
		)
	);
	*/
	
	// The conditional field that only shows if the field name 'payment_type' is set to 'other'.
	$fields[] = new PMPro_Field(
		'other_payment_type',
		'text',
		array(
			'label'  => 'Please describe your payment method.',
			'profile'		=> 'admins',
			'memberslistcsv'	=> true,
			'size' => 100,
			'depends'		=> array(array('id' => 'payment_type', 'value' => 'Other')) 
		)
	);
 
	// Add our field into the same group as the "Payment Method" fields.
	foreach ( $fields as $field ) {
		pmpro_add_user_field(
			'Payment Method',
			$field
		);
	}
}
add_action( 'init', 'my_pmpro_payment_type_other_conditional_user_field' );

/**
 * Update the order with the chosen payment type and optional payment type description.
 */
function my_pmpro_add_payment_type_to_order( $order ) {

	// Bail if payment gateway isn't check.
	if ( 'check' != $order->gateway ) {
		return;
	}

	if ( isset( $_REQUEST['other_payment_type'] ) && ! empty( $_REQUEST['other_payment_type'] ) ) {
		$order->payment_type = esc_attr( $_REQUEST['other_payment_type'] );
	} elseif ( isset( $_REQUEST['payment_type']) && 'other' !== $_REQUEST['payment_type'] ) {
		$order->payment_type = esc_attr( $_REQUEST['payment_type'] );
	}

	$order->saveOrder();
}
add_action( 'pmpro_added_order', 'my_pmpro_add_payment_type_to_order' );
