<?php
/**
 * Only allow a member to claim a trial level's First Recurring Payment delay once.
 *
 * Records when a user gets the trial level. If that user checks out for the
 * trial level again, the delay is removed: they pay the recurring amount at
 * checkout and the subscription renews one billing cycle later.
 *
 * Requires Paid Memberships Pro 3.9 or later. Set the trial level's First
 * Recurring Payment to "After a number of days" under Memberships > Settings > Levels.
 *
 * The trial is not skipped for checkouts that use a discount code, or when the
 * trial level has a Billing Cycle Limit or a Custom Trial.
 *
 * title: One time credit card required trial
 * layout: snippet
 * collection: add-ons, pmpro-subscription-delays
 * category: subscriptions, trials
 * link: https://www.paidmembershipspro.com/subscription-delay-one-time-credit-card-required-trial/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

// Set this to the ID of your trial level.
function my_pmpro_one_time_trial_level_id() {
	return 1; // Membership Level ID
}

// Check whether a user has already claimed the trial.
function my_pmpro_one_time_trial_claimed( $user_id ) {
	if ( empty( $user_id ) ) {
		return false;
	}

	return (int) get_user_meta( $user_id, 'pmpro_trial_level_used', true ) === my_pmpro_one_time_trial_level_id();
}

// Record when users gain the trial level.
function my_pmpro_one_time_trial_save_trial_level_used( $level_id, $user_id ) {
	$trial_level_id = my_pmpro_one_time_trial_level_id();

	if ( (int) $level_id === $trial_level_id ) {
		update_user_meta( $user_id, 'pmpro_trial_level_used', $trial_level_id );
	}
}
add_action( 'pmpro_after_change_membership_level', 'my_pmpro_one_time_trial_save_trial_level_used', 10, 2 );

// Show the user's trial status to admins on the Edit Profile page.
function my_pmpro_one_time_trial_show_trial_level_used( $user ) {
	if ( ! current_user_can( 'edit_users' ) ) {
		return;
	}
	?>
	<h3><?php esc_html_e( 'One-Time Trial', 'pmpro-snippets-library' ); ?></h3>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"></th>
				<td>
					<?php
					if ( my_pmpro_one_time_trial_claimed( $user->ID ) ) {
						esc_html_e( 'Trial period has been claimed.', 'pmpro-snippets-library' );
					} else {
						esc_html_e( 'Trial period not claimed.', 'pmpro-snippets-library' );
					}
					?>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}
add_action( 'show_user_profile', 'my_pmpro_one_time_trial_show_trial_level_used' );
add_action( 'edit_user_profile', 'my_pmpro_one_time_trial_show_trial_level_used' );

// Check whether this checkout should skip the trial.
function my_pmpro_one_time_trial_skip_trial( $level ) {
	// Not logged in, or not the trial level?
	if ( empty( $level->id ) || (int) $level->id !== my_pmpro_one_time_trial_level_id() || ! my_pmpro_one_time_trial_claimed( get_current_user_id() ) ) {
		return false;
	}

	// Let discount codes set their own pricing.
	if ( ! empty( $level->code_id ) ) {
		return false;
	}

	// A full initial payment would add a paid cycle to a billing limit or a custom trial.
	if ( ! empty( $level->billing_limit ) || ! empty( $level->trial_limit ) ) {
		return false;
	}

	return true;
}

// Charge the recurring amount at checkout and remove the First Recurring Payment delay.
function my_pmpro_one_time_trial_checkout_level( $level ) {
	if ( empty( $level ) || ! my_pmpro_one_time_trial_skip_trial( $level ) ) {
		return $level;
	}

	$level->initial_payment = $level->billing_amount;

	// PMPro sets this from the level's First Recurring Payment setting. Without it, billing starts one cycle after checkout.
	unset( $level->profile_start_date );

	// Read when the checkout completes, including offsite checkouts.
	$level->one_time_trial_skipped = 1;

	return $level;
}
add_filter( 'pmpro_checkout_level', 'my_pmpro_one_time_trial_checkout_level', 5 );

// Show the regular price on the levels page and at checkout.
function my_pmpro_one_time_trial_level_cost_text( $cost, $level, $tags = true, $short = false ) {
	// Leave existing memberships on the Membership Account page alone.
	if ( property_exists( $level, 'startdate' ) || ! my_pmpro_one_time_trial_skip_trial( $level ) ) {
		return $cost;
	}

	$regular_level                  = clone $level;
	$regular_level->initial_payment = $regular_level->billing_amount;
	unset( $regular_level->profile_start_date );

	// Rebuild the text with PMPro's own wording, minus the First Recurring Payment sentence.
	$delay_option = 'pre_option_pmpro_subscription_delay_' . (int) $level->id;
	remove_filter( 'pmpro_level_cost_text', 'my_pmpro_one_time_trial_level_cost_text', 15 );
	add_filter( $delay_option, '__return_empty_string' );

	$cost = pmpro_getLevelCost( $regular_level, $tags, $short );

	remove_filter( $delay_option, '__return_empty_string' );
	add_filter( 'pmpro_level_cost_text', 'my_pmpro_one_time_trial_level_cost_text', 15, 4 );

	return $cost;
}
add_filter( 'pmpro_level_cost_text', 'my_pmpro_one_time_trial_level_cost_text', 15, 4 );

// Flag the order when a returning member skipped the trial. Runs before the trial meta is saved.
function my_pmpro_one_time_trial_flag_order( $user_id, $order ) {
	global $pmpro_level;

	if ( ! empty( $pmpro_level->one_time_trial_skipped ) && ! empty( $order->id ) ) {
		update_pmpro_membership_order_meta( $order->id, 'one_time_trial_skipped', 1 );
	}
}
add_action( 'pmpro_checkout_before_change_membership_level', 'my_pmpro_one_time_trial_flag_order', 10, 2 );

// Remove the trial's first payment date from the checkout emails for flagged orders.
function my_pmpro_one_time_trial_email_data( $data, $email ) {
	$templates = array( 'checkout_paid', 'checkout_paid_admin', 'checkout_check', 'checkout_check_admin' );
	if ( empty( $email->template ) || ! in_array( $email->template, $templates, true ) || empty( $data['order_id'] ) ) {
		return $data;
	}

	$order = new MemberOrder();
	$order->getMemberOrderByCode( $data['order_id'] );
	if ( empty( $order->id ) || ! get_pmpro_membership_order_meta( $order->id, 'one_time_trial_skipped', true ) ) {
		return $data;
	}

	$level = pmpro_getSpecificMembershipLevelForUser( $order->user_id, $order->membership_id );
	if ( empty( $level ) ) {
		return $data;
	}

	// Without profile_start_date, PMPro leaves the first payment date out of an existing membership's cost text.
	$level = clone $level;
	unset( $level->profile_start_date );
	$data['membership_cost'] = pmpro_getLevelCost( $level );

	return $data;
}
add_filter( 'pmpro_email_data', 'my_pmpro_one_time_trial_email_data', 10, 2 );
