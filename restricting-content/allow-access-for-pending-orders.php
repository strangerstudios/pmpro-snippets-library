<?php
/**
 * Allow users to access restricted content if they have a pending order for a level that grants access.
 * This is specifically helpful to allow Pay By Check users to access content while their check is pending.
 *
 * title: Allow Access for Users with Pending Orders
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/*
 * @param bool $hasaccess Whether the user has access to the content.
 * @param WP_Post $post The post object.
 * @param WP_User $user The user object.
 * @param array $post_membership_levels The membership levels associated with the post.
 * @return bool
 */
function my_pmpro_has_membership_access_filter_pending_orders( $hasaccess, $post, $user, $post_membership_levels ) {
	// If the user already has access, return early.
	if ( $hasaccess ) {
		return $hasaccess;
	}

	// If the user is not logged in, return early.
	if ( empty( $user->ID ) ) {
		return $hasaccess;
	}

	// Get all of the pending orders for the user.
	$get_orders_params = array(
		'status' => 'pending',
		'user_id' => $user->ID,
	);
	$pending_orders = MemberOrder::get_orders( $get_orders_params );

	// If there are no pending orders, return early.
	if ( empty( $pending_orders ) ) {
		return $hasaccess;
	}

	// If any of the orders is for a level that grants access, return true.
	$post_level_ids = wp_list_pluck( $post_membership_levels, 'id' );
	foreach ( $pending_orders as $order ) {
		if ( in_array( $order->membership_id, $post_level_ids ) ) {
			return true;
		}
	}

	return $hasaccess;
}
add_filter( 'pmpro_has_membership_access_filter', 'my_pmpro_has_membership_access_filter_pending_orders', 10, 4 );