<?php
/**
 * Adds a custom column to the Users List and show user meta data in the column.
 *
 * title: Adds a custom column to the Users List and show user meta data in the column.
 * layout: snippet
 * collection: admin-pages
 * category: admin
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
// Add 'Company' column to Users list header.
function my_pmpro_manage_users_columns( $columns ) {
    $columns['company'] = 'Company';
    return $columns;
}
add_filter( 'manage_users_columns', 'my_pmpro_manage_users_columns' );

// Add 'Company' column to Users list rows.
function my_pmpro_manage_users_custom_column( $value, $column_name, $user_id ) {
    if ( 'company' === $column_name ) {
        return get_user_meta( $user_id, 'company', true );
    }
    return $value;
}
add_action( 'manage_users_custom_column', 'my_pmpro_manage_users_custom_column', 10, 3 );
