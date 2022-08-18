<?php
/**
 * Custom order codes for Paid Memberships Pro Orders.
 * This code will take the order ID and create an order code from that such as "INV1", "INV2", "INV3" and increment with each order added.
 * A fallback is in place that if "INV1" already exists for some order, it will just generate a random code to be safe.
 *
 * title: Custom order codes for PMPro orders.
 * layout: snippet
 * collection: orders
 * category: custom
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
 function pmpro_custom_order_codes( $code ) {

 	global $wpdb;

 	$last_id= $wpdb->get_var( "SELECT `id` FROM $wpdb->pmpro_membership_orders ORDER BY `id` DESC LIMIT 1" );

 	$current_id = (int) $last_id + 1;

 	$prefix = apply_filters( "pmpro_custom_order_prefix", "INV" ); // You can change "INV" to something else or filter this from another plugin or custom code.

 	$code =  $prefix . $current_id; //Code cannot just be an integer and _must_contain_a_string_.

 	// We need to add a check to see if the order code is not taken, otherwise it will be an infinite loop.
 	$check = $wpdb->get_var( "SELECT `id` FROM $wpdb->pmpro_membership_orders WHERE code = '$code' LIMIT 1" );

 	// If the code already exists or is only a number, just generate a random order code with 10 digits.
 	if ( $check || is_numeric( $code ) ) {
 		$code = wp_generate_password( 10, false, false );

 	}

 	return $code;

 }
 add_filter( 'pmpro_random_code', 'pmpro_custom_order_codes', 10, 1 );
