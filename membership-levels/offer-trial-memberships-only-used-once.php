<?php
/**
 * Offer Trial Memberships That Can Only Be Used Once
 *
 * This will allow users to use the trial level once.
 *
 * Note: This does not affect pre-existing members that had a level before this code is implemented.
 *
 * Ensure line 26 is changed to the trial Level ID's that should only be allowed to be used once.
 *
 * title: Offer Trial Memberships That Can Only Be Used Once
 * layout: snippet
 * collection: membership-levels
 * category: trials
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

// record when users gain the trial level
function my_pmpro_after_change_membership_level( $level_id, $user_id ) {

	// set this to the id of your trial level
	$trial_levels = array( 1, 2 );

	if ( in_array( $level_id, $trial_levels ) ) {
		// add user meta to record the fact that this user has had this level before
		update_user_meta( $user_id, "pmpro_trial_level_used_{$level_id}", '1' );
	}
}
add_action( 'pmpro_after_change_membership_level', 'my_pmpro_after_change_membership_level', 10, 2 );

// check at checkout if the user has used the trial level already
function my_pmpro_registration_checks( $value ) {

	global $current_user;

	$level = pmpro_getLevelAtCheckout();
    
    $level_id = $level->id;

	if ( $current_user->ID ) {
		// check if the current user has already used the trial level
		$already = get_user_meta( $current_user->ID, "pmpro_trial_level_used_{$level_id}", true );

		// yup, don't let them checkout
		if ( $already ) {
			global $pmpro_msg, $pmpro_msgt;
			$pmpro_msg  = 'You have already used up your trial membership. Please select a full membership to checkout.';
			$pmpro_msgt = 'pmpro_error';

			$value = false;
		}
	}

	return $value;
}
add_filter( 'pmpro_registration_checks', 'my_pmpro_registration_checks' );

// swap the expiration text if the user has used the trial
function my_pmpro_level_expiration_text( $text, $level ) {
	global $current_user;

	// has user used trial level already.
	if ( $current_user->ID ) {
		$used_trial = get_user_meta( $current_user->ID, "pmpro_trial_level_used_{$level->id}", true );

		if ( ! empty( $used_trial ) ) {
			$text = 'You have already used up your trial membership. Please select a full membership to checkout.';
		}
	}

	return $text;
}
add_filter( 'pmpro_level_expiration_text', 'my_pmpro_level_expiration_text', 10, 2 );
