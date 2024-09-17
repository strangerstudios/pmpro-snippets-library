<?php
/**
 * Replace "Renewal" label logics showing "Renewal" for orders which are actually renewals.
 *
 * Orders status column in admin shows a not clear "Renewal" label for returning customers
 * (orders from a customer who already had an order before - might be a renewal or not).
 *
 * Some people just want to see "Renewal" for orders which are actually renewals.
 *
 * title: Show "Renewal" for orders which are actually renewals.
 * layout: snippet
 * collection: orders
 * category: status, renewal
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

add_action( 'pmpro_orders_extra_cols_header', function ( $order_id ) {
	echo '<th class="column-status-v2">' . esc_html__( 'Status', 'paid-memberships-pro' ) . '</th>';
} );

add_action( 'pmpro_orders_extra_cols_body', function ( $order ) {
	?>
    <td class="column-status-v2" data-colname="<?php esc_attr_e( 'Status', 'paid-memberships-pro' ); ?>">
    <span class="pmpro_order-status pmpro_order-status-<?php esc_attr_e( $order->status ); ?>">
                            <?php esc_html_e( ucwords( $order->status ) ); ?>
						</span>

		<?php if ( pmpro_order_is_renewal_in_subscription( $order ) ) { ?>
            <a href="<?php echo esc_url( add_query_arg( array(
				'page' => 'pmpro-orders',
				's'    => $order->subscription_transaction_id
			), admin_url( 'admin.php' ) ) ); ?>"
               title="<?php esc_attr_e( 'View all orders for this subscription', 'paid-memberships-pro' ); ?>"
               class="pmpro_order-renewal"><?php esc_html_e( 'Renewal', 'paid-memberships-pro' ); ?></a>
		<?php } ?>

    </td>
	<?php
} );


/**
 * New definition of Renewal is "any order which is not the first order for a subscription".
 *
 * @param MemberOrder $order
 *
 * @return bool|mixed
 */
function pmpro_order_is_renewal_in_subscription( $order ) {
	global $wpdb;

	// If our property is already set, use that.
	if ( isset( $order->is_renewal ) ) {
		return $order->is_renewal;
	}

	// Can't tell if this is a renewal without a user.
	if ( empty( $order->user_id ) ) {
		$order->is_renewal = false;

		return $order->is_renewal;
	}

	if ( ! empty( $order->subscription_transaction_id ) ) {
		// Logic for recurring orders.
		$original_subscription_order = $order->get_original_subscription_order();
		if ( $order->id !== $original_subscription_order->id ) {
			$order->is_renewal = true;
		} else {
			$order->is_renewal = false;
		}
	} else {
		// Logic for non-recurring orders.
		$sqlQuery       = "SELECT `id`
							 FROM $wpdb->pmpro_membership_orders
							 WHERE `user_id` = '" . esc_sql( $order->user_id ) . "'						 	
								AND `id` <> '" . esc_sql( $order->id ) . "'
								AND `gateway_environment` = '" . esc_sql( $order->gateway_environment ) . "'
								AND `total` > 0
								AND `total` IS NOT NULL
								AND status NOT IN('refunded', 'review', 'token', 'error')
								AND timestamp < '" . esc_sql( date( 'Y-m-d H:i:s', $order->timestamp ) ) . "'
							 LIMIT 1";
		$older_order_id = $wpdb->get_var( $sqlQuery );

		if ( ! empty( $older_order_id ) ) {
			$order->is_renewal = true;
		} else {
			$order->is_renewal = false;
		}
	}

	return $order->is_renewal;
}

add_action( 'admin_footer', function () {
	?>
    <style>
        body.memberships_page_pmpro-orders table.wp-list-table .column-status {
            display: none;
        }
    </style>
	<?php
} );
