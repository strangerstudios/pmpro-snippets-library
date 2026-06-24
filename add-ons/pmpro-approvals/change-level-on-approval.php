<?php
/**
 * If a user is approved for a certain level, change them to a different level.
 *
 * title: Change Membership Level on Approval
 * layout: snippet
 * collection: add-ons
 * category: pmpro-approvals
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_change_level_if_approved( $user_id, $level_id ) {
	$approval_level_id = 1; // Replace with your Approval Level ID.
	$new_level_id      = 2; // Level to change to after approval.

	// Only run if the approved level is the targeted Approval level.
	if ( (int) $level_id === $approval_level_id ) {
		$current_level = pmpro_getSpecificMembershipLevelForUser( $user_id, $approval_level_id );

		if ( ! empty( $current_level ) ) {
			$custom_level = array(
				'user_id'         => $user_id,
				'membership_id'   => $new_level_id,
				'code_id'         => $current_level->code_id,
				'initial_payment' => $current_level->initial_payment,
				'billing_amount'  => $current_level->billing_amount,
				'cycle_number'    => $current_level->cycle_number,
				'cycle_period'    => $current_level->cycle_period,
				'billing_limit'   => $current_level->billing_limit,
				'trial_amount'    => $current_level->trial_amount,
				'trial_limit'     => $current_level->trial_limit,
				'startdate'       => ! empty( $current_level->startdate ) ? date( 'Y-m-d H:i:s', $current_level->startdate ) : current_time( 'mysql' ),
				'enddate'         => ! empty( $current_level->enddate ) ? date( 'Y-m-d H:i:s', $current_level->enddate ) : '0000-00-00 00:00:00',
			);

			// Don't cancel the approval level subscription, if one exists, when changing the level.
			add_filter( 'pmpro_cancel_previous_subscriptions', '__return_false' );
			$changed = pmpro_changeMembershipLevel( $custom_level, $user_id );
			remove_filter( 'pmpro_cancel_previous_subscriptions', '__return_false' );

			if ( ! $changed ) {
				return;
			}

			// Check for an active subscription for the previous level.
			$previous_subscription = PMPro_Subscription::get_subscription(
				array(
					'user_id'             => $user_id,
					'membership_level_id' => $approval_level_id,
					'status'              => 'active',
				)
			);

			// If found, point the subscription from the previous level to the new level.
			if ( ! empty( $previous_subscription ) ) {
				$previous_subscription->set( 'membership_level_id', $new_level_id );
				if ( ! $previous_subscription->save() ) {
					error_log( sprintf( 'PMPro: Failed to update subscription %d to level %d for user %d.', $previous_subscription->get( 'id' ), $new_level_id, $user_id ) );
				}
			}
		}
	}
}
add_action( 'pmpro_approvals_after_approve_member', 'my_pmpro_change_level_if_approved', 10, 2 );
