<?php
/**
 * Display messages of the Original Price, Discounted Price and Amount Saved when
 * a discount code is applied at checkout.
 *
 * title: Display the Original and Discounted Price when a Discount Code is Applied at Checkout.
 * layout: snippet
 * collection: checkout
 * category: discount-codes
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

	// Format prices.
	$original_price   = pmpro_formatPrice( $level->initial_payment );
	$discounted_price = pmpro_formatPrice( $code_level->initial_payment );
	$discount         = $level->initial_payment - $code_level->initial_payment;
	$discount         = pmpro_formatPrice( $discount );

	// Build HTML.
	$html  = "<div class='pmpro-discorig-message pmpro-original-price'>The original price is {$original_price}. </div>";
	$html .= "<div class='pmpro-discorig-message pmpro-discount-price'>The discounted price is {$discounted_price}. </div>";
	$html .= "<div class='pmpro-discorig-message pmpro-save-price'>You save {$discount}.</div>";
	?>
		jQuery( "#pmpro_level_cost" ).append( <?php echo wp_json_encode( $html ); ?> );
	<?php
}
add_action( 'pmpro_applydiscountcode_return_js', 'my_pmpro_applydiscountcode_return_js', 10, 4 );
