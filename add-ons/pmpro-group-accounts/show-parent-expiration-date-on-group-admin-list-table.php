<?php
/**
 * Add a "Parent Expiration" column to the Group Accounts admin list table.
 *
 * title: Add Parent Expiration Date Column to the Group Accounts admin list table
 * layout: snippet
 * collection: add-ons, pmpro-group-accounts
 * category: admin
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Register the "Parent Expiration" column header after the "Parent Level" column.
 */
function my_pmprogroupacct_admin_parent_expiration_column( $columns ) {
	$new_columns = array();
	foreach ( $columns as $key => $label ) {
		$new_columns[ $key ] = $label;
		if ( 'parent_level' === $key ) {
			$new_columns['parent_expiration'] = __( 'Parent Expiration', 'pmpro-snippets-library' );
		}
	}
	return $new_columns;
}
add_filter( 'pmprogroupacct_manage_groupslist_columns', 'my_pmprogroupacct_admin_parent_expiration_column' );

/**
 * Display the "Parent Expiration" column value for each group row.
 */
function my_pmprogroupacct_admin_parent_expiration_column_content( $column_name, $item ) {
	if ( 'parent_expiration' !== $column_name ) {
		return;
	}

	// Get the parent membership level associated with the current row item.
	$level = pmpro_getSpecificMembershipLevelForUser( (int) $item->group_parent_user_id, (int) $item->group_parent_level_id );

	// Make sure that we have a level object.
	if ( empty( $level ) || ! is_object( $level ) ) {
		return;
	}

	// Generate the column content.
	if ( empty( $level->enddate ) ) {
		// If the level does not have an enddate, show "Never".
		echo esc_html__( 'Never', 'pmpro-snippets-library' );
	} else {
		// Show the parent's enddate.
		echo esc_html( date_i18n( get_option( 'date_format' ), (int) $level->enddate ) );
	}
}
add_action( 'pmprogroupacct_manage_grouplist_custom_column', 'my_pmprogroupacct_admin_parent_expiration_column_content', 10, 2 );
