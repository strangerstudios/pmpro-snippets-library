<?php
/**
 * This code recipe lets you sort the PMPro Member Directory by a custom user field.
 *
 * title: Order PMPro Member Directory by custom user meta field.
 * layout: snippet
 * collection: add-ons, pmpro-membership-directory
 * category: directory, sort, SQL, custom fields
 * link: https://www.paidmembershipspro.com/order-pmpro-member-directory/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function pmpro_sort_member_directory_by_user_field( $parts ) {
	global $wpdb;
    
	$user_meta_key_name = 'company'; // Custom field name to sort by, use the stored database value.
	$order = 'ASC'; //Change ASC to DESC if you need to change the order displayed based on meta value.

	$parts['SELECT'] = "SELECT SQL_CALC_FOUND_ROWS u.ID, u.user_login, u.user_email, u.user_nicename, u.display_name, UNIX_TIMESTAMP(u.user_registered) as joindate, mu.membership_id, mu.initial_payment, mu.billing_amount, mu.cycle_period, mu.cycle_number, mu.billing_limit, mu.trial_amount, mu.trial_limit, UNIX_TIMESTAMP(mu.startdate) as startdate, UNIX_TIMESTAMP(mu.enddate) as enddate, m.name as membership, umf.meta_value as first_name, uml.meta_value as last_name, umk.meta_value as user_meta_key_name FROM $wpdb->users u ";

	$parts['JOIN'] .= " LEFT JOIN $wpdb->usermeta umk ON umk.meta_key = '". esc_sql( $user_meta_key_name ) . "' AND u.ID = umk.user_id ";

	$parts['ORDER'] = ' ORDER BY user_meta_key_name ' . esc_sql( $order ) . ' ';

	return $parts;
}
add_filter( 'pmpro_member_directory_sql_parts', 'pmpro_sort_member_directory_by_user_field', 10, 1 );
