<?php
/**
 * Allow members to select a membership duration at checkout. This code adjusts the amount charged at checkout to include an early payment discount and set expiration date of the membership accordingly.
 *
 * title: Allow members to select a membership duration at checkout
 * layout: snippet
 * collection: checkout
 * category: fields
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Create a dropdown field for "Membership Duration" with three options.
 *
 */
function my_pmpro_add_level_duration_field_to_checkout() {
	// Don't break if PMPro is out of date or not loaded.
	if ( ! function_exists( 'pmpro_add_user_field' ) ) {
		return false;
	} 

	// Store our field settings in an array.
	$fields = array();

	// Define the fields.
	$fields[] = new PMPro_Field(
		'membership_duration',
		'select',
		array(
			'label' => 'Membership Duration',
			'options' => array(
				'no' => '1 Year',
				2 => '2 Years',
				3 => '3 Years',
			),
			'levels' => array( 1 ),
		)
	);

	// Add a field group to put our fields into.
	pmpro_add_field_group( 'Membership Term' );

	// Add the fields into a new area of the checkout page.
	foreach ( $fields as $field ) {
		pmpro_add_user_field(
			'Membership Term',
			$field
		);
	}
}
add_action( 'init', 'my_pmpro_add_level_duration_field_to_checkout' );

/**
 * Alter the amount charged and membership expiration date
 * based on the chosen membership_duration value.
 *
 * @param  object $level The $pmpro_level object prior to duration being added.
 *
 * @return object $level The callback function returns the modified $pmpro_level object with the added duration.
 */
function my_pmpro_checkout_level_adjust_membership_duration_and_price( $level ) {
	// Get the value of the membership_duration field from the submission.
    $duration = isset( $_REQUEST['membership_duration'] ) ? $_REQUEST['membership_duration'] : '';

	// Bail if no duration is set.
	if ( empty( $duration ) ) {
		return $level;
	}

	// Bail if the user hasn't selected a duration and use base level settings.
	if ( 'no' === $duration ) {
		return $level;
	}

	// Duration for 2 years, give 20% discount.
	if ( '2' === $duration ) {
		$duration = 2;
		$discount_percentage = 0.2;
	}

	// Duration for 3 years, give 25% discount.
	if ( '3' === $duration ) {
		$duration = 3;
		$discount_percentage = 0.25;
	}

	/**
	 * Set membership expiration date and adjust the charged amount.
	 */
	if ( is_int( $duration ) ) {

		// Set the level term.
		$level->expiration_number = $duration;
		$level->expiration_period = 'Year';

		// Calculate the new initial payment with discount.
		$new_amount = $duration * $level->initial_payment;
		$discount_amount = $new_amount * $discount_percentage;

		// Set the level's new price.
		$level->initial_payment = round( $new_amount - $discount_amount );
	}

	return $level;
}
add_filter( 'pmpro_checkout_level', 'my_pmpro_checkout_level_adjust_membership_duration_and_price' );
