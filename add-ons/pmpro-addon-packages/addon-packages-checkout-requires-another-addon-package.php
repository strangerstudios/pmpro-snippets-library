<?php
/**
 * Require an Addon Package to be purchased already to purchase another Addon Package.
 *
 * title: Addon Packages Checkout Requires Another Addon Package
 * layout: snippet
 * collection: addons
 * category: pmpro-addon-packages
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_require_ap_for_ap_checkout( $continue ) {
	global $current_user, $pmpro_msg, $pmpro_msgt;

	// Set the Addon Packages that require other Addon Packages to be purchased first.
	$ap_required = array(
		'129' => '127', // Addon Package post ID 129 requires Addon Package post ID 127 to be purchased first.
	);

	// Let's  only do this if PMPro Addon Packages is active and checkout is for an Addon Package.
	if ( ! function_exists( 'pmproap_hasAccess' ) || ! isset( $_REQUEST['ap'] ) ) {
		return $continue;
	}

	// If things aren't okay, just bail.
	if ( ! $continue ) {
		return $continue;
	}

	$checkout_ap = $_REQUEST['ap'];

	// if user does not have access to the Addon Package they are trying to purchase, .
	if ( ! pmproap_hasAccess( $current_user->ID, $ap_required[ $checkout_ap ] ) ) {
		// get post permalink for checkout ap
		$required_ap_permalink = get_permalink( $ap_required[ $checkout_ap ] );

		// set error message with link to checkout ap with pmpro_setMessage
		$pmpro_msg  = sprintf( __( 'To be able to purchase access to this post you first need to purchase access to <a href="%s">this</a>.', 'paid-memberships-pro' ), $required_ap_permalink );
		$pmpro_msgt = 'pmpro_error';
		return false;
	}

	return $continue;
}
add_filter( 'pmpro_registration_checks', 'my_pmpro_require_ap_for_ap_checkout', 10, 1 );
