<?php

/**
 * Display credit card logos before the submit button on the Paid Memberships Pro Membership Checkout page.
 * 
 * title: Add Credit Cards Logos to Membership Checkout
 * layout: snippet
 * collection: checkout
 * category: logo
 * url: https://www.paidmembershipspro.com/add-credit-card-and-paypal-logos-to-checkout/
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_add_cc_logos_to_checkout() {
	global $pmpro_level;

	// Only show the credit card logos if the level is not free.
	if ( pmpro_isLevelFree( $pmpro_level ) ) {
		return;
	}

	// Display the credit card logos.
	?>
	<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_card' ) ); ?>">
		<h2 class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_card_title pmpro_font-large' ) ); ?>">Accepted Credit Cards</h2>
		<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_card_content' ) ); ?>">
			<p>We accept Visa, Mastercard, Discover, and American Express.</p>
			<p><img alt="Credit card logos for Visa, Mastercard, Discover, and American Express" style="max-width: 300px;" src="<?php echo esc_url( plugins_url( '/images/pay-with-credit-cards.png', __FILE__ ) ); ?>"></p>
		</div> <!-- end pmpro_card_content -->
	</div> <!-- end pmpro_card -->
	<?php
}
add_action( 'pmpro_checkout_after_billing_fields', 'my_pmpro_add_cc_logos_to_checkout', 10 );
