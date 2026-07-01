<?php
/**
 * Prorate seat changes at checkout for existing Group Accounts parent members.
 *
 * For parent members on Variable-seat, recurring Group Accounts levels, this snippet
 * adjusts the checkout flow so that seat changes are handled fairly mid-cycle. When
 * adding seats, only a prorated amount is charged for the remaining days in the current
 * billing cycle. When removing seats, nothing is charged today — the seat count reduction
 * is deferred until the next renewal so the parent retains access to the seats they
 * already paid for. In both cases, the existing subscription billing date is preserved.
 *
 * This snippet has no effect on Fixed-seat group levels or non-recurring parent levels.
 *
 * title: Prorate Seat Changes at Checkout for Group Accounts Parent Members
 * layout: snippet
 * collection: add-ons, group-accounts
 * category: group-accounts, billing, checkout
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * For existing group parents on Variable-seat, recurring levels, show current seat count
 * and pre-fill the seats field. Runs before the group-accounts plugin's own
 * pmpro_checkout_boxes hook (priority 10).
 *
 * Fixed-seat groups and non-recurring levels are skipped entirely.
 */
function my_pmpro_group_parent_existing_seats_notice() {
	if ( ! function_exists( 'pmprogroupacct_get_settings_for_level' ) || ! class_exists( 'PMProGroupAcct_Group' ) ) {
		return;
	}

	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		return;
	}

	$level = pmpro_getLevelAtCheckout();
	if ( empty( $level->id ) ) {
		return;
	}

	$settings = pmprogroupacct_get_settings_for_level( $level->id );
	if ( empty( $settings ) ) {
		return;
	}

	// Only show for Variable-seat groups. Fixed groups have no editable #pmprogroupacct_seats
	// field at checkout, so showing "enter the new total you want" would be misleading.
	// Variable groups are identified by min_seats !== max_seats; Fixed groups lock both to
	// the same value since there is no range to choose from.
	if ( ! isset( $settings['min_seats'], $settings['max_seats'] ) || (int) $settings['min_seats'] === (int) $settings['max_seats'] ) {
		return;
	}

	// Only show for recurring levels. Non-recurring levels charge the full cost of all
	// seats as a fresh purchase — proration does not apply and the notice would be misleading.
	if ( ! pmpro_isLevelRecurring( $level ) ) {
		return;
	}

	$existing_group = PMProGroupAcct_Group::get_group_by_parent_user_id_and_parent_level_id( $user_id, $level->id );
	if ( empty( $existing_group ) ) {
		return;
	}

	$existing_seats = intval( $existing_group->group_total_seats );
	$ajax_url       = admin_url( 'admin-ajax.php' );
	$ajax_nonce     = wp_create_nonce( 'my_pmpro_seats_cost_text' );
	?>
	<script>
	document.addEventListener( 'DOMContentLoaded', function () {
		var field = document.getElementById( 'pmprogroupacct_seats' );
		if ( ! field ) {
			return;
		}
		// Pre-fill with existing seat count.
		field.value = <?php echo (int) $existing_seats; ?>;

		// Live-update the level cost text whenever the seat count changes.
		var levelId    = <?php echo (int) $level->id; ?>;
		var ajaxUrl    = <?php echo wp_json_encode( $ajax_url ); ?>;
		var ajaxNonce  = <?php echo wp_json_encode( $ajax_nonce ); ?>;
		var debounceId = null;

		function refreshLevelCostText() {
			var target = document.querySelector( '.pmpro_level_cost_text' );
			if ( ! target ) {
				return;
			}
			var seats = parseInt( field.value, 10 );
			if ( isNaN( seats ) ) {
				return;
			}
			var body = new URLSearchParams();
			body.append( 'action', 'my_pmpro_seats_cost_text' );
			body.append( '_ajax_nonce', ajaxNonce );
			body.append( 'level_id', levelId );
			body.append( 'pmprogroupacct_seats', seats );

			fetch( ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: body.toString()
			} )
			.then( function ( r ) { return r.json(); } )
			.then( function ( data ) {
				if ( data && data.success && data.data && typeof data.data.cost_text === 'string' ) {
					target.innerHTML = data.data.cost_text;
				}
			} )
			.catch( function () { /* ignore */ } );
		}

		field.addEventListener( 'input', function () {
			clearTimeout( debounceId );
			debounceId = setTimeout( refreshLevelCostText, 250 );
		} );
	} );
	</script>
	<p class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_message' ) ); ?>">
		<?php
		printf(
			/* translators: %s: current seat count */
			esc_html( _n( 'You currently have %s seat. Enter the new total number of seats you want.', 'You currently have %s seats. Enter the new total number of seats you want.', $existing_seats, 'pmpro-group-accounts' ) ),
			'<strong>' . esc_html( number_format_i18n( $existing_seats ) ) . '</strong>'
		);
		?>
		<br />
		<?php
		$next_payment_date_str = '';
		if ( class_exists( 'PMPro_Subscription' ) ) {
			$subscriptions = PMPro_Subscription::get_subscriptions_for_user( $user_id, $level->id );
			if ( ! empty( $subscriptions ) ) {
				$next_payment_ts = $subscriptions[0]->get_next_payment_date( 'timestamp' );
				if ( ! empty( $next_payment_ts ) ) {
					$next_payment_date_str = date_i18n( get_option( 'date_format' ), $next_payment_ts );
				}
			}
		}

		if ( ! empty( $next_payment_date_str ) ) {
			printf(
				/* translators: %s: next billing date */
				esc_html__( 'If you add seats, you will only be charged a prorated amount today for the remaining days in your current billing cycle. If you remove seats, you will not be charged today. Either way, your next billing date stays %s and the new seat total takes effect then.', 'pmpro-group-accounts' ),
				'<strong>' . esc_html( $next_payment_date_str ) . '</strong>'
			);
		} else {
			esc_html_e( 'If you add seats, you will only be charged a prorated amount today for the remaining days in your current billing cycle. If you remove seats, you will not be charged today. Your billing date stays the same.', 'pmpro-group-accounts' );
		}
		?>
	</p>
	<?php
}
add_action( 'pmpro_checkout_boxes', 'my_pmpro_group_parent_existing_seats_notice', 5 );

