<?php
/**
 * This code recipe automatically calculates the tax and adds
 * it to the level cost text on the checkout page as soon as a 
 * member selects their billing country and state. 
 *
 * title: Update the Level Cost Text Based on Country and State
 * collection: checkout
 * category: tax
 * link: https://www.paidmembershipspro.com/update-level-cost-text-country-state/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function mypmpro_tax_update_script() {

	$level = pmpro_getLevelAtCheckout();
	$tax_state = pmpro_getOption( 'tax_state' );
	$tax_rate = floatval( pmpro_getOption( 'tax_rate' ) );
	?>
	<script type="text/javascript">
		
		jQuery(document).ready(function($) {
			const originalBillingText = $("#pmpro_level_cost").html(); // Store the original cost text

			$("body").on("change", "#bstate", function() {
				let selectedState = $(this).val();
				if ( selectedState === '<?php echo $tax_state; ?>' ) {
					const billingText = `<?php
						if( !empty( $tax_rate ) ) {
							$level->initial_payment += ( $level->initial_payment * $tax_rate );
							$level->billing_amount += ( $level->billing_amount * $tax_rate );
						}
						echo pmpro_getLevelCost( $level );
					?>`;
					
					$("#pmpro_level_cost").html( billingText );
				} else {
					// Restore original cost when selecting a non-taxable state
					$("#pmpro_level_cost").html( originalBillingText );
				}
			});
		});
	</script>
	<?php
}
add_action( 'wp_footer', 'mypmpro_tax_update_script' );

function mypmpro_override_tax_text( $translated_text, $text, $domain ){

	if( $text === 'Customers in %1$s will be charged %2$s%% tax.' ) {
		$translated_text = 'Customers in %1$s are automatically charged a %2$s%% tax.';
	}

	return $translated_text;
}
add_filter( 'gettext', 'mypmpro_override_tax_text', 10, 3 );

