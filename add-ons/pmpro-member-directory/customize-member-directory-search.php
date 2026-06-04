<?php
/**
 * Customize which fields are searched in the PMPro Member Directory search form.
 *
 * title: Customize Fields Searched in the Member Directory
 * layout: snippet-example
 * collection: add-ons
 * category: pmpro-member-directory, search
 * link: https://www.paidmembershipspro.com/add-ons/member-directory/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Customize the fields searched in the PMPro Member Directory.
 *
 * By default, the Member Directory searches:
 * - user_login
 * - user_email
 * - display_name
 * - all usermeta values
 *
 * This recipe replaces the default search behavior with a specific list
 * of WordPress user fields and usermeta keys that you define below.
 */
function my_pmpro_directory_custom_search_fields( $sql_search_where, $s ) {
	global $wpdb;

	$escaped_s = esc_sql( $s );

	$conditions = array(

		// -----------------------------------------------------------------------
		// WordPress user fields.
		// Uncomment any fields you would like to include in the search.
		// -----------------------------------------------------------------------
		"u.display_name LIKE '%{$escaped_s}%'",   // Search display name.
		// "u.user_login LIKE '%{$escaped_s}%'",  // Search username.
		// "u.user_email LIKE '%{$escaped_s}%'",  // Search email address.

		// -----------------------------------------------------------------------
		// Standard WordPress usermeta fields.
		// Remove any blocks you do not want to search.
		// -----------------------------------------------------------------------
		"EXISTS (
			SELECT 1 FROM {$wpdb->usermeta} um_fn
			WHERE um_fn.user_id = u.ID
			  AND um_fn.meta_key = 'first_name'
			  AND um_fn.meta_value LIKE '%{$escaped_s}%'
		)",

		"EXISTS (
			SELECT 1 FROM {$wpdb->usermeta} um_ln
			WHERE um_ln.user_id = u.ID
			  AND um_ln.meta_key = 'last_name'
			  AND um_ln.meta_value LIKE '%{$escaped_s}%'
		)",

		// -----------------------------------------------------------------------
		// PMPro User Fields (or any custom usermeta field).
		//
		// Replace the example meta_key values below with your own field names.
		// Add one EXISTS block for each custom field you want to include.
		// -----------------------------------------------------------------------

		// Example: Search a Company field.
		// "EXISTS (
		// 	SELECT 1 FROM {$wpdb->usermeta} um_co
		// 	WHERE um_co.user_id = u.ID
		// 	  AND um_co.meta_key = 'company'
		// 	  AND um_co.meta_value LIKE '%{$escaped_s}%'
		// )",

		// Example: Search a City field.
		// "EXISTS (
		// 	SELECT 1 FROM {$wpdb->usermeta} um_city
		// 	WHERE um_city.user_id = u.ID
		// 	  AND um_city.meta_key = 'city'
		// 	  AND um_city.meta_value LIKE '%{$escaped_s}%'
		// )",

		// Example: Search the WordPress Biography field.
		// "EXISTS (
		// 	SELECT 1 FROM {$wpdb->usermeta} um_bio
		// 	WHERE um_bio.user_id = u.ID
		// 	  AND um_bio.meta_key = 'description'
		// 	  AND um_bio.meta_value LIKE '%{$escaped_s}%'
		// )",
	);

	// Build the search query using the selected fields above.
	$sql_search_where = ' AND ( ' . implode( ' OR ', $conditions ) . ' ) ';

	return $sql_search_where;
}
add_filter( 'pmpro_member_directory_sql_search_where', 'my_pmpro_directory_custom_search_fields', 10, 2 );