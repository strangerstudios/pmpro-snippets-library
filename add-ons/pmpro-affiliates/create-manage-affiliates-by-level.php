<?php
/**
 * Creates an affiliate for new members of Level ID 1.
 * Updates affiliate status if the affiliate already exists.
 * Sets affilaite status to 'inactive' if membership of Level ID 1 is cancelled (includes expiration).
 *
 * title: Create and Manage Affiliates by Membership Level Using AffiliateWP
 * layout: snippet
 * collection: add-ons
 * category: pmpro-affiliates
 * url: https://www.paidmembershipspro.com/create-manage-affiliates-membership-level-using-affiliatewp/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_affiliatewp_after_change_membership_level( $level_id, $user_id, $cancel_level ) {
	// make sure affiliatewp is active
	if ( ! function_exists( 'affwp_is_affiliate' ) ) {
		return;
	}

	if ( $level_id == 1 ) {
		// New member of level 1. Set up the Affiliate
		if ( affwp_is_affiliate( $user_id ) ) {
			$affiliate_id = affwp_get_affiliate_id( $user_id );
			// Affiliate already exists. Update Affiliate status to 'active'.
			affwp_update_affiliate(
				array(
					'affiliate_id' => $affiliate_id,
					'status'       => 'active',
				)
			);
		} else {
			// Affiliate does not exist. Create the Affiliate.
			affwp_add_affiliate(
				array(
					'user_id' => $user_id,
					'status'  => 'active',
				)
			);
		}
	}
	if ( $cancel_level == 1 ) {
		// User is cancelling or membership is expired.
		if ( affwp_is_affiliate( $user_id ) ) {
			$affiliate_id = affwp_get_affiliate_id( $user_id );
			// Affiliate exists. Update Affiliate status to 'inactive'.
			affwp_update_affiliate(
				array(
					'affiliate_id' => $affiliate_id,
					'status'       => 'inactive',
				)
			);
		}
	}
}
add_action( 'pmpro_after_change_membership_level', 'my_pmpro_affiliatewp_after_change_membership_level', 10, 3 );
