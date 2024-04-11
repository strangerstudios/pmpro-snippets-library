<?php
/**
 * Add !!membership_level_name!! to Paid Memberships Pro expired email template.
 * 
 * title: Add !!membership_level_name!! to expired email template.
 * layout: snippet-example
 * collection: email
 * category: custom-fields, email
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_email_data_membership_level_name( $data, $email ) {
	// Only add the membership level name to the expired email template.
	if ( strpos( $email->template, '_expired' ) == false ) {
		return $data;
	}

	// Only add the membership level name if it is not already set.
	if ( isset( $data['membership_level_name'] ) ) {
		return $data;
	}

	// Get the last level ID with expired status.
	global $wpdb;
	$user  = get_user_by( 'email', $data['user_email'] );
	$last_expired_level = $wpdb->get_var( $wpdb->prepare( "SELECT membership_id FROM $wpdb->pmpro_memberships_users WHERE user_id = %s AND status = %s ORDER BY modified DESC LIMIT 1", $user->ID, 'expired' ) );
	$last_expired_level_obj = pmpro_getLevel( $last_expired_level );

	// Get level name and add to email data
	$data['membership_level_name'] = $last_expired_level_obj->name;

	// Return the updated data.
	return $data;
}
add_filter( 'pmpro_email_data', 'my_pmpro_email_data_membership_level_name', 10, 2 );
