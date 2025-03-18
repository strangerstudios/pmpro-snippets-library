<?php
/**
 * Redirect members to a specific page when logging in for the first time.
 *
 * title: First Time Login Redirect For Paid Memberships Pro
 * layout: snippet
 * collection: misc
 * category: login redirect
 * link: https://www.paidmembershipspro.com/redirect-new-members-to-a-specific-page-the-first-time-they-log-in/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function first_time_login_redirect( $redirect_to, $request, $user ) {
	//check level
	if ( ! empty( $user ) && ! empty( $user->ID ) && function_exists( 'pmpro_getMembershipLevelForUser' ) ) {
		$first_login = get_user_meta( $user->ID, 'first_login', true );

		if ( $first_login == 'no' ) {
			return $redirect_to;
		}

		$level = pmpro_getMembershipLevelForUser( $user->ID );

		// Change case 'x': to level ID and $redirect_to URL to redirect the user on first login.
		switch ( $level->ID ) {
			case '1':
				update_user_meta( $user->ID, 'first_login', 'no' );
				$redirect_to = home_url();
				break;

			case '2':
				update_user_meta( $user->ID, 'first_login', 'no' );
				$redirect_to = home_url( '/page-slug-2' );
				break;

			case '3':
				update_user_meta( $user->ID, 'first_login', 'no' );
				$redirect_to = home_url( '/page-slug-3' );
				break;
		}
	}
	return $redirect_to;
}
add_filter( 'login_redirect', 'first_time_login_redirect', 15, 3 );
