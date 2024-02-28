<?php
/**
 * Add the "billing address" column back to the members list and CSV export.
 *
 * Note: This will add the most recent billing address for the user and level
 * by looking at the user's order history, not user meta.
 *
 * title: Show billing address on the Members list and Members CSV export.
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
 * Add a "Billing Address" column to the Members List.
 *
 * @param array $columns The columns to display in the Members List.
 * @return array The updated columns to display in the Members List.
 */
function my_pmpro_manage_memberslist_columns_billing_address( $columns ) {
	$columns['billing_address'] = 'Billing Address';
	return $columns;
}
add_filter( 'pmpro_manage_memberslist_columns', 'my_pmpro_manage_memberslist_columns_billing_address' );

/**
 * Display the billing address in the Members List.
 *
 * @param string $column_name The name of the column to display.
 * @param int    $user_id     The ID of the user to display the column for.
 * @param array|null $item The membership information being shown or null.
 */
function my_pmpro_manage_memberslist_column_billing_address( $column_name, $user_id, $item = null ) {
	if ( 'billing_address' === $column_name ) {
		// Get the most recent order for the user.
		$order = new MemberOrder();
		$order->getLastMemberOrder( $user_id, 'success', ( empty( $item ) || empty( $item['membership_id'] ) ) ? null : $item['membership_id'] );

		// Get the billing address from the order.
		$r = '';

		if ( ! empty( $order->billing->name ) ) {
			$r .= esc_html( $order->billing->name ) . '<br />';
		}

		if ( ! empty( $order->billing->street ) ) {
			$r .= esc_html( $order->billing->street ) . '<br />';
		}

		if ( $order->billing->city && $order->billing->state ) {
			$r .= esc_html( $order->billing->city ) . ', ';
			$r .= esc_html( $order->billing->state ) . ' ';
			$r .= esc_html( $order->billing->zip ) . ' ';
			if ( ! empty( $order->billing->country ) ) {
				$r .= esc_html( $order->billing->country );
			}
		}

		if ( ! empty( $order->billing->phone ) ) {
			$r .= '<br />' . esc_html( formatPhone( $order->billing->phone ) );
		}

		// If this column is completely empty, set $r to a dash.
		if ( empty( $r ) ) {
			$r .= '&#8212;';
		}

		// Echo the data for this column.
		echo $r;
	}
}

/**
 * Hooks the my_pmpro_manage_memberslist_column_billing_address() function
 * with 2 parameters if using PMPro v2.x or 3 parameters if using PMPro v3.0+.
 */
function my_pmpro_hook_my_pmpro_manage_memberslist_column_billing_address() {
	if ( class_exists( 'PMPro_Subscription' ) ) {
		add_action( 'pmpro_manage_memberslist_custom_column', 'my_pmpro_manage_memberslist_column_billing_address', 10, 3 );
	} else {
		add_action( 'pmpro_manage_memberslist_custom_column', 'my_pmpro_manage_memberslist_column_billing_address', 10, 2 );
	}
}
add_action( 'admin_init', 'my_pmpro_hook_my_pmpro_manage_memberslist_column_billing_address' );

/**
 * Add "billing_address" columns to CSV export
 *
 * @param array $columns The columns to be exported with their callbacks.
 * @return array
 */
function my_pmpro_members_list_csv_extra_columns_billing_address( $columns ) {
	$new_columns = array(
		'bname' => 'my_pmpro_members_list_csv_extra_columns_bname_callback',
		'bstreet' => 'my_pmpro_members_list_csv_extra_columns_bstreet_callback',
		'bcity' => 'my_pmpro_members_list_csv_extra_columns_bcity_callback',
		'bstate' => 'my_pmpro_members_list_csv_extra_columns_bstate_callback',
		'bzip' => 'my_pmpro_members_list_csv_extra_columns_bzip_callback',
		'bcountry' => 'my_pmpro_members_list_csv_extra_columns_bcountry_callback',
		'bphone' => 'my_pmpro_members_list_csv_extra_columns_bphone_callback',
	);
	
	$columns = array_merge($columns, $new_columns);
	
	return $columns;
}
add_filter('pmpro_members_list_csv_extra_columns', 'my_pmpro_members_list_csv_extra_columns_billing_address');

/**
 * Callback for "bname" column in CSV export
 *
 * @param object $user The user object for the row with some additional membership data.
 * @return string
 */
function my_pmpro_members_list_csv_extra_columns_bname_callback($user) {
	global $cache_billing_address_order;
	$cache_billing_address_order = new MemberOrder();
	$cache_billing_address_order->getLastMemberOrder($user->ID, 'success', $user->membership_id);
	return empty($cache_billing_address_order->billing->name) ? '' : $cache_billing_address_order->billing->name;
}

/**
 * Callback for "bstreet" column in CSV export
 *
 * @param object $user The user object for the row with some additional membership data.
 * @return string
 */
function my_pmpro_members_list_csv_extra_columns_bstreet_callback($user) {
	global $cache_billing_address_order;
	return empty($cache_billing_address_order->billing->street) ? '' : $cache_billing_address_order->billing->street;
}

/**
 * Callback for "bcity" column in CSV export
 *
 * @param object $user The user object for the row with some additional membership data.
 * @return string
 */
function my_pmpro_members_list_csv_extra_columns_bcity_callback($user) {
	global $cache_billing_address_order;
	return empty($cache_billing_address_order->billing->city) ? '' : $cache_billing_address_order->billing->city;
}

/**
 * Callback for "bstate" column in CSV export
 *
 * @param object $user The user object for the row with some additional membership data.
 * @return string
 */
function my_pmpro_members_list_csv_extra_columns_bstate_callback($user) {
	global $cache_billing_address_order;
	return empty($cache_billing_address_order->billing->state) ? '' : $cache_billing_address_order->billing->state;
}

/**
 * Callback for "bzip" column in CSV export
 *
 * @param object $user The user object for the row with some additional membership data.
 * @return string
 */
function my_pmpro_members_list_csv_extra_columns_bzip_callback($user) {
	global $cache_billing_address_order;
	return empty($cache_billing_address_order->billing->zip) ? '' : $cache_billing_address_order->billing->zip;
}

/**
 * Callback for "bcountry" column in CSV export
 *
 * @param object $user The user object for the row with some additional membership data.
 * @return string
 */
function my_pmpro_members_list_csv_extra_columns_bcountry_callback($user) {
	global $cache_billing_address_order;
	return empty($cache_billing_address_order->billing->country) ? '' : $cache_billing_address_order->billing->country;
}

/**
 * Callback for "bphone" column in CSV export
 *
 * @param object $user The user object for the row with some additional membership data.
 * @return string
 */
function my_pmpro_members_list_csv_extra_columns_bphone_callback($user) {
	global $cache_billing_address_order;
	return empty($cache_billing_address_order->billing->phone) ? '' : $cache_billing_address_order->billing->phone;
}
