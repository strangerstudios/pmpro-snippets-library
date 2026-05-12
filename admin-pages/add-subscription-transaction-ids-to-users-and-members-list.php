<?php
/**
 * Display Order Transaction IDs and Allow Search by ID in the WordPress Admin.
 *
 * title: Display Order Transaction IDs and Allow Search by ID in the WordPress Admin.
 * layout: snippet
 * collection: admin-pages
 * category: admin
 * link: https://www.paidmembershipspro.com/display-order-transaction-ids-and-allow-search-by-id-in-the-wordpress-admin/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Add Transaction IDs column to Members List.
 */
function tids_pmpro_memberslist_extra_cols_header( $columns ) {
	$columns['transaction_ids'] = esc_html__( 'Transaction IDs', 'pmpro-snippets-library' );
	return $columns;
}
add_action( 'pmpro_manage_memberslist_columns', 'tids_pmpro_memberslist_extra_cols_header' );

/**
 * Populate Transaction IDs Members List column.
 */
function tids_pmpro_memberslist_extra_cols_body( $column_name, $user_id, $item ) {
	if ( 'transaction_ids' === $column_name ) {
		$order = new MemberOrder();
		$order->getLastMemberOrder( $user_id, 'success', $item['membership_id'] );

		echo esc_html__( 'Payment', 'pmpro-snippets-library' ) . ': <br />';
		if ( ! empty( $order->payment_transaction_id ) ) {
			echo '<a href="' . esc_html( admin_url( 'admin.php?page=pmpro-orders&order=' . $order->id ) ) . '">' . esc_html( $order->payment_transaction_id ) . '</a>';
		} else {
			esc_html_e( 'N/A', 'pmpro-snippets-library' );
		}
		echo '<br />';
		echo esc_html__( 'Subscription', 'pmpro-snippets-library' ) . ': <br />';
		if ( ! empty( $order->subscription_transaction_id ) ) {
			echo '<a href="' . esc_html( admin_url( 'admin.php?page=pmpro-orders&order=' . $order->id ) ) . '">' . esc_html( $order->subscription_transaction_id ) . '</a>';
		} else {
			esc_html_e( 'N/A', 'pmpro-snippets-library' );
		}
	}
}
add_action( 'pmpro_manage_memberslist_custom_column', 'tids_pmpro_memberslist_extra_cols_body', 10, 3 );

/**
 * Add Transaction ID column to members list CSV.
 */
function tids_pmpro_members_list_csv_extra_columns( $columns ) {
	$columns['payment_transaction_id'] = 'tids_csv_columns_get_payment_transaction_id';
	return $columns;
}
add_filter( 'pmpro_members_list_csv_extra_columns', 'tids_pmpro_members_list_csv_extra_columns' );

/**
 * Populate Payment Transaction ID members list CSV columns.
 */
function tids_csv_columns_get_payment_transaction_id( $theuser ) {
	$order = new MemberOrder();
	$order->getLastMemberOrder( $theuser->ID, 'success', $theuser->membership_id );

	if ( ! empty( $order->payment_transaction_id ) ) {
		return $order->payment_transaction_id;
	} else {
		return '';
	}
}

/**
 * Search Payment Transaction IDs on Members List Page.
 */
function tids_pmpro_members_list_sql( $sql_query ) {
	global $wpdb;

	$s = isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : '';

	if ( ! empty( $s ) && strlen( $s ) > 8 ) {
		// Look for orders with this payment id.
		$user_ids = $wpdb->get_col( "SELECT user_id FROM $wpdb->pmpro_membership_orders WHERE payment_transaction_id LIKE '%" . esc_sql( $s ) . "%' GROUP BY user_id" );

		if ( ! empty( $user_ids ) ) {
			// Some vars for the search.
			$l = isset( $_REQUEST['l'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['l'] ) ) : '';

			if ( isset( $_REQUEST['pn'] ) ) {
				$pn = sanitize_text_field( wp_unslash( $_REQUEST['pn'] ) );
			} else {
				$pn = 1;
			}

			if ( isset( $_REQUEST['limit'] ) ) {
				$limit = sanitize_text_field( wp_unslash( $_REQUEST['limit'] ) );
			} else {
				$limit = 15;
			}

			$end   = $pn * $limit;
			$start = $end - $limit;

			// Filter results to only include these user ids.
			$sql_query = "SELECT SQL_CALC_FOUND_ROWS u.ID, u.user_login, u.user_email, u.display_name, UNIX_TIMESTAMP(u.user_registered) as joindate, mu.membership_id, mu.initial_payment, mu.billing_amount, mu.cycle_period, mu.cycle_number, mu.billing_limit, mu.trial_amount, mu.trial_limit, UNIX_TIMESTAMP(mu.startdate) as startdate, UNIX_TIMESTAMP(mu.enddate) as enddate, m.name as membership FROM $wpdb->users u LEFT JOIN $wpdb->usermeta um ON u.ID = um.user_id LEFT JOIN $wpdb->pmpro_memberships_users mu ON u.ID = mu.user_id LEFT JOIN $wpdb->pmpro_membership_levels m ON mu.membership_id = m.id ";

			if ( 'oldmembers' === $l ) {
				$sql_query .= " LEFT JOIN $wpdb->pmpro_memberships_users mu2 ON u.ID = mu2.user_id AND mu2.status = 'active' ";
			}

			// This is the line changed.
			$sql_query .= ' WHERE u.ID IN(' . implode( ',', $user_ids ) . ') ';

			if ( 'oldmembers' === $l ) {
				$sql_query .= " AND mu.status = 'inactive' AND mu2.status IS NULL ";
			} elseif ( $l ) {
				$sql_query .= " AND mu.status = 'active' AND mu.membership_id = '" . $l . "' ";
			} else {
				$sql_query .= " AND mu.status = 'active' ";
			}

			$sql_query .= 'GROUP BY u.ID ';

			if ( 'oldmembers' === $l ) {
				$sql_query .= 'ORDER BY enddate DESC ';
			} else {
				$sql_query .= 'ORDER BY u.user_registered DESC ';
			}

			$sql_query .= "LIMIT $start, $limit";
		}
	}

	return $sql_query;
}
add_filter( 'pmpro_members_list_sql', 'tids_pmpro_members_list_sql' );

