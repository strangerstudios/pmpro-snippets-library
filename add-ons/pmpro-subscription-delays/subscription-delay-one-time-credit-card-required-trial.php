<?php
/**
 * This code stores data when a user checks out for a level.
 * If that user tries to checkout for the same level, the Subscription Delay is removed.
 * The user is instead charged for their first subscription payment at checkout.
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

// Record when users gain the trial level.
function my_pmpro_one_time_trial_save_trial_level_used( $level_id, $user_id ) {
	// Set this to the ID of your trial level.
	$trial_level_id = 1; // Membership Level ID

	if ( $level_id == $trial_level_id ) {
		// Add user meta to record that the user has received their one-time trial.
		update_user_meta( $user_id, 'pmpro_trial_level_used', $trial_level_id );
	}
}
add_action( 'pmpro_after_change_membership_level', 'my_pmpro_one_time_trial_save_trial_level_used', 10, 2 );

// Show the user's trial meta setting for admins on the Edit Profile page.
function my_pmpro_one_time_trial_show_trial_level_used( $user ) {
	if ( current_user_can( 'edit_users' ) ) { ?>
		<h3><?php esc_html_e( 'One-Time Trial', 'pmpro-snippets-library' ); ?></h3>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"></th>
					<td>
						<?php
							$already = get_user_meta( $user->ID, 'pmpro_trial_level_used', true );
						if ( ! empty( $already ) && $already == '1' ) {
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
}
add_action( 'show_user_profile', 'my_pmpro_one_time_trial_show_trial_level_used' );
add_action( 'edit_user_profile', 'my_pmpro_one_time_trial_show_trial_level_used' );

// Check if the user has received their one-time trial at checkout.
function my_pmpro_one_time_trial_remove_delay_hooks() {
	global $current_user;

	//set this to the id of your trial level
	$trial_level_id = 1; // Membership Level ID
	$level_at_checkout = pmpro_getLevelAtCheckout();
	//Bail if no level is selected.
	if ( empty( $level_at_checkout ) ) {
		return;
	}
	$checkout_level_id = $level_at_checkout->id;

	if ( ! empty( $current_user->ID ) && ! empty( $checkout_level_id ) && $checkout_level_id == $trial_level_id ) {
		// Check the current user's meta.
		$already = get_user_meta( $current_user->ID, 'pmpro_trial_level_used', true );

		// Remove the subscription delay from checkout. Charge the subscripton immediately.
		if ( $already ) {
			//for backwards compatibility with PMPro < 3.4
			remove_filter( 'pmpro_profile_start_date', 'pmprosd_pmpro_profile_start_date', 10, 2 );
			remove_filter( 'pmpro_checkout_level', 'pmprosd_pmpro_checkout_level' ); //for PMPro 3.4+
			remove_action( 'pmpro_after_checkout', 'pmprosd_pmpro_after_checkout' );
			remove_filter( 'pmpro_next_payment', 'pmprosd_pmpro_next_payment', 10, 3 );
			remove_filter( 'pmpro_level_cost_text', 'pmprosd_level_cost_text', 10, 2 );
			remove_action( 'pmpro_save_discount_code_level', 'pmprosd_pmpro_save_discount_code_level', 10, 2 );
		}
	}
}
add_action( 'init', 'my_pmpro_one_time_trial_remove_delay_hooks' );

// Filter the price on the levels page to remove one-time trial.
function my_pmpro_one_time_trial_delay_pmpro_level_cost_text( $cost, $level ) {
	global $current_user, $pmpro_pages;

	// Not logged in?
	if ( empty( $current_user->ID ) ) {
		return $cost;
	}

	// Check the current user's meta.
	$already = get_user_meta( $current_user->ID, 'pmpro_trial_level_used', true );

	// If the user already had the trial for this level, make initial payment = billing amount.
	if ( $level->id == $already && is_page( $pmpro_pages['levels'] ) ) {
		$cost = sprintf( __( '<strong>%1$s per %2$s</strong>.', 'pmpro-snippets-library' ),
		pmpro_formatPrice( $level->billing_amount ), pmpro_translate_billing_period( $level->cycle_period ) );
	}

	return $cost;
}
add_filter( 'pmpro_level_cost_text', 'my_pmpro_one_time_trial_delay_pmpro_level_cost_text', 15, 2 );

// Filter the price at checkout to charge the billing ammount immediately.
function my_pmpro_one_time_trial_delay_pmpro_checkout_level( $level ) {
	global $current_user, $discount_code, $wpdb;

	// Not logged in?
	if ( empty( $current_user->ID ) ) {
		return $level;
	}

	// Not if using a discount code.
	if ( ! empty( $discount_code ) || ! empty( $_REQUEST['discount_code'] ) ) {
		return $level;
	}

	// Check the current user's meta.
	$already = get_user_meta( $current_user->ID, 'pmpro_trial_level_used', true );

	// If the user already had the trial for this level, make initial payment = billing amount.
	if ( $level->id == $already ) {
		$level->initial_payment = $level->billing_amount;
	}

	return $level;
}
add_filter( 'pmpro_checkout_level', 'my_pmpro_one_time_trial_delay_pmpro_checkout_level', 5 );
