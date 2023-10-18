
<?php
/**
 * Show the renewal date for the order on the Memberships > Orders page in the WordPress admin.
 *
 * Note that the "renewal date" value is an estimate based on the billing cycle of the subscription and the last order date. It may be off from the actual recurring date set at the gateway, especially if the subscription was updated at the gateway.
 *
 * title: Show renewal date on the Orders list in the WordPress admin.
 * layout: snippet
 * collection: admin-pages
 * category: admin
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_renewal_date_orders_extra_cols_header( $order_ids ) {
	echo '<td>Renewal Date</td>';
}
add_action( 'pmpro_orders_extra_cols_header', 'my_pmpro_renewal_date_orders_extra_cols_header' );

function my_pmpro_renewal_date_orders_extra_cols_body( $order ) {
	if ( empty( $order->id ) && empty( $order->subscription_transaction_id ) ) {
		echo '<td>&#8212;</td>';
	} elseif ( $order->status == 'cancelled' ) {
		echo '<td>&#8212;</td>';
	} else {
		// Get the membership level for the last order.
		$level = $order->getMembershipLevel();

		if ( ! empty( $level ) && ! empty( $level->id ) && ! empty( $level->cycle_number ) ) {
			// Next Payment Date
			$nextdate = strtotime( '+' . $level->cycle_number . ' ' . $level->cycle_period, $order->getTimestamp() );
			echo '<td>' . date_i18n( get_option( 'date_format' ), $nextdate ) . '</td>';
		} else {
			// no order or level found, or level was not recurring
			echo '<td>&#8212;</td>';
		}
	}
}
add_action( 'pmpro_orders_extra_cols_body', 'my_pmpro_renewal_date_orders_extra_cols_body' );
