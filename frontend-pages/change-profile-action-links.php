<?php
/**
 * Change profile action links on the account page.
 *
 * title: Change profile action links on the account page.
 * layout: snippet
 * collection: frontend-pages
 * category: profile, account
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function my_pmpro_account_profile_action_links( $pmpro_profile_action_links ) {

	// set your custom links here
	$my_edit_profile_url    = home_url( 'my-edit-profile-page-slug' );

	/* Uncomment the relative line by removing the comment ("//" double forward slash before the variable) if you would like to change the url for password reset or logout. */

	// $my_change_password_url = home_url( 'my-change-password-page-slug' );
	// $my_logout_url          = home_url( 'my-logout-page-slug' );

	if ( ! empty( $my_edit_profile_url ) ) {
		$pmpro_profile_action_links['edit-profile'] = sprintf( '<a id="pmpro_actionlink-profile" href="%s">%s</a>', esc_url( $my_edit_profile_url ), esc_html__( 'Edit Profile', 'paid-memberships-pro' ) );
	}

	if ( ! empty( $my_change_password_url ) ) {
		$pmpro_profile_action_links['change-password'] = sprintf( '<a id="pmpro_actionlink-change-password" href="%s">%s</a>', esc_url( $my_change_password_url ), esc_html__( 'Change Password', 'paid-memberships-pro' ) );
	}

	if ( ! empty( $my_logout_url ) ) {
		$pmpro_profile_action_links['logout'] = sprintf( '<a id="pmpro_actionlink-logout" href="%s">%s</a>', esc_url( $my_logout_url ), esc_html__( 'Log Out', 'paid-memberships-pro' ) );
	}

	return $pmpro_profile_action_links;

}
add_filter( 'pmpro_account_profile_action_links', 'my_pmpro_account_profile_action_links' );