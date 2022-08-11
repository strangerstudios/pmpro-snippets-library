<?php
/**
 * This recipe will add billing fields to the checkout page. Useful for payment gateways
 * that don't have the option to display billing fields.
 *
 * title: Add billing fields to checkout.
 * layout: snippet
 * collection: checkout
 * category: billing-fields, register-helper, custom-fields
 *
 * You can add this recipe to your site by creating a custom plugin
* or using the Code Snippets plugin available for free in the WordPress repository.
* Read this companion article for step-by-step directions on either method.
* https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
*/

function mypmpromm_add_address_fields() {
 // Require PMPro and PMPro Register Helper
 if ( ! defined( 'PMPRO_VERSION' ) || ! defined( 'PMPRORH_VERSION' ) ) {
	 return;
 }

 // List the fields you want to include
 $address_fields = array(
	 'pmpro_baddress1' => 'Address 1',
	 'pmpro_baddress2' => 'Address 2',
	 'pmpro_bcity' => 'City',
	 'pmpro_bstate' => 'State',
	 'pmpro_bzipcode' => 'Zipcode',
 );

 // Define the fields
 $fields = array();
 foreach ( $address_fields as $name => $label ) {
	 $fields[] = new PMProRH_Field(
		 $name,
		 'text',
		 array(
			 'label' => $label,
			 'size' => 40,
			 'addmember' => true,
		 )
	 );
 }

 // Add new checkout box with label
 pmprorh_add_checkout_box( 'billing_mailing_address', 'Billing/Mailing Address' );

 // Add fields into a new checkout_boxes area of checkout page
 foreach ( $fields as $field ) {
	 pmprorh_add_registration_field(
		 'billing_mailing_address', // location on checkout page
		 $field            // PMProRH_Field object
	 );
 }
}
add_action( 'init', 'mypmpromm_add_address_fields' );
