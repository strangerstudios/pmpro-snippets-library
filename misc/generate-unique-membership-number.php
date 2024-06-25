<?php
/**
 * Generate a Unique Member Number for each user.
 * 
 * Change the generate_member_number function if your member number needs to be in a certain format.
 * 
 * Member numbers are generated when users are registered or when the membership account page is 
 * accessed for the first time. This code will only generate one member number per user (not per membership).
 * 
 * title: Generate a Unique Member Number for Each User
 * layout: snippet
 * collection: misc
 * category: membership-number
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function generate_member_number( $user_id ) {
	// Get member number.
	$member_number = get_user_meta( $user_id, 'member_number', true );

	// If no member number, create one.
	if ( empty( $member_number ) ) {
		global $wpdb;

		// This code generates a string that's 10 characters long of numbers and letters.
		while ( empty( $member_number ) ) {

			$scramble = md5( AUTH_KEY . current_time('timestamp') . $user_id . SECURE_AUTH_KEY) ;
			$member_number = substr( $scramble, 0, 10 );
			
			$check = $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE meta_value = '" . esc_sql( $member_number ) . "' LIMIT 1" );

			if ( $check || is_numeric( $member_number ) ) {
				$member_number = NULL;
			}
		}

		// Save to user meta.
		update_user_meta( $user_id, 'member_number', $member_number );
		
		return $member_number;
	}
}
add_action( 'user_register', 'generate_member_number' );

// Generate member number on the Membership Account page if the user doesn't have one.
function my_pmpro_generate_member_number_if_empty() {
	global $pmpro_pages;

	// Only run on the account page.
	if ( ! is_page( $pmpro_pages['account'] ) ) {
		return;
	}

	// Only run for logged in users.
	if ( ! is_user_logged_in() ) {
		return;
	}

	// If the generate number function doesn't exist, return.
	if ( ! function_exists( 'generate_member_number' ) ) {
		return;
	}

	// Get member number.
	global $current_user;	
	$member_number = get_user_meta( $current_user->ID, 'member_number', true );

	// If no number, generate one.
	if ( empty( $member_number ) ) {
		$member_number = generate_member_number( $current_user->ID );
	}
}
add_action( 'template_redirect', 'my_pmpro_generate_member_number_if_empty' );
