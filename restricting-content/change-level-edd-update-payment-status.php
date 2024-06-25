<?php
/**
 * title: Assign a membership level to members when purchasing an EDD product.
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, Edd purchase
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 * 
 * This recipe will allow you to assign a membership level to members when purchasing an EDD product.
 * You can do this one of two ways:
 *
 * OPTION 1: Product ID and Membership Level
 * $product_levels = array(
 		27 => 1, //27 is the EDD product ID. 1 is the membership level they get when purchasing product 27
 	);
 *
 * OPTION 2: Product ID with custom Membership Level Array - Change parameters for that level on the fly such as an expiration date.
 * $product_levels = array(
 		37 => array(
			'membership_id'		=> 2, // integer - Required
			'user_id'		=> $payment_meta['user_info']['id'],
			'code_id'		=> '0', //Discount code
			'initial_payment' 	=> '0', // float (string)
			'billing_amount' 	=> '0', // float (string)
			'cycle_number' 		=> '0', // integer
			'cycle_period' 		=> '', // string (enum)
			'billing_limit' 	=> '0', // integer
			'trial_amount' 		=> '0', // float (string)
			'trial_limit' 		=> '0', // integer
			'startdate' 		=> current_time( 'mysql' ), // string (date)
			'enddate' 		=> '2025-12-31' // string (date)
 		),
 	);
 */

 function my_pmpro_change_level_edd_update_payment_status( $payment_id, $new_status, $old_status ) {

  	// Basic payment meta.
 	$payment_meta = edd_get_payment_meta( $payment_id );

  	// OPTION 1: Set the relationship between EDD product ID and level ID.
  	$product_levels = array(
  		27 => 1, //27 is the EDD product ID. 1 is the membership level they get when purchasing product 27
  	);

 	$user_id = $payment_meta['user_info']['id'];

 	if ( $new_status == 'publish' || $new_status == 'complete' ) {
 		// Order is complete.
 		foreach ( $payment_meta['cart_details'] as $key => $item ) {
 			if ( ! empty( $product_levels[$item['id']] ) ) {
 				pmpro_changeMembershipLevel( $product_levels[$item['id']], $payment_meta['user_info']['id'] ); // If a user buys the product with ID of 27, change their level to 1.
 			}
 		}
 	} else {
 		// Remove the level for any of the other statuses: pending, processing, revoked, failed, abandoned, preapproval, cancelled, refunded
 		pmpro_changeMembershipLevel( 0, $user_id );
 	}
 }
 add_action( 'edd_update_payment_status', 'my_pmpro_change_level_edd_update_payment_status', 10, 3 );
