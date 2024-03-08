<?php
/**
 * Show the user's Membership Number on the Membership Account page.
 * 
 * Member numbers must be generated using the generate_member_number snippet in this companion code: 
 * https://github.com/strangerstudios/pmpro-snippets-library/tree/dev/misc/generate-unique-membership-number.php
 *
 * title: Display the Member's Unique Membership Number on the Membership Account
 * layout: snippet
 * collection: frontend-pages
 * category: membership-number
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_member_number_account_bullets() {

	// If the generate number function doesn't exist, return.
	if ( ! function_exists( 'generate_member_number' ) ) {
		return;
	}

	// Only run for logged in users.
	if ( ! is_user_logged_in() ) {
		return;
	}

	// Get member number.
	global $current_user;
	$member_number = get_user_meta( $current_user->ID, 'member_number', true );

	// If no member number, create one.
	if ( empty( $member_number ) )
		$member_number = generate_member_number( $current_user->ID );
	
	// Show the member number.
	if ( ! empty( $member_number ) ) { ?>
		<li><strong><?php esc_html_e( 'Member Number', 'paid-memberships-pro' ); ?>:</strong> <?php echo $member_number; ?></li>
	<?php
	}
}
add_action( 'pmpro_account_bullets_bottom', 'my_pmpro_member_number_account_bullets' );
