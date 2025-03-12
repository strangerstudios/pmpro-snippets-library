<?php
/**
 * This recipe will send mailing address fields to Mailchimp.
 *
 * title: Send Mailing Address fields to Mailchimp
 * layout: snippets
 * collection: pmpro-mailchimp
 * category: shipping-fields, email, mailchimp
 * link: https://www.paidmembershipspro.com/shipping-address-details-mailchimp/
 *
 * The Mailing Address (formerly Shipping Address) Add On is required for this recipe to work
 * https://www.paidmembershipspro.com/add-ons/shipping-address-membership-checkout/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

// Creates Mailiing fields in MailChimp
function my_pmpro_mailchimp_merge_field_mailing( $merge_fields ) {

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
add_filter( 'pmpro_mailchimp_merge_fields', 'my_pmpro_mailchimp_merge_field_mailing' );

// Populate the Mailing fields in MailChimp from the mailing fields.
function my_pmpro_mailchimp_listsubscribe_field_mailing( $fields, $user ) {

	$new_fields = array(
		'SFIRSTNAME' => ( isset( $_REQUEST['pmpro_sfirstname'] ) ) ? sanitize_text_field( $_REQUEST['pmpro_sfirstname'] ) : get_user_meta( $user->ID, 'pmpro_sfirstname', true ),
		'SLASTNAME'  => ( isset( $_REQUEST['pmpro_slastname'] ) ) ? sanitize_text_field( $_REQUEST['pmpro_slastname'] ) : get_user_meta( $user->ID, 'pmpro_slastname', true ),
		'SADDRESS1'  => ( isset( $_REQUEST['pmpro_saddress1'] ) ) ? sanitize_text_field( $_REQUEST['pmpro_saddress1'] ) : get_user_meta( $user->ID, 'pmpro_saddress1', true ),
		'SADDRESS2'  => ( isset( $_REQUEST['pmpro_saddress2'] ) ) ? sanitize_text_field( $_REQUEST['pmpro_saddress2'] ) : get_user_meta( $user->ID, 'pmpro_saddress2', true ),
		'SCITY'      => ( isset( $_REQUEST['pmpro_scity'] ) ) ? sanitize_text_field( $_REQUEST['pmpro_scity'] ) : get_user_meta( $user->ID, 'pmpro_scity', true ),
		'SSTATE'     => ( isset( $_REQUEST['pmpro_sstate'] ) ) ? sanitize_text_field( $_REQUEST['pmpro_sstate'] ) : get_user_meta( $user->ID, 'pmpro_sstate', true ),
		'SZIPCODE'   => ( isset( $_REQUEST['pmpro_szipcode'] ) ) ? sanitize_text_field( $_REQUEST['pmpro_szipcode'] ) : get_user_meta( $user->ID, 'pmpro_szipcode', true ),
		'SPHONE'     => ( isset( $_REQUEST['pmpro_sphone'] ) ) ? sanitize_text_field( $_REQUEST['pmpro_sphone'] ) : get_user_meta( $user->ID, 'pmpro_sphone', true ),
		'SCOUNTRY'   => ( isset( $_REQUEST['pmpro_scountry'] ) ) ? sanitize_text_field( $_REQUEST['pmpro_scountry'] ) : get_user_meta( $user->ID, 'pmpro_scountry', true )
	);

	$fields = array_merge( $fields, $new_fields );

	return $fields;

}
add_action( 'pmpro_mailchimp_listsubscribe_fields', 'my_pmpro_mailchimp_listsubscribe_field_mailing', 10, 2 );