/**
 * Ajax handler: return the formatted level cost text for the given level + seat count.
 */
function my_pmpro_seats_cost_text_ajax() {
	check_ajax_referer( 'my_pmpro_seats_cost_text' );

	$level_id = isset( $_POST['level_id'] ) ? intval( $_POST['level_id'] ) : 0;
	if ( ! $level_id || ! function_exists( 'pmpro_getLevelAtCheckout' ) ) {
		wp_send_json_error();
	}

	$level = pmpro_getLevelAtCheckout( $level_id );
	if ( empty( $level ) ) {
		wp_send_json_error();
	}

	wp_send_json_success( array(
		'cost_text' => pmpro_getLevelCost( $level ),
	) );
}
add_action( 'wp_ajax_my_pmpro_seats_cost_text', 'my_pmpro_seats_cost_text_ajax' );
add_action( 'wp_ajax_nopriv_my_pmpro_seats_cost_text', 'my_pmpro_seats_cost_text_ajax' );

/**
 * Prorate a group account parent level checkout when an existing parent adds or removes seats.
 *
 * Runs after pmprogroupacct_pmpro_checkout_level_parent (priority 10).
 *
 * Upgrade: charges a prorated initial payment for additional seats only, preserving the
 * existing billing anchor date.
 *
 * Downgrade: charges $0 today, preserves the billing anchor date, and stores a pending
 * seat reduction in user meta so the seat count only drops at the next renewal — not
 * immediately. The member already paid for the current cycle's seats.
 */
function my_pmpro_prorate_parent_seat_upgrade( $level ) {
	if ( ! function_exists( 'pmprogroupacct_get_settings_for_level' ) || ! class_exists( 'PMProGroupAcct_Group' ) ) {
		return $level;
	}

	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		return $level;
	}

	$settings = pmprogroupacct_get_settings_for_level( $level->id );
	if ( empty( $settings ) || $settings['pricing_model'] !== 'fixed' ) {
		return $level;
	}

	// Only act on Variable-seat groups. Fixed groups have a locked seat count
	// with no checkout input to change it. Variable groups are identified by
	// min_seats !== max_seats; Fixed groups lock both to the same value.
	if ( ! isset( $settings['min_seats'], $settings['max_seats'] ) || (int) $settings['min_seats'] === (int) $settings['max_seats'] ) {
		return $level;
	}

	// Only prorate recurring levels.
	if ( ! pmpro_isLevelRecurring( $level ) ) {
		return $level;
	}

	// Only act when an existing group is being updated.
	$existing_group = PMProGroupAcct_Group::get_group_by_parent_user_id_and_parent_level_id( $user_id, $level->id );
	if ( empty( $existing_group ) ) {
		return $level;
	}

	$price_per_seat  = (float) $settings['pricing_model_settings'];
	$new_total_seats = intval( isset( $_REQUEST['pmprogroupacct_seats'] ) ? $_REQUEST['pmprogroupacct_seats'] : $settings['min_seats'] );
	$existing_seats  = intval( $existing_group->group_total_seats );

	$is_downgrade     = $new_total_seats < $existing_seats;
	$additional_seats = $is_downgrade ? 0 : ( $new_total_seats - $existing_seats );

	// Fetch the existing subscription's next payment date.
	$existing_next_payment_ts = 0;
	if ( class_exists( 'PMPro_Subscription' ) ) {
		$subscriptions = PMPro_Subscription::get_subscriptions_for_user( $user_id, $level->id );
		if ( ! empty( $subscriptions ) ) {
			$existing_next_payment_ts = (int) $subscriptions[0]->get_next_payment_date( 'timestamp' );
		}
	}

	// Mid-cycle proration ratio for upgrades.
	$proration_ratio = 1.0;
	if ( ! empty( $existing_next_payment_ts ) && ! empty( $level->cycle_number ) && ! empty( $level->cycle_period ) ) {
		$now            = current_time( 'timestamp' );
		$cycle_seconds  = strtotime( '+' . (int) $level->cycle_number . ' ' . $level->cycle_period, $now ) - $now;
		$remaining_secs = max( 0, $existing_next_payment_ts - $now );
		if ( $cycle_seconds > 0 ) {
			$proration_ratio = min( 1.0, $remaining_secs / $cycle_seconds );
		}
	}

	switch ( $settings['price_application'] ) {
		case 'both':
		case 'initial':
			$level->initial_payment = $additional_seats * $price_per_seat * $proration_ratio;
			if ( $level->initial_payment > 0 && $level->initial_payment < 0.5 ) {
				$level->initial_payment = 1;
			}
			break;
	}

	// Preserve the existing billing anchor for both upgrades and downgrades.
	if ( ! empty( $existing_next_payment_ts ) ) {
		$level->profile_start_date = date( 'Y-m-d H:i:s', $existing_next_payment_ts );
	}

	// For downgrades: store a pending reduction so we can defer the seat count
	// drop until the next renewal. The member paid for the current cycle's seats
	// and should keep access to them until the billing date.
	if ( $is_downgrade ) {
		update_user_meta( $user_id, '_my_pmpro_pending_seat_reduction', array(
			'level_id'       => $level->id,
			'new_seats'      => $new_total_seats,
			'existing_seats' => $existing_seats,
		) );
	} else {
		// Upgrade or same-seat checkout: clear any stale pending reduction.
		delete_user_meta( $user_id, '_my_pmpro_pending_seat_reduction' );
	}

	return $level;
}
add_filter( 'pmpro_checkout_level', 'my_pmpro_prorate_parent_seat_upgrade', 20 );

