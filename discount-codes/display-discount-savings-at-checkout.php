<?php
/**
 * Display original, discounted, and recurring prices when a discount code is applied at checkout.
 *
 * title: Display Discount Savings at Checkout
 * layout: snippet
 * collection: discount-codes
 * category: discount code, checkout, pricing
 * link: https://www.paidmembershipspro.com/display-the-original-and-discounted-price-when-a-discount-code-is-applied-at-checkout/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/

 */
function my_pmpro_applydiscountcode_return_js( $discount_code, $discount_code_id, $level_id, $code_level ) {

	// Only continue if code is valid.
	if ( empty( $code_level ) ) {
		return;
	}

	// Get the original level.
	$level = pmpro_getLevel( $level_id );

	if ( empty( $level ) ) {
		return;
	}

	// Initial payment amounts.
	$original_initial   = isset( $level->initial_payment ) ? floatval( $level->initial_payment ) : 0;
	$discounted_initial = isset( $code_level->initial_payment ) ? floatval( $code_level->initial_payment ) : 0;

	// Calculate savings.
	$savings = $original_initial - $discounted_initial;

	// Format prices.
	$original_price   = pmpro_formatPrice( $original_initial );
	$discounted_price = pmpro_formatPrice( $discounted_initial );
	$savings_price    = pmpro_formatPrice( $savings );

	// Build HTML.
	$html  = "<div class='pmpro-discorig-message pmpro-original-price'>Original price: {$original_price}</div>";
	$html .= "<div class='pmpro-discorig-message pmpro-discount-price'>Discounted price: {$discounted_price}</div>";

	if ( $savings > 0 ) {
		$html .= "<div class='pmpro-discorig-message pmpro-save-price'>You save {$savings_price}</div>";
	}

	// Handle recurring billing.
	if ( ! empty( $level->billing_amount ) ) {

		$original_recurring   = floatval( $level->billing_amount );
		$discounted_recurring = isset( $code_level->billing_amount ) ? floatval( $code_level->billing_amount ) : $original_recurring;

		// Compare floats safely.
		if ( abs( $original_recurring - $discounted_recurring ) > 0.001 ) {

			$discounted_recurring_price = pmpro_formatPrice( $discounted_recurring );
			$cycle_number = intval( $level->cycle_number );
			$cycle_period = esc_html( $level->cycle_period );

			$html .= "<div class='pmpro-discorig-message pmpro-recurring-price'>";
			$html .= "Then {$discounted_recurring_price} per {$cycle_number} {$cycle_period}";
			$html .= "</div>";
		}
	}

	// Output JS (AJAX-safe).
	echo "jQuery('.pmpro-discorig-message').remove();";

	// Display pricing details near the level cost (top of checkout).
	echo "jQuery('#pmpro_level_cost').append(" . wp_json_encode( $html ) . ");";

	// Display pricing details below the discount code message.
	// You can comment out the line below if you only want the message to appear at the top.
	echo "jQuery('#pmpro_message_bottom').append(" . wp_json_encode( $html ) . ");";
}

add_action( 'pmpro_applydiscountcode_return_js', 'my_pmpro_applydiscountcode_return_js', 10, 4 );