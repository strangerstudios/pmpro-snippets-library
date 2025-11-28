<?php
/**
 * Provide Discount for Early Membership Renewal
 *
 * title: Early Renewal Discount
 * layout: snippet
 * collection: checkout
 * category: Membership Renewal
 * link: https://www.paidmembershipspro.com/provide-discount-early-membership-renewal/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method:
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function pmpro_early_renewal_discount_checkout_level( $level ) {
	global $discount_code;
	if ( ! empty( $discount_code ) ) {
		return $level;
	}

	// Level IDs and their early renewal price.
	$discounts = array(
		1 => 49,
		2 => 49,
		3 => 49,
		4 => array( //use an array to change more than initial_payment
			'initial_payment'    => 49,
			'expiration_number'  => 1,
		),
	);

	// Only apply if the user already has the level.
	if ( pmpro_hasMembershipLevel( $level->id ) && isset( $discounts[$level->id] ) ) {
		if ( is_array( $discounts[$level->id] ) ) {
			foreach ( $discounts[$level->id] as $key => $value ) {
				$level->$key = $value;
			}
		} else {
			$level->initial_payment = $discounts[$level->id];
		}
	}

	return $level;
}
add_filter( 'pmpro_checkout_level', 'pmpro_early_renewal_discount_checkout_level' );

/*
 * Add "Early Renewal" message to price text on checkout
 */
function pmpro_early_renewal_discount_cost_text( $text, $level ) {
	// Only show the message if the user already has this level.
	if ( pmpro_hasMembershipLevel( $level->id ) ) {
		$text .= '<br><em>(Early renewal membership price applied)</em>';
	}

	return $text;
}
add_filter( 'pmpro_level_cost_text', 'pmpro_early_renewal_discount_cost_text', 10, 2 );
