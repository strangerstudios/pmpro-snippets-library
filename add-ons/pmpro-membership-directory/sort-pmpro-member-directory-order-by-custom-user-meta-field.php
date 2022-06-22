<?php
/**
 * Order (sort) PMPro Member Directory by custom user meta field.
 *
 * This may be used to sort the directory by a custom registration field
 * created for Register Helper.
 *
 * Ths code recipe requires you to set your custom meta field name as the value
 * for the $user_meta_key_name variable
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function pmpro_custom_directory_sql_parts( $parts ) {

	// Set your custom registration field name here
	$user_meta_key_name = 'company';

	// The default is Ascending, set to true to change to a Descending sort order.
	$switch_sort_order = true;

	/* --- That's it, no further editing required --- */

	$order = $switch_sort_order ? 'DESC' : 'ASC';
	$user_meta_key_name = esc_sql( $user_meta_key_name );

	global $wpdb;

	$parts['SELECT'] = "SELECT SQL_CALC_FOUND_ROWS u.ID, u.user_login, u.user_email, u.user_nicename, u.display_name, UNIX_TIMESTAMP(u.user_registered) as joindate, mu.membership_id, mu.initial_payment, mu.billing_amount, mu.cycle_period, mu.cycle_number, mu.billing_limit, mu.trial_amount, mu.trial_limit, UNIX_TIMESTAMP(mu.startdate) as startdate, UNIX_TIMESTAMP(mu.enddate) as enddate, m.name as membership, umf.meta_value as first_name, uml.meta_value as last_name, umk.meta_value as user_meta_key_name FROM $wpdb->users u ";

	$parts['JOIN'] .= " LEFT JOIN $wpdb->usermeta umk ON umk.meta_key = '$user_meta_key_name' AND u.ID = umk.user_id ";

	$parts['ORDER'] = ' ORDER BY user_meta_key_name ' . $order . ' ';

	return $parts;
}
add_filter( 'pmpro_member_directory_sql_parts', 'pmpro_custom_directory_sql_parts', 10, 1 );
