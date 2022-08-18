<?php

/**
 * Adds WooCommerce billing fields to Paid Memberships Pro invoices.
 *
 * title: Add Woocommerce billing field to PMPro invoices
 * layout: snippet
 * collection: frontend-pages
 * category: invoices, woocommerce
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
 function add_woocommerce_billing_fields_to_pmpro_invoice() {
 	global $current_user;

 	// Personal details from billing details WooCommerce.
 	$company = get_user_meta( $current_user->ID, 'billing_company', true );
 	$fname = get_user_meta( $current_user->ID, 'billing_first_name', true );
 	$lname = get_user_meta( $current_user->ID, 'billing_last_name', true );
 	$phone = get_user_meta( $current_user->ID, 'billing_phone', true );

 	// Address details from billing details WooCommerce.
 	$address_1 = get_user_meta( $current_user->ID, 'billing_address_1', true );
 	$address_2 = get_user_meta( $current_user->ID, 'billing_address_2', true );
 	$city = get_user_meta( $current_user->ID, 'billing_city', true );
 	$state = get_user_meta( $current_user->ID, 'billing_state', true );
 	$postcode = get_user_meta( $current_user->ID, 'billing_postcode', true );
 	$country = get_user_meta( $current_user->ID, 'billing_country', true );

 	echo '<li><strong>Company:</strong> ' . $company . '</li>';
 	echo '<li><strong>Full Name:</strong> ' . $fname . ' ' . $lname . '</li>';
 	echo '<li><strong>Phone:</strong> ' . $phone . '</li>';
 	echo '<li><strong>Address:</strong><br/>' . $address_1 . '<br/>';
 	echo $address_2 . '<br/>';
 	echo $city . '<br/>';
 	echo $state . '<br/>';
 	echo $postcode . '<br/>';
 	echo $country . '</li>';
 }

 add_action( 'pmpro_invoice_bullets_bottom', 'add_woocommerce_billing_fields_to_pmpro_invoice' );
