<?php
/**
 * Only require name and email address at membership checkout.
 *
 * title: Only require name and email address at membership checkout.
 * layout: snippet
 * collection: checkout
 * category: user fields
 * link: https://www.paidmembershipspro.com/reduce-form-fields-at-checkout/
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
 * Don't load any of the Billing Information fields
 */
function simple_checkout_remove_billing_address_fields( $include ) {
	return false;
}
add_filter( 'pmpro_include_billing_address_fields', 'simple_checkout_remove_billing_address_fields' );


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
 * Don't require the billing fields either.
 */
function simple_checkout_unset_required_billing_fields( $pmpro_required_billing_fields ) {
	unset( $pmpro_required_billing_fields['bfirstname'] );
	unset( $pmpro_required_billing_fields['blastname'] );
	unset( $pmpro_required_billing_fields['baddress1'] );
	unset( $pmpro_required_billing_fields['bcity'] );
	unset( $pmpro_required_billing_fields['bstate'] );
	unset( $pmpro_required_billing_fields['bzipcode'] );
	unset( $pmpro_required_billing_fields['bcountry'] );
	unset( $pmpro_required_billing_fields['bphone'] );
	
	return $pmpro_required_billing_fields;
}
add_filter( 'pmpro_required_billing_fields', 'simple_checkout_unset_required_billing_fields' );

/**
 * Auto-generate a username from the email address at checkout, but only on the PMPro checkout page.
 */
function simple_checkout_generate_username( $username ) {
    // Only run if we're on the PMPro checkout page
    if ( function_exists( 'pmpro_is_checkout' ) && pmpro_is_checkout() ) {
        if ( empty( $username ) && isset( $_REQUEST['bemail'] ) ) {
            $username = sanitize_user( current( explode( '@', $_REQUEST['bemail'] ) ) );
        }
    }
    return $username;
}
add_filter( 'pre_user_login', 'simple_checkout_generate_username' );


/**
 * Add the required User Fields
 */
function simple_checkout_name_email_only_signup_pmpro_init() {
	// Return early if the user is logged in.
	if ( is_user_logged_in() ) {
		return;
	}

	// Don't break if PMPro is out of date or not loaded.
	if ( ! function_exists( 'pmpro_add_user_field' ) ) {
		return false;
	} 
	
	// Store our field settings in an array.
	$fields = array();
	$fields[] = new PMPro_Field(
		'first_name',
		'text',
		array(
			'label' => 'First Name',
			'profile' => false,
			'required' => true
		)
	);
	$fields[] = new PMPro_Field(
		'last_name',
		'text',
		array(
			'label' => 'Last Name',
			'profile' => false,
			'required' => true
		)
	);
	$fields[] = new PMPro_Field(
		'bemail',
		'text',
		array(
			'label' => 'Email Address',
			'profile' => false
		)
	);
	$fields[] = new PMPro_Field(
		'bconfirmemail_copy',
		'hidden',
		array(
			'label' => '&nbsp;',
			'value' => '1',
		)
	);

	// Add a field group to put our fields into.
	pmpro_add_field_group( 'Account Information' );

	//add the fields into a new checkout_boxes are of the checkout page
	foreach ( $fields as $field ) {
		pmpro_add_user_field(
			'Account Information',
			$field
		);
	}
}
add_action( 'init', 'simple_checkout_name_email_only_signup_pmpro_init' );