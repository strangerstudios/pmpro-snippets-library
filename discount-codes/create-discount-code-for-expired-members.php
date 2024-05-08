<?php
/**
 * Creates a discount code for expired members.
 *
 * Learn more at https://www.paidmembershipspro.com/automatic-discount-code/
 * 
 * title: Creates a discount code for expired members
 * layout: snippet-example
 * collection: discount-codes
 * category: expiration
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * This generates an unique discount code with one use whenever a user expires, for their current level.
 * Add the variable !!expired_code!! to the Membership Expired email, to show the code to users.
 * Add this code to your PMPro Customizations Plugin - https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function pmpro_create_discount_code_on_expiry( $user_id, $level_id ) {

 	// Generate the unique discount code.
 	pmpro_create_custom_discount_code( $user_id, $level_id );

	return $user_id;
}
add_filter( 'pmpro_membership_pre_membership_expiry', 'pmpro_create_discount_code_on_expiry', 10, 2 );


/**
 * Generates a code for expired users only.
 */
function pmpro_create_custom_discount_code( $user_id, $level_id ) {
	global $wpdb;

	$prefix = "PMPRO"; // Make this something unique for expired codes.

	$code = $prefix . $user_id;

	$wpdb->replace(
		$wpdb->pmpro_discount_codes,
		array(
			'code' => $code,
			'starts' => date_i18n( "Y-m-d" ),
			'expires' => date_i18n( "Y-m-d", strtotime( "+14 days" ) ), // change +14 to +xx for how many days the discount code will be valid.
			'uses' => 1
		)
	);

	// Get the code ID so we can insert level settings.
	$sql_get_code_id = "SELECT id FROM $wpdb->pmpro_discount_codes WHERE code = '" . esc_sql( $code ) . "' LIMIT 1";
	$code_id = $wpdb->get_var( $sql_get_code_id );

	// Configure the level settings.
	$wpdb->insert(
		$wpdb->pmpro_discount_codes_levels,
		array(
			'code_id' => $code_id,
			'level_id' => $level_id,
			'initial_payment' => '5.00',
			'billing_amount' => '0.00',
			'cycle_number' => '0',
			'cycle_period' => '',
			'billing_limit' => '0',
			'trial_amount' => '0.00',
			'trial_limit' => '0',
			'expiration_number' => '0',
			'expiration_period' => ''
		)
	);
}

// Adjust expiration email to include the discount code generated.
function my_pmpro_expiration_discount_code_email_variable( $data, $email ) {

	$user = get_user_by( 'email', $email->email );

	$data['expired_code'] = "PMPRO" . $user->ID;

	return $data;
}
add_filter( 'pmpro_email_data', 'my_pmpro_expiration_discount_code_email_variable', 10, 2 );