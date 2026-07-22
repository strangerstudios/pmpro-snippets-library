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

	// Change 'company' to the name of your user field.
	$user_meta_key_name = 'company';
	// The default is Ascending (A-Z), set to true to change to a Descending sort order.
	$switch_sort_order = false;

	/* --- That's it, no further editing required --- */

	$order              = $switch_sort_order ? 'DESC' : 'ASC';
	$user_meta_key_name = esc_sql( $user_meta_key_name );

	$parts['JOIN']  .= " LEFT JOIN $wpdb->usermeta umk ON umk.meta_key = '" . $user_meta_key_name . "' AND u.ID = umk.user_id ";
	$parts['ORDER']  = " ORDER BY umk.meta_value " . $order . " ";

	return $parts;
}
add_filter( 'pmpro_member_directory_sql_parts', 'pmpro_sort_member_directory_by_user_field', 10, 1 );
