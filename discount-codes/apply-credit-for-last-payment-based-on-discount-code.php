<?php
/**
 * Apply a full credit of last payment within 1 year toward checkout when using certain discount codes.
 *
 * Change line 20 to the discount code IDs you want to trigger this behavior.
 * Change line 21 to the membership level IDs to look up last successful order against.
 * Change line 56 if you want to adjust the time period for last payment eligibility.
 * Change line 97 to adjust the messaging shown to members.
 *
 * title: Credit last payment based on discount code at checkout
 * layout: snippet
 * collection: discount-codes
 * category: checkout
 * link: TBD
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_credit_on_code_level( $pmpro_level, $discount_code_id ) {
	global $current_user;

	$eligible_code_ids  = array( 3, 4 ); // Only apply if discount code ID is in this array.
	$source_level_ids   = array( 2, 3 ); // Look for last payment on these membership level IDs.

	// Return early if we do not have a level, level ID, or user to check against.
	if ( empty( $pmpro_level ) || empty( $pmpro_level->id ) || empty( $current_user->ID ) ) {
		return $pmpro_level;
	}

	// Only run for specific discount code IDs.
	if ( ! in_array( (int) $discount_code_id, $eligible_code_ids, true ) ) {
		return $pmpro_level;
	}

	// Get the user's most recent successful order from qualifying levels.
	$args = array(
		'user_id'             => $current_user->ID,
		'status'              => array( 'success' ),
		'membership_level_id' => $source_level_ids,
		'limit'               => 1,
	);
	$orders = MemberOrder::get_orders( $args );

	if ( empty( $orders ) || ! is_array( $orders ) ) {
		return $pmpro_level;
	}

	$last_order = current( $orders );
	if ( empty( $last_order ) || empty( $last_order->timestamp ) ) {
		return $pmpro_level;
	}

	// Must be within 364 days (today included).
	$now = current_time( 'timestamp' );
	if ( (int) $last_order->timestamp < strtotime( '-364 days', $now ) ) {
		return $pmpro_level;
	}

	// Determine full credit amount.
	$credit = isset( $last_order->total ) && $last_order->total !== ''
		? (float) $last_order->total
		: (float) $last_order->subtotal;

	if ( $credit <= 0 ) {
		return $pmpro_level;
	}

	// Apply credit against initial payment (float math, never below zero).
	$initial_payment = isset( $pmpro_level->initial_payment ) ? (float) $pmpro_level->initial_payment : 0.0;
	$pmpro_level->initial_payment = max( 0, $initial_payment - $credit );

	// Store details for messaging.
	$pmpro_level->my_discount_code_credit_amount = $credit;
	$pmpro_level->my_discount_code_credit_date   = (int) $last_order->timestamp;

	return $pmpro_level;
}
add_filter( 'pmpro_discount_code_level', 'my_pmpro_credit_on_code_level', 20, 2 );

/**
 * Append a note to the cost text so members understand their credit.
 */
function my_pmpro_credit_on_code_level_cost_text( $cost, $level ) {
	if ( empty( $level->my_discount_code_credit_amount ) || empty( $level->my_discount_code_credit_date ) ) {
		return $cost;
	}

	$credit_display = function_exists( 'pmpro_formatPrice' )
		? pmpro_formatPrice( (float) $level->my_discount_code_credit_amount )
		: '$' . number_format( (float) $level->my_discount_code_credit_amount, 2 );

	$date = date_i18n( get_option( 'date_format' ), (int) $level->my_discount_code_credit_date );

	$cost .= sprintf(
		'<br><br>This price reflects a %s credit for your last payment on %s.',
		esc_html( $credit_display ),
		esc_html( $date )
	);

	return $cost;
}
add_filter( 'pmpro_level_cost_text', 'my_pmpro_credit_on_code_level_cost_text', 12, 2 );