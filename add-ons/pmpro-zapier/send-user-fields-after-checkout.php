<?php
/**
 * Send custom field data to Zapier using the "After Checkout Trigger".
 * Learn more at https://www.paidmembershipspro.com/send-user-fields-zapier-after-membership-checkout/
 * 
 * title: Send User Fields After Checkout Zapier.
 * layout: snippet
 * collection: add-ons, pmpro-zapier
 * category: custom-fields
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function pmpro_add_custom_data_to_zapier( $data, $user_id, $level, $order ) {
	$data['sms']   = get_user_meta( $user_id, 'sms', true );
	$data['goals'] = get_user_meta( $user_id, 'goals', true );
	$data['diet']  = get_user_meta( $user_id, 'diet', true );
	return $data;
}
add_filter( 'pmproz_after_checkout_data', 'pmpro_add_custom_data_to_zapier', 10, 4 );
