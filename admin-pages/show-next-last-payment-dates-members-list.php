<?php
/**
 * Show the member's last payment date and next payment date on the Members list and Members CSV export.
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
	$columns[ 'last_payment_date' ] = 'Last Payment';
	$columns[ 'next_payment_date' ] = 'Next Payment';
	return $columns;
}
add_filter( 'pmpro_manage_memberslist_columns', 'my_pmpro_add_memberslist_col_payment_dates' );

/**
 * Fills the "last-payment" and "next-payment" columns of the Members List.
 *
 * @param  string $colname column being filled.
 * @param  string $user_id to get information for.
 * @param  array  $item The membership data being shown.
 */
function my_pmpro_fill_memberslist_col_payment_dates( $colname, $user_id, $item ) {
	if ( 'last_payment_date' === $colname ) {
		$last_order = new MemberOrder();
		$last_order->getLastMemberOrder( $user_id, array( 'success', 'refunded' ), $item['membership_id'] );

		if ( ! empty( $last_order ) && ! empty( $last_order->id ) ) {
			echo esc_html( sprintf(
				// translators: %1$s is the date and %2$s is the time.
				__( '%1$s at %2$s', 'pmpro-customizations' ),
				date( get_option('date_format'), $last_order->timestamp ),
				date( get_option('time_format'), $last_order->timestamp )
			) );
		} else {
			echo 'N/A';
		}
	}

	if ( 'next_payment_date' === $colname ) {
		// Get the subscription.
		$subscriptions =  PMPro_Subscription::get_subscriptions_for_user( $user_id, $item['membership_id'] );

		// Return early if there are no subscriptions.
		if ( empty( $subscriptions ) ) {
			echo 'N/A';
			return;
		}

		// Show the next payment date if the subscription is active.
		$subscription = $subscriptions[0];
		$next_payment_date = $subscriptions[0]->get_next_payment_date( get_option( 'date_format' ) );
		$next_payment_time = $subscriptions[0]->get_next_payment_date( get_option( 'time_format' ) );
		if ( ! empty( $next_payment_date ) ) {
			echo esc_html( sprintf(
				// translators: %1$s is the date and %2$s is the time.
				__( '%1$s at %2$s', 'pmpro-customizations' ),
				esc_html( $next_payment_date ),
				esc_html( $next_payment_time )
			) );
		} else {
			echo 'N/A';
		}
	}
}
add_filter( 'pmpro_manage_memberslist_custom_column', 'my_pmpro_fill_memberslist_col_payment_dates', 10, 3 );

/**
 * Adds "last_payment_date" column to Members List CSV export.
 * Note: PMPro v3.0+ already includes "next_payment_date" in the CSV export.
 *
 */
function my_pmpro_members_list_csv_extra_columns_payment_dates( $columns ) {
	$columns[ 'last_payment_date' ] = 'my_extra_column_last_payment_date';

	return $columns;
}
add_filter( 'pmpro_members_list_csv_extra_columns', 'my_pmpro_members_list_csv_extra_columns_payment_dates', 10);

/**
 * Populate the "last_payment_date" column of the Members List CSV export.
 *
 */
function my_extra_column_last_payment_date( $user ) {
	// Get the last order.
	$last_order = new MemberOrder();
	$last_order->getLastMemberOrder( $user->ID, array( 'success', 'refunded' ), $user->membership_id );

	// Use the core filter to get date format for exports.
	$dateformat = apply_filters( 'pmpro_memberslist_csv_dateformat', 'Y-m-d' );

	// Show the last payment date if the order exists and was not free.
	if ( ! empty( $last_order ) && ! empty( $last_order->id ) && $last_order->total > 0 ) {
		return date_i18n( $dateformat, $last_order->timestamp );
	} else {
		return '';
	}
}
