<?php
/**
 * Show the member's last payment date and next payment date on the Members list and Members CSV export.
 *
 * Note that "last payment" value will get the last order in "success", "cancelled", or "" status. (Oddly enough, cancelled here means that the membership was cancelled, not the order.)
 *
 * The "next payment" value is an estimate based on the billing cycle of the subscription and the last order date. It may be off from the actual recurring date set at the gateway, especially if the subscription was updated at the gateway.
 *
 * title: Show next and last payment date on the Members list and Members CSV export.
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
 * Adds "Last Payment" and "Next Payment" columns to Members List.
 *
 * @param  array $columns for table.
 * @return array
 */
function my_pmpro_add_memberslist_col_payment_dates( $columns ) {
    $columns[ 'last-payment' ] = 'Last Payment';
    $columns[ 'next-payment' ] = 'Next Payment';
    return $columns;
}
add_filter( 'pmpro_manage_memberslist_columns', 'my_pmpro_add_memberslist_col_payment_dates' );

/**
 * Fills the "last-payment" and "next-payment" columns of the Members List.
 *
 * @param  string $colname column being filled.
 * @param  string $user_id to get information for.
 */
function my_pmpro_fill_memberslist_col_payment_dates( $colname, $user_id ) {
    if ( 'last-payment' === $colname ) {
        $order = new MemberOrder();
        $order->getLastMemberOrder( $user_id, array( 'success', 'cancelled', '' ) );

        if ( ! empty( $order ) && ! empty( $order->id ) ) {
            echo date( get_option('date_format'), $order->timestamp );
        } else {
            echo 'N/A';
        }
    }

    if ( 'next-payment' === $colname ) {
        $next = pmpro_next_payment( $user_id, array( 'success', 'cancelled', '' ), 'date_format' ); 
        if ( $next ) {
            echo $next;
        } else {
            echo 'N/A';
        }
    }
}
add_filter( 'pmpro_manage_memberslist_custom_column', 'my_pmpro_fill_memberslist_col_payment_dates', 10, 2 );

/**
 * Adds "Last Payment" and "Next Payment" columns to Members List CSV export.
 *
 */
function my_pmpro_members_list_csv_extra_columns_payment_dates( $columns ) {
    $columns[ 'last_payment' ] = 'my_extra_column_last_payment';
    $columns[ 'next_payment' ] = 'my_extra_column_next_payment';

    return $columns;
}
add_filter( 'pmpro_members_list_csv_extra_columns', 'my_pmpro_members_list_csv_extra_columns_payment_dates', 10);

/**
 * Populate the "last_payment" column of the Members List CSV export.
 *
 */
function my_extra_column_last_payment( $user ) {
    $order = new MemberOrder();
    $order->getLastMemberOrder( $user->ID, array( 'success', 'cancelled', '' ) );

    if ( ! empty( $order ) && ! empty( $order->id ) ) {
        return date( get_option('date_format'), $order->timestamp );
    } else {
        return '';
    }
}

/**
 * Populate the "next_payment" column of the Members List CSV export.
 *
 */
function my_extra_column_next_payment( $user ) {
    return pmpro_next_payment( $user->ID, array( 'success', 'cancelled', '' ), 'date_format' );
}
