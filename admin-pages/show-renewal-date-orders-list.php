
<?php
/**
 * Show the next payment date for the order's subscription on the Memberships > Orders page in the WordPress admin.
 *
 * title: Show next payment date for the subscription on the Orders list in the WordPress admin.
 * layout: snippet
 * collection: admin-pages
 * category: admin
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_next_payment_date_orders_column_header( $columns ) {
	$columns['next_payment_date'] = 'Next Payment Date';

    return $columns;
}
add_filter( 'pmpro_manage_orderslist_columns',  'my_pmpro_next_payment_date_orders_column_header', 10, 1 );

function my_pmpro_next_payment_date_orders_column_body( $column_name, $item ) {
	if ( $column_name == 'next_payment_date' ) {
		// Get the order.
		$order = new MemberOrder( $item );

		// Return early if the order is not a subscription.
		if ( empty( $order->id ) && empty( $order->subscription_transaction_id ) ) {
			echo '&#8212;';
		}

		// Get the subscription.
		$subscription = $order->get_subscription();

		// Return early if there are no subscriptions.
		if ( empty( $subscription ) ) {
			echo 'N/A';
			return;
		}

		// Show the next payment date if the subscription is active.
		$next_payment_date = $subscription->get_next_payment_date( get_option( 'date_format' ) );
		$next_payment_time = $subscription->get_next_payment_date( get_option( 'time_format' ) );
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
add_action( 'pmpro_manage_orderlist_custom_column' , 'my_pmpro_next_payment_date_orders_column_body', 10, 2 );
