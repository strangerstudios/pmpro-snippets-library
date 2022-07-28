<?php //do not copy

/**
 * This recipe will send shipping fields to Mailchimp.

 title: send shipping fields to Mailchimp
 layout: Snippets
 collection: pmpro-MailChimp
 category: shipping-fields 
 *
 * The Shipping Address on Membership Checkout Add On is required for this recipe to work
 * https://www.paidmembershipspro.com/add-ons/shipping-address-membership-checkout/
 *
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/*
 * Creates Shipping fields in MailChimp
 */
function my_pmpro_mailchimp_merge_field_shipping( $merge_fields ) {

	$merge_fields[] = array( 'name' => 'SFIRSTNAME', 'type' => 'text' );
	$merge_fields[] = array( 'name' => 'SLASTNAME', 'type' => 'text' );
	$merge_fields[] = array( 'name' => 'SADDRESS1', 'type' => 'text' );
	$merge_fields[] = array( 'name' => 'SADDRESS2', 'type' => 'text' );
	$merge_fields[] = array( 'name' => 'SCITY', 'type' => 'text' );
	$merge_fields[] = array( 'name' => 'SSTATE', 'type' => 'text' );
	$merge_fields[] = array( 'name' => 'SZIPCODE', 'type' => 'text' );
	$merge_fields[] = array( 'name' => 'SPHONE', 'type' => 'text' );
	$merge_fields[] = array( 'name' => 'SCOUNTRY', 'type' => 'text' );


	return $merge_fields;
}
add_filter( 'pmpro_mailchimp_merge_fields', 'my_pmpro_mailchimp_merge_field_shipping' );

/*
 * Populates the Shipping fields in MailChimp
 */
function my_pmpro_mailchimp_listsubscribe_field_shipping($fields, $user) {

	$new_fields =  array(
		'SFIRSTNAME' => ( isset( $_REQUEST['sfirstname'] ) ) ? $_REQUEST['sfirstname'] : get_user_meta( $user->ID, 'sfirstname', true ),
		'SLASTNAME' => ( isset( $_REQUEST['slastname'] ) ) ? $_REQUEST['slastname'] : get_user_meta( $user->ID, 'slastname', true ),
		'SADDRESS1' => ( isset( $_REQUEST['saddress1'] ) ) ? $_REQUEST['saddress1'] : get_user_meta( $user->ID, 'saddress1', true ),
		'SADDRESS2' => ( isset( $_REQUEST['saddress2'] ) ) ? $_REQUEST['saddress2'] : get_user_meta( $user->ID, 'saddress2', true ),
		'SCITY' => ( isset( $_REQUEST['scity'] ) ) ? $_REQUEST['scity'] : get_user_meta( $user->ID, 'scity', true ),
		'SSTATE' => ( isset( $_REQUEST['sstate'] ) ) ? $_REQUEST['sstate'] : get_user_meta( $user->ID, 'sstate', true ),
		'SZIPCODE' => ( isset( $_REQUEST['szipcode'] ) ) ? $_REQUEST['szipcode'] : get_user_meta( $user->ID, 'szipcode', true ),
		'SPHONE' => ( isset( $_REQUEST['sphone'] ) ) ? $_REQUEST['sphone'] : get_user_meta( $user->ID, 'sphone', true ),
		'SCOUNTRY' => ( isset( $_REQUEST['scountry'] ) ) ? $_REQUEST['scountry'] : get_user_meta( $user->ID, 'scountry', true ),
	);

	$fields = array_merge($fields, $new_fields);

	return $fields;

}
add_action('pmpro_mailchimp_listsubscribe_fields', 'my_pmpro_mailchimp_listsubscribe_field_shipping', 10, 2);
