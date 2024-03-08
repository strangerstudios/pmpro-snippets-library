<?php
/**
 * Add the "Member Number" column to the members list and CSV export.
 *
 * Member numbers must be generated using the generate_member_number snippet in this companion code: 
 * https://github.com/strangerstudios/pmpro-snippets-library/tree/dev/misc/generate-unique-membership-number.php
 *
 * title: Add Member Number to Members List and CSV Export
 * layout: snippet
 * collection: admin-pages
 * category: admin
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Add a "Member Number" column to the Members List.
 *
 * @param array $columns The columns to display in the Members List.
 * @return array The updated columns to display in the Members List.
 */
function my_pmpro_manage_memberslist_columns_member_number( $columns ) {
	$columns['member_number'] = 'Member Number';
	return $columns;
}
add_filter( 'pmpro_manage_memberslist_columns', 'my_pmpro_manage_memberslist_columns_member_number' );

/**
 * Display the Member Number in the Members List.
 *
 * @param string $column_name The name of the column to display.
 * @param int    $user_id     The ID of the user to display the column for.
 * @param array|null $item The membership information being shown or null.
 */
function my_pmpro_manage_memberslist_column_member_number( $column_name, $user_id, $item = null ) {
	if ( 'member_number' === $column_name ) {
		// Get member number.
		$member_number = get_user_meta( $user_id, 'member_number', true );

		// If this column is completely empty, set $member_number to a dash.
		if ( empty( $member_number ) ) {
			$member_number = '&#8212;';
		}

		// Echo the data for this column.
		echo $member_number;
	}
}

/**
 * Hooks the my_pmpro_manage_memberslist_column_member_number() function
 * with 2 parameters if using PMPro v2.x or 3 parameters if using PMPro v3.0+.
 */
function my_pmpro_hook_my_pmpro_manage_memberslist_column_member_number() {
	if ( class_exists( 'PMPro_Subscription' ) ) {
		add_action( 'pmpro_manage_memberslist_custom_column', 'my_pmpro_manage_memberslist_column_member_number', 10, 3 );
	} else {
		add_action( 'pmpro_manage_memberslist_custom_column', 'my_pmpro_manage_memberslist_column_member_number', 10, 2 );
	}
}
add_action( 'admin_init', 'my_pmpro_hook_my_pmpro_manage_memberslist_column_member_number' );

/**
 * Add "member_number" columns to CSV export
 *
 * @param array $columns The columns to be exported with their callbacks.
 * @return array
 */
function my_pmpro_members_list_csv_extra_columns_member_number( $columns ) {
	$new_columns = array(
		'member_number' => 'my_pmpro_members_list_csv_extra_columns_member_number_callback',
	);

	$columns = array_merge($columns, $new_columns);

	return $columns;
}
add_filter('pmpro_members_list_csv_extra_columns', 'my_pmpro_members_list_csv_extra_columns_member_number');

/**
 * Callback for "member_number" column in CSV export
 *
 * @param object $user The user object for the row with some additional membership data.
 * @return string
 */
function my_pmpro_members_list_csv_extra_columns_member_number_callback($user) {
	return get_user_meta($user->ID, 'member_number', true);
}
