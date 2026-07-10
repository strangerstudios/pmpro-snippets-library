<?php
/**
 * This recipe adds validation for a URL User Field at Membership Checkout.
 * 
 * title: Add Validation For User URL Field At Checkout
 * layout: snippet
 * collection: checkout
 * category: user fields
 * link: https://www.paidmembershipspro.com/validate-urls-checkout/
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_check_valid_urls( $pmpro_continue_registration ) {
	if ( ! $pmpro_continue_registration ) {
		return $pmpro_continue_registration;
	}

	$url_fields = array(
		'user_url' => 'Website', // replace user_url with the name of your field and Website with your field label
	);

	$error_messages = array();

	foreach ( $url_fields as $field_name => $label ) {
		if ( isset( $_REQUEST[ $field_name ] ) ) {
			$url = sanitize_text_field( $_REQUEST[ $field_name ] );

			if ( ! empty( $url ) && ! preg_match( '/^https?:\/\//i', $url ) ) {
				$pmpro_continue_registration = false;
				$error_messages[] = 'Please enter a full URL for "' . esc_html( $label ) . '" starting with http:// or https://'; // Set your preferred starting sentence for the error displayed
			}
		}
	}

	// Show all error messages
	if ( ! empty( $error_messages ) ) {
		pmpro_setMessage( implode( '<br>', $error_messages ), 'pmpro_error' );
	}

	return $pmpro_continue_registration;
}
add_filter( 'pmpro_registration_checks', 'my_pmpro_check_valid_urls' );