/**
 * After a successful downgrade checkout, revert group_total_seats to the existing count.
 *
 * Group Accounts writes the new (lower) seat count to the DB during checkout. We run at
 * priority 999 to revert it back — the member paid for the current cycle's seats, so they
 * should keep them until the next billing date.
 *
 * The pending meta stored in my_pmpro_prorate_parent_seat_upgrade() is used to identify
 * the correct group and seat counts to restore.
 */
function my_pmpro_defer_seat_reduction_after_checkout( $user_id, $morder ) {
	global $wpdb;

	if ( ! class_exists( 'PMProGroupAcct_Group' ) ) {
		return;
	}

	$pending = get_user_meta( $user_id, '_my_pmpro_pending_seat_reduction', true );
	if ( empty( $pending ) || empty( $pending['level_id'] ) || ! isset( $pending['existing_seats'] ) ) {
		return;
	}

	// Only act if this order is for the same level as the pending reduction.
	if ( ! empty( $morder->membership_id ) && (int) $morder->membership_id !== (int) $pending['level_id'] ) {
		return;
	}

	$existing_group = PMProGroupAcct_Group::get_group_by_parent_user_id_and_parent_level_id( $user_id, $pending['level_id'] );
	if ( empty( $existing_group ) ) {
		return;
	}

	// Revert group_total_seats to the pre-downgrade count so the member retains
	// their current cycle's seats. my_pmpro_apply_pending_seat_reduction() will
	// drop it to the new count at next renewal.
	$wpdb->update(
		$wpdb->prefix . 'pmprogroupacct_groups',
		array( 'group_total_seats' => intval( $pending['existing_seats'] ) ),
		array( 'id' => intval( $existing_group->id ) ),
		array( '%d' ),
		array( '%d' )
	);
}
add_action( 'pmpro_after_checkout', 'my_pmpro_defer_seat_reduction_after_checkout', 999, 2 );

/**
 * At renewal, apply any pending seat reduction for the renewed level.
 *
 * Fires when a subscription payment completes. If the user has a pending seat
 * reduction for the renewed level, group_total_seats is updated to the new (lower)
 * count and the pending meta is cleared.
 */
function my_pmpro_apply_pending_seat_reduction( $subscription ) {
	global $wpdb;

	if ( ! class_exists( 'PMProGroupAcct_Group' ) || ! is_a( $subscription, 'PMPro_Subscription' ) ) {
		return;
	}

	$user_id  = (int) $subscription->get_user_id();
	$level_id = (int) $subscription->get_membership_level_id();

	$pending = get_user_meta( $user_id, '_my_pmpro_pending_seat_reduction', true );
	if ( empty( $pending ) || (int) $pending['level_id'] !== $level_id ) {
		return;
	}

	$existing_group = PMProGroupAcct_Group::get_group_by_parent_user_id_and_parent_level_id( $user_id, $level_id );
	if ( empty( $existing_group ) ) {
		delete_user_meta( $user_id, '_my_pmpro_pending_seat_reduction' );
		return;
	}

	$wpdb->update(
		$wpdb->prefix . 'pmprogroupacct_groups',
		array( 'group_total_seats' => intval( $pending['new_seats'] ) ),
		array( 'id' => intval( $existing_group->id ) ),
		array( '%d' ),
		array( '%d' )
	);

	delete_user_meta( $user_id, '_my_pmpro_pending_seat_reduction' );
}
add_action( 'pmpro_subscription_payment_completed', 'my_pmpro_apply_pending_seat_reduction' );

