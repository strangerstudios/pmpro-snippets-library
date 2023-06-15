
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

 function mypmpro_renewal_date_column_header( $columns ){

	$columns['renewal_date'] = 'Renewal Date';

    return $columns;

}
add_filter( 'pmpro_manage_orderslist_columns',  'mypmpro_renewal_date_column_header', 10, 1 );

function mypmpro_renewal_date_column_body($column_name, $item ) {
	
	if( $column_name == 'renewal_date' ) {

		$order = new MemberOrder( $item );
		
		if ( empty( $order->id ) && empty( $order->subscription_transaction_id ) ) {
			echo '&#8212;';
		} elseif ( $order->status == 'cancelled' ) {
			echo '&#8212;';
		} else {
			// Get the membership level for the last order.
			$level = $order->getMembershipLevel();
		
			if ( ! empty( $level ) && ! empty( $level->id ) && ! empty( $level->cycle_number ) ) {
				// Next Payment Date
				$nextdate = strtotime( '+' . $level->cycle_number . ' ' . $level->cycle_period, $order->getTimestamp() );
				echo date_i18n( get_option( 'date_format' ), $nextdate );
			} else {
				// no order or level found, or level was not recurring
				echo '&#8212;';
			}
		}
	}

}
add_action( 'pmpro_manage_orderlist_custom_column' , 'mypmpro_renewal_date_column_body', 10, 2 );
