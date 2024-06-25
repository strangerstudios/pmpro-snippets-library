<?php
/*
 * Returns membership profile URL when scanning the QR Code on member's Membership Card.
 * 
 * Learn more at https://www.paidmembershipspro.com/membership-qr-code/
 *
 * title: Returns membership profile URL when scanning the QR Code on member's Membership Card.
 * layout: snippet
 * collection: add-ons, pmpro-membership-card
 * category: qr-code
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function mypmro_membership_card_qr_data_profile_url( $pmpro_membership_card_user, $option ){
	//Checking if PMPro is active
	if( ! function_exists( 'pmpro_hasMembershipLevel' ) || ! function_exists( 'pmpromd_build_profile_url' ) ){
		return;
	}

	if( $option == 'other' ){
		if( ! empty( $pmpro_membership_card_user->user_nicename ) ){
			return pmpromd_build_profile_url( $pmpro_membership_card_user );
		}
	}	

}
add_filter( 'pmpro_membership_card_qr_data_other', 'mypmro_membership_card_qr_data_profile_url', 10, 2 );