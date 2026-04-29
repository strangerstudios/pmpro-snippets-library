<?php
/**
 * Customize QR Code Data Based on Membership Status
 *
 * This recipe adds dynamic status data ("active" or "inactive") to the QR code 
 * when using the PMPro Membership Card shortcode with the attribute `qr_data='other'`.
 *
 * title: Customize QR Code Data Based on Membership Status
 * layout: snippet
 * collection: add-ons, pmpro-membership-card
 * category: qr-code
 * link: https://www.paidmembershipspro.com/customize-membership-card-wordpress/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function mypmro_membership_card_show_active ( $pmpro_membership_card_user, $option ) {

	//Checking if PMPro is active
	if ( ! function_exists( 'pmpro_hasMembershipLevel' ) ) {
		return;
	}

	if ( $option == 'other' ) {
		//Change according to the levels you'd like to check for
		$has_level = pmpro_hasMembershipLevel( array( 1, 2, 3 ) );

		if ( $has_level ) {
			$status = 'active';
		} else {
			$status = 'inactive';
		}

		return $status;
	}

	return $pmpro_membership_card_user; // Return the original data if not 'other'

}
add_filter( 'pmpro_membership_card_qr_data_other', 'mypmro_membership_card_show_active', 10, 2 );