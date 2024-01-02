<?php 
/**
 * Add Group code to Paid Memberships Pro Zapier Integration data.
 
 * title: Add Group code to Zapier
 * layout: snippet
 * collection: add-ons, pmpro-zapier
 * category: pmpro-group-accounts
 *
 * This runs on the "After Checkout" trigger. 
 * Requires Group Accounts Add On - https://www.paidmembershipspro.com/add-ons/group-accounts/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function my_pmpro_add_group_code_zapier( $data, $user_id, $level, $order ) {
 
	//Bail if pmpro groups account Add On isn't active.
	if (! class_exists( 'PMProGroupAcct_Group' ) ) {
		return;
	}
 
	$group_code = $_REQUEST['pmprogroupacct_group_code'];
 
	if ( ! empty( $group_code ) ) {
		$data['pmpro_group_code'] = $group_code;
	}
 
	return $data;
}
add_filter( 'pmproz_after_checkout_data', 'my_pmpro_add_group_code_zapier', 10, 4 );