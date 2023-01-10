<?php
/**
 * Add a column to show the active membership level for the affiliate on the
 * Memberships > Affiliates list in the WordPress admin.
 *
 * title: Show Affiliate's Membership Level on Admin Page
 * layout: snippet
 * collection: add-ons
 * category: pmpro-affiliates
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Add table header to the Affiliates admin page for "Level"
 */
function my_pmpro_affiliate_extra_cols_header_level() {
	echo '<th>Level</th>';
}
add_action( 'pmpro_affiliate_extra_cols_header', 'my_pmpro_affiliate_extra_cols_header_level' );

/**
 * Add membership level row data for each affiliate.
 */
function my_pmpro_affiliate_extra_cols_body_level( $affiliate, $earnings) {
	// Get the user for this affiliate.
	$user = get_user_by( 'login', $affiliate->affiliateuser );

	// Return a blank/missing character if no user.
	if ( empty( $user ) ) {
		echo '<td>&#8212;</td>';
		return;
	}

	// Show the level name or return a blank/missing character if no membership.
	$membership_level = pmpro_getMembershipLevelForUser($user->ID);
	echo '<td>' . ( $membership_level->name ?: '&#8212;' ) . '</td>';

}
add_action( 'pmpro_affiliate_extra_cols_body', 'my_pmpro_affiliate_extra_cols_body_level', 10, 2 );
