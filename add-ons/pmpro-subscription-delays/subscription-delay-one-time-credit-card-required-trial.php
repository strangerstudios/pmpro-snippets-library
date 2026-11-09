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

	return $level;
}
add_filter( 'pmpro_checkout_level', 'my_pmpro_one_time_trial_checkout_level', 5 );

// Show the regular price on the levels page and at checkout.
function my_pmpro_one_time_trial_level_cost_text( $cost, $level ) {
	// Leave existing memberships on the Membership Account page alone.
	if ( property_exists( $level, 'startdate' ) || ! my_pmpro_one_time_trial_skip_trial( $level ) ) {
		return $cost;
	}

	if ( 1 === (int) $level->cycle_number ) {
		/* translators: 1: billing amount, 2: billing period */
		$cost = sprintf( __( 'The price for membership is <strong>%1$s per %2$s</strong>.', 'pmpro-snippets-library' ), pmpro_formatPrice( $level->billing_amount ), pmpro_translate_billing_period( $level->cycle_period ) );
	} else {
		/* translators: 1: billing amount, 2: cycle number, 3: billing period */
		$cost = sprintf( __( 'The price for membership is <strong>%1$s every %2$d %3$s</strong>.', 'pmpro-snippets-library' ), pmpro_formatPrice( $level->billing_amount ), $level->cycle_number, pmpro_translate_billing_period( $level->cycle_period, $level->cycle_number ) );
	}

	return $cost;
}
add_filter( 'pmpro_level_cost_text', 'my_pmpro_one_time_trial_level_cost_text', 15, 2 );
