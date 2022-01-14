<?php
/**
 * Auto-populate Billing Fields with Woocommerce Data
 *
 * title: Auto-populate Billing Fields with Woocommerce Data
 * layout: snippet
 * collection: checkout
 * category: woocommerce
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function mypmpro_autofill_pmpro_fields() {

	global $current_user, $bfirstname, $blastname, $baddress1, $baddress2, $bcity, $bstate, $bzipcode, $bcountry, $bphone;

	// Personal details from billing details WooCommerce.
	$company = get_user_meta( $current_user->ID, 'billing_company', true );
	$fname   = get_user_meta( $current_user->ID, 'billing_first_name', true );
	$lname   = get_user_meta( $current_user->ID, 'billing_last_name', true );
	$phone   = get_user_meta( $current_user->ID, 'billing_phone', true );

	// Address details from billing details WooCommerce.
	$address_1 = get_user_meta( $current_user->ID, 'billing_address_1', true );
	$address_2 = get_user_meta( $current_user->ID, 'billing_address_2', true );
	$city      = get_user_meta( $current_user->ID, 'billing_city', true );
	$state     = get_user_meta( $current_user->ID, 'billing_state', true );
	$postcode  = get_user_meta( $current_user->ID, 'billing_postcode', true );
	$country   = get_user_meta( $current_user->ID, 'billing_country', true );

	$bfirstname = $fname;
	$blastname  = $lname;
	$baddress1  = $address_1;
	$baddress2  = $address_2;
	$bcity      = $city;
	$bstate     = $state;
	$bzipcode   = $postcode;
	$bcountry   = $country;
	$bphone     = $phone;

}
add_action( 'wp', 'mypmpro_autofill_pmpro_fields' );
