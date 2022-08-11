<?php
/**
 * Function to retrieve active members of a level with required usermeta field.
 *
 * Example Usage:
 * $level_one_active_users = my_pmpro_get_level_active_users( 1, ARRAY_A );
 *
 * title: Retrieve all active members with required usermeta field for a specific membership level ID.
 * layout: snippet
 * collection: misc
 * category: members
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Return an array of all active members with the usermeta fields first name, last name, and pet name in an associative array.
 *
 * @param [mixed] $level Level ID (number) as integer or string
 * @param [constant] $output (Optional) Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants. Default is OBJECT.
 * @return [mixed] $results Format returned depends on $output parameter.
 *
 */
function my_pmpro_get_level_active_users( $level, $output = OBJECT ) {
	global $wpdb;

	$results = '';
	$results = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT u.ID, u.user_login, u.user_email, u.display_name, firstname.meta_value as first_name, lastname.meta_value as last_name, petname.meta_value as pet_name,
			mu.membership_id, m.name as membership_name
			FROM wp_users u
			INNER JOIN (SELECT user_id, meta_value FROM $wpdb->usermeta WHERE meta_key = 'first_name') as firstname ON u.ID = firstname.user_id
			INNER JOIN (SELECT user_id, meta_value FROM $wpdb->usermeta WHERE meta_key = 'last_name') as lastname ON u.ID = lastname.user_id
			INNER JOIN (SELECT user_id, meta_value FROM $wpdb->usermeta WHERE meta_key = 'pet_name') as petname ON u.ID = petname.user_id
			LEFT JOIN $wpdb->pmpro_memberships_users mu
			ON u.ID = mu.user_id
			LEFT JOIN $wpdb->pmpro_membership_levels m
			ON mu.membership_id = m.id
			WHERE mu.membership_id > 0  AND mu.status = 'active' AND mu.membership_id = %s  GROUP BY u.ID  ORDER BY u.ID DESC ",
			$level
		),
		$output
	);
	return $results;
}
