<?php
/*
 * Show a member number on the membership card for Paid Memberships Pro.
 *
 * This is an extension of the Membership Number snippet here:
 * https://github.com/strangerstudios/pmpro-snippets-library/blob/1b7724c13775d45574fd0f9772b41100193f7f91/frontend-pages/generate-unique-membership-number.php
 *
 * title: Show a member number on the membership card for Paid Memberships Pro.
 * layout: snippet
 * collection: add-ons, pmpro-membership-card
 * category: membership-number
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_show_member_number_on_card( $user ) {

	if ( ! pmpro_hasMembershipLevel() || ! function_exists( 'generate_member_number' )) {
		return;
	}

	$member_id = get_user_meta( $user->ID, 'member_number', true );

	if ( empty( $member_id ) ) {
		// lets generate it on the fly.
		$member_id = generate_member_number( $user->ID );
	}

	echo '<p><strong>Membership Number:</strong> ' . esc_html( $member_id ) . '</p>';

}
add_action( 'pmpro_membership_card_after_card', 'my_show_member_number_on_card' );