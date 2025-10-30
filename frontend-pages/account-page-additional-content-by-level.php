<?php
/**
 * Display additional custom field content on the Membership Account page cards for specific membership levels.
 *
 * title: Show Additional Content on Membership Account Cards by Level
 * layout: snippet-example
 * collection: frontend-pages
 * category: account-page
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_display_additional_content( $level ) {

	// Only show this section for members with one of these level IDs.
	// Replace 6, 7, 8 with your actual membership level IDs.
	if ( in_array( $level->ID, array( 6, 7, 8 ) ) ) {

		// Output custom member data using PMPro shortcode fields.
		echo '<p><strong>Coaching Hours Remaining:</strong> ' . do_shortcode( '[pmpro_member field="coaching_hours_remaining"]' ) . '</p>';
		echo '<p><strong>Coaching Notes:</strong><br />' . do_shortcode( '[pmpro_member field="coaching_notes"]' ) . '</p>';
	}
}
add_action( 'pmpro_membership_account_after_level_card_content', 'my_pmpro_display_additional_content', 15 );
