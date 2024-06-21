
<?php
/**
 * Adds a custom column to the Members List and shows user meta data in the column.
 *
 * title: Adds a custom column to the Members List and shows user meta data in the column.
 * layout: snippet
 * collection: admin-pages
 * category: admin
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/*
 * Requires PMPro >= v2.3
 *
 * Filters for adding columns:
 * - 'manage_{$screen->id}_columns'
 *   - Default filter in WP_List_Table class to modify columns
 * - 'pmpro_manage_memberslist_columns'
 *   - Modifies columns in default locaiton of PMPro Members List
 *   - Equivalent to 'manage_memberships_page_pmpro-memberslist_columns'
 *
 * Hook for filling column data:
 * - 'pmpro_manage_memberslist_custom_column'
 */

// Copy from below here...

/**
 * Adds "Company" column to Members List.
 *
 * @param  array $columns for table.
 * @return array
 */
function my_pmpro_add_memberslist_col_company( $columns ) {

	$columns['company'] = 'Company';
	return $columns;
    
}
add_filter( 'pmpro_manage_memberslist_columns', 'my_pmpro_add_memberslist_col_company' );

/**
 * Fills the "Company" column of the Members List.
 *
 * @param  string $colname column being filled.
 * @param  string $user_id to get information for.
 */
function my_pmpro_fill_memberslist_col_company( $colname, $user_id ) {

	if ( 'company' === $colname ) {
		echo esc_html( get_user_meta( $user_id, 'company', true ) );
	}

}
add_filter( 'pmpro_manage_memberslist_custom_column', 'my_pmpro_fill_memberslist_col_company', 10, 2 );