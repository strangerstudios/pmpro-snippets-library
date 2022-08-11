<?php //do not copy

/**
 * This recipe will generate an affiliate code for all existing users when using PMPro Affiliates.
 * To run this script, you'll need to add /wp-admin/?mypmpro_create_affiliates=true to your URL
 *
 * The default roles that we're looking for are subscribers. You can change this on line 18
 * The number of days a cookie will remain active before expiring is 30 days (Line 49)
 *
 * title: Generate affiliate code for existing users
 * layout: snippet
 * collection: add-ons
 * category: pmpro-affiliates
 *
 * Add this code to your PMPro Customizations Plugin - https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
* Works for PayPal Express and Stripe payment gateways.
* www.paidmembershipspro.com
*/

function mypmpro_run_affiliate_creation(){

 if( isset( $_REQUEST['mypmpro_create_affiliates'] ) && function_exists( 'mypmpro_create_affiliate' ) ){

	 $users = get_users( array( 'role__in' => array( 'subscriber' ) ) );

	 if( $users ){

		 foreach( $users as $user ){

			 mypmpro_create_affiliate( $user->display_name, $user->user_login );

		 }

		 exit('End');

	 }

 }

}
add_action( 'admin_init', 'mypmpro_run_affiliate_creation' );

function mypmpro_create_affiliate( $name = '', $username = '' ){

 if( !function_exists( 'pmpro_affiliates_getNewCode' ) ){
	 return;
 }

 global $wpdb;

 $code = pmpro_affiliates_getNewCode();

 $trackingcode = "";
 $cookiedays = "30"; //Change number of days the cookie should be valid for
 $enabled = true;

 $sqlQuery = "INSERT INTO $wpdb->pmpro_affiliates (code, name, affiliateuser, trackingcode, cookiedays, enabled) VALUES('" . esc_sql($code) . "', '" . esc_sql($name) . "', '" . esc_sql($username) . "', '" . esc_sql($trackingcode) . "', '" . intval($cookiedays) . "', '" . esc_sql($enabled) . "')";

 if( $wpdb->query($sqlQuery) !== false ) {
	 echo "Affiliate Created Successfully - ".$username."<br/>";
 } else {
	 echo "Affiliate Could Not Be Created  - ".$username."<br/>";
 }

}
