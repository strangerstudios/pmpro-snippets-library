<?php
/**
 * Add Level Group Columns in Members List CSV Export
 *
 * This recipe adds two custom columns to the Members List CSV export.
 * Each column (Group One, Group Two) shows level names from static arrays of level IDs.
 *
 * Title: Add Level Group Columns in Members List CSV Export
 * Layout: snippet
 * Collection: admin-pages
 * Category: exports
 * link: tbd
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * Link: https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

// Register the custom columns.
function my_pmpro_members_list_static_columns( $columns ) {
	$columns['group_one'] = 'my_pmpro_get_group_one_column'; // Column title will be "Group One"
	$columns['group_two'] = 'my_pmpro_get_group_two_column'; // Column title will be "Group Two"
	return $columns;
}
add_filter( 'pmpro_members_list_csv_extra_columns', 'my_pmpro_members_list_static_columns' );

// Define Group One data.
function my_pmpro_get_group_one_column( $user ) {
	$group_one_ids = array( 1, 2, 3 ); // Set level IDs for Group One
	$levels = pmpro_getMembershipLevelsForUser( $user->ID );
	$matches = array();

	foreach ( $levels as $level ) {
		if ( in_array( $level->id, $group_one_ids ) ) $matches[] = $level->name;
	}

	return implode( ', ', $matches );
}

// Define Group Two data.
function my_pmpro_get_group_two_column( $user ) {
	$group_two_ids = array( 4, 5 ); // Set level IDs for Group Two
	$levels = pmpro_getMembershipLevelsForUser( $user->ID );
	$matches = array();

	foreach ( $levels as $level ) {
		if ( in_array( $level->id, $group_two_ids ) ) $matches[] = $level->name;
	}

	return implode( ', ', $matches );
}

add_filter( 'pmpro_members_list_csv_extra_columns_data', 'my_pmpro_get_group_one_column', 10, 2 );
add_filter( 'pmpro_members_list_csv_extra_columns_data', 'my_pmpro_get_group_two_column', 10, 2 );