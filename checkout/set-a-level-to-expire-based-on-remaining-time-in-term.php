<?php
/**
 * Let users specify how far into a term they are at checkout and expire the level based on time remaining.
 *
 * title: Set a Level to Expire Based on Remaining Time in Term
 * layout: snippet
 * collection: checkout
 * category: membership-levels
 * link: https://www.paidmembershipspro.com/level-to-expire-time-in-term/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Add the Term field at checkout.
 */
function my_pmpro_add_term_user_field() {
	if ( ! function_exists( 'pmpro_add_user_field' ) ) {
		return false;
	}

	// Define the field for term selection.
	$fields   = array();
	$fields[] = new PMPro_Field(
		'terms',   // Input name and user meta key.
		'select',  // Type of field.
		array(
			'levels'        => array( 1, 2 ), // Only show on these membership levels.
			'showmainlabel' => false,         // Hide field label. We'll use the field group label.
			'options'       => array(         // <option> elements for select field.
				''  => 'Select an Option',
				'1' => '1 Month',
				'2' => '2 Months',
				'3' => '3 Months',
				'4' => '4 Months',
				'5' => '5 Months',
				'6' => '6 Months',
				'7' => '7 Months',
				'8' => '8 Months',
				'9' => '9 Months',
			),
		)
	);

	// Add a field group to put our field into.
	pmpro_add_field_group( 'term-progress', 'How far into the term are you?' );

	// Add our fields into that group.
	foreach ( $fields as $field ) {
		pmpro_add_user_field(
			'term-progress', // Which group to add to.
			$field           // PMPro_Field object.
		);
	}
}
add_action( 'init', 'my_pmpro_add_term_user_field' );

/**
 * Adjust the expiration date
 */
function my_pmpro_checkout_level_set_expiration_from_term( $level ) {
	if ( ! empty( $_REQUEST['terms'] ) ) {
		$total_months = 10; // Do this so that if someone selects 9 months, they still have 1 month ahead of them.

		$terms = intval( $_REQUEST['terms'] );

		$remainder_months = $total_months - $terms;

		$level->expiration_number = $remainder_months;
		$level->expiration_period = 'Month';
	}
	return $level;
}
add_filter( 'pmpro_checkout_level', 'my_pmpro_checkout_level_set_expiration_from_term' );
