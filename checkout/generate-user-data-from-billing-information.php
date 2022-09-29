<?php
/**
 * Generate user data from billing information fields at membership checkout.
 *
 * title: Generate user data from billing information fields at membership checkout.
 * layout: snippet
 * collection: checkout
 * category: user fields
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
/**
 * Hide the Account Information section
 */
function simple_checkout_hide_account_information_section( $skip_account_fields, $current_user ) {
	if ( empty( $current_user->ID ) ) {
		$skip_account_fields = 1;
	}
	return $skip_account_fields;
}
add_filter( 'pmpro_skip_account_fields', 'simple_checkout_hide_account_information_section', 10, 2 );

/**
 * Don't require some Account Information fields
 */
function simple_checkout_unset_required_user_fields( $pmpro_required_user_fields ) {
	unset( $pmpro_required_user_fields['username'] );
	unset( $pmpro_required_user_fields['password'] );
	unset( $pmpro_required_user_fields['password2'] );
	unset( $pmpro_required_user_fields['bconfirmemail'] );

	return $pmpro_required_user_fields;
}
add_filter( 'pmpro_required_user_fields', 'simple_checkout_unset_required_user_fields', 10, 2 );

/**
 * Don't include the "confirm email" field in Billing Information
 */
add_filter( 'pmpro_checkout_confirm_email', '__return_false' );
