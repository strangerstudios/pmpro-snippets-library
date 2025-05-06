<?php
/**
 * Append Avatar to Bottom of Membership Card
 *
 * This recipe appends a member's avatar image to the bottom of the Membership Card.
 *
 * title: Append Avatar to Bottom of Membership Card
 * layout: snippet
 * collection: add-ons, pmpro-membership-card
 * category: profile-display
 * link: https://www.paidmembershipspro.com/customize-membership-card-wordpress/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_add_avatar_after_member_card( $pmpro_membership_card_user, $print_sizes, $qr_code, $qr_data ){

	echo "<p>".get_avatar( $pmpro_membership_card_user->user_email, 96 )."</p>"; //Wrapping content in <p> tags will help with consistent spacing

}
add_action( 'pmpro_membership_card_after_card', 'my_pmpro_add_avatar_after_member_card', 10, 4 );