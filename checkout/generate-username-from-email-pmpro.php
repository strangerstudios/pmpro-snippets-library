<?php
/**
* Generate a username at PMPro checkout from email for users.
* includes hiding 'username' field using custom CSS line 67 - 79
*
* title: Generate a username at PMPro checkout from email for users.
* layout: snippet
* collection: username
* category: checkout
* link: https://www.paidmembershipspro.com/customizing-usernames-for-your-membership-site/
*
* You can add this recipe to your site by creating a custom plugin
* or using the Code Snippets plugin available for free in the WordPress repository.
* Read this companion article for step-by-step directions on either method.
* https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
*/


function my_pmpro_generate_username_at_checkout() {

	// Make sure PMPro is installed.
	if ( ! function_exists( 'pmpro_is_checkout' ) || ! function_exists( 'pmpro_getLevelAtCheckout' ) ) {
		return;
	}

	// Bail early if not on checkout page.
	// Note: pmpro_getLevelAtCheckout() triggers discount code filters which can
	// start PHP sessions (e.g. via the Local Pricing Add On), so we guard with
	// pmpro_is_checkout() first to avoid starting sessions on every page load.
	if ( ! pmpro_is_checkout() ) {
		return;
	}

	// Check for level to confirm checkout context.
	if ( empty( pmpro_getLevelAtCheckout() ) ) {
		return;
	}

	if ( ! empty( $_POST['bemail'] ) ) {
		$_REQUEST['username'] = $_POST['username'] = my_pmpro_generate_username_from_email( $_POST['bemail'] );
	}

	if ( ! empty( $_GET['bemail'] ) ) {
		$_REQUEST['username'] = $_GET['username'] = my_pmpro_generate_username_from_email( $_GET['bemail'] );
	}

}
add_action( 'wp', 'my_pmpro_generate_username_at_checkout', 1 );

/**
 * Hide the username field on checkout.
 */
function my_pmpro_unset_required_username_checkout( $pmpro_required_user_fields ) {
	unset( $pmpro_required_user_fields['username'] );
	return $pmpro_required_user_fields;
}
add_filter( 'pmpro_required_user_fields', 'my_pmpro_unset_required_username_checkout', 10, 2 );


/**
 * Generate a username from a user's email address.
 * Helper function.
 */
function my_pmpro_generate_username_from_email( $email ) {

	$parts = explode( '@', $email );

	while ( username_exists( $parts[0] ) ) {
		$parts[0] .= random_int( 0, 9999 );
	}

	return sanitize_text_field( $parts[0] );
}

/**
 * Loads styling in the footer to hide the username field
 */
add_action( 'wp_footer', function () {
    ?>
    <style>
        /* Hide the username field on checkout. */
        .pmpro_form_field-username {
            display: none !important;
        }
    </style>
    <?php
} );