/**
 * Add Transaction Ids column to WordPress Users list.
 */
function tids_manage_users_columns( $columns ) {
	$columns['tids_transaction_ids'] = __( 'Transaction IDs', 'pmpro-snippets-library' );
	return $columns;
}
add_filter( 'manage_users_columns', 'tids_manage_users_columns' );

/**
 * Populate Transaction Ids WordPress Users list column.
 */
function tids_manage_users_custom_column( $column_data, $column_name, $user_id ) {
	// Make sure PMPro is installed.
	if ( ! class_exists( 'MemberOrder' ) ) {
		return $column_data;
	}

	if ( 'tids_transaction_ids' === $column_name ) {
		$order = new MemberOrder();
		$order->getLastMemberOrder( $user_id, '' );

		$column_data = esc_html__( 'Payment', 'pmpro-snippets-library' ) . ': ';
		if ( ! empty( $order ) && ! empty( $order->payment_transaction_id ) ) {
			$column_data .= '<a href="' . esc_html( admin_url( 'admin.php?page=pmpro-orders&order=' . $order->id ) ) . '">' . esc_html( $order->payment_transaction_id ) . '</a>';
		} else {
			$column_data .= esc_html__( 'N/A', 'pmpro-snippets-library' );
		}
		$column_data .= '<br />' . esc_html__( 'Subscription', 'pmpro-snippets-library' ) . ': ';
		if ( ! empty( $order ) && ! empty( $order->subscription_transaction_id ) ) {
			$column_data .= '<a href="' . esc_html( admin_url( 'admin.php?page=pmpro-orders&order=' . $order->id ) ) . '">' . esc_html( $order->subscription_transaction_id ) . '</a>';
		} else {
			$column_data .= esc_html__( 'N/A', 'pmpro-snippets-library' );
		}
	}
	return $column_data;
}
add_filter( 'manage_users_custom_column', 'tids_manage_users_custom_column', 10, 3 );

/**
 * Add some fields to search in users list.
 */
function tids_pre_user_query( $user_query ) {
	// Make sure this is only applied to user search.
	if ( $user_query->query_vars['search'] ) {
		$search = trim( $user_query->query_vars['search'], '*' );
		if ( isset( $_REQUEST['s'] ) && $_REQUEST['s'] === $search ) {
			global $wpdb;

			// Look for transaction ids.
			if ( strlen( $search ) > 8 ) {
				$user_ids = $wpdb->get_col( "SELECT user_id FROM $wpdb->pmpro_membership_orders WHERE subscription_transaction_id lIKE '%" . esc_sql( $search ) . "%' OR payment_transaction_id LIKE '%" . esc_sql( $search ) . "%' GROUP BY user_id" );
			}

			if ( ! empty( $user_ids ) ) {
				$user_query->query_where = 'WHERE 1=1 AND ID IN(' . implode( ',', $user_ids ) . ') ';
			} else {
				$user_query->query_from .= " LEFT JOIN $wpdb->usermeta MF ON MF.user_id = {$wpdb->users}.ID AND MF.meta_key = 'first_name'";
				$user_query->query_from .= " LEFT JOIN $wpdb->usermeta ML ON ML.user_id = {$wpdb->users}.ID AND ML.meta_key = 'last_name'";
				$user_query->query_from .= " LEFT JOIN $wpdb->usermeta AE ON AE.user_id = {$wpdb->users}.ID AND AE.meta_key = 'AdditionalEmail'";

				$user_query->query_where = 'WHERE 1=1' . $user_query->get_search_sql( $search, array( 'user_login', 'user_email', 'user_nicename', 'MF.meta_value', 'ML.meta_value', 'AE.meta_value' ), 'both' );
			}
		}
	}
}
add_action( 'pre_user_query', 'tids_pre_user_query' );

/**
 * Add Transaction Ids to edit user page.
 */
function tids_profile_fields( $user ) {
	$order = new MemberOrder();
	$order->getLastMemberOrder( $user->ID );
	?>
		<h3>
			<?php esc_html_e( 'Transaction IDs', 'pmpro-snippets-library' ); ?>
		</h3>
		<p>
			<?php esc_html_e( 'Payment', 'pmpro-snippets-library' ); ?>: 
			<?php
			if ( ! empty( $order->payment_transaction_id ) ) {
				echo '<a href="' . esc_html( admin_url( 'admin.php?page=pmpro-orders&order=' . $order->id ) ) . '">' . esc_html( $order->payment_transaction_id ) . '</a>';
			} else {
				esc_html_e( 'N/A', 'pmpro-snippets-library' );
			}
			?>
			<br />
			<?php esc_html_e( 'Subscription', 'pmpro-snippets-library' ); ?>: 
			<?php
			if ( ! empty( $order->subscription_transaction_id ) ) {
				echo '<a href="' . esc_html( admin_url( 'admin.php?page=pmpro-orders&order=' . $order->id ) ) . '">' . esc_html( $order->subscription_transaction_id ) . '</a>';
			} else {
				esc_html_e( 'N/A', 'pmpro-snippets-library' );
			}
			?>
		</p>
	<?php
}
add_action( 'show_user_profile', 'tids_profile_fields' );
add_action( 'edit_user_profile', 'tids_profile_fields' );
