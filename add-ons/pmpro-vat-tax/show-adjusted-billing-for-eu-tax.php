<?php
/** 
 * Updates the level cost text based on the Country selected when using the VAT Tax Add On.
 *
 * title: Updates the level cost text based on the Country selected when using the VAT Tax Add On.
 * layout: snippet
 * collection: add-ons, pmpro-vat-tax
 * category: level-cost-text
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function mypmpro_tax_update_script(){

	global $pmpro_levels, $pmpro_vat_by_country;
	
	$level = ( isset( $_REQUEST['level'] ) ? $pmpro_levels[$_REQUEST['level']] : '' );

	?>
	<script type="text/javascript">
		
		var vat_rates = <?php echo json_encode( $pmpro_vat_by_country ); ?>;

		jQuery(document).ready(function(){
			
			var ajax_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';

			var country = jQuery( "#bcountry" ).val();

			var vat_string = '';

			jQuery.each( vat_rates, function( key, val ){
				if( country === key ){
					//Charge VAT

					var data = {
						action: 'mypmpro_get_level_cost_text',
						level_id: '<?php echo $_REQUEST['level']; ?>',
						vat_rate: val,
						discount_code: jQuery("#other_discount_code").val()
					}
					
					jQuery.post( ajax_url, data, function( response ){

						var vat_string = ' Including '+(val*100)+'% VAT'; // Optional. Prints: Including 21% VAT (for example)
						jQuery("#pmpro_level_cost").html( response + vat_string );

						// jQuery(".pmpro_submit").prepend( response ); //Optional if you want to display it at the bottom of the page too.
					});												

				}
			});

			jQuery("body").on("change", "#bcountry", function(){
				
				var country = jQuery(this).val();
				var vat_string = '';

				jQuery.each( vat_rates, function( key, val ){
					if( country === key ){
						//Charge VAT

						var data = {
							action: 'mypmpro_get_level_cost_text',
							level_id: '<?php echo $_REQUEST['level']; ?>',
							vat_rate: val,
							discount_code: jQuery("#other_discount_code").val()
						}
						
						jQuery.post( ajax_url, data, function( response ){

							var vat_string = ' Including '+(val*100)+'% VAT'; // Optional. Prints: Including 21% VAT (for example)
							jQuery("#pmpro_level_cost").html( response + vat_string );

							// jQuery(".pmpro_submit").prepend( response ); //Optional if you want to display it at the bottom of the page too.
						});												

					}
				});

			});
		});

	</script>
	<?php
}
add_action( 'wp_footer', 'mypmpro_tax_update_script' );

function mypmpro_generate_level_cost_text(){

	if( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'mypmpro_get_level_cost_text' ){

		$discount_code = isset( $_REQUEST['discount_code'] ) ? sanitize_text_field( $_REQUEST['discount_code'] ) : null;

		$checkout_level = pmpro_getLevelAtCheckout( $_REQUEST['level_id'], $discount_code );

		$vat_rate = floatval( $_REQUEST['vat_rate'] );

		$checkout_level->initial_payment = $checkout_level->initial_payment + ( $checkout_level->initial_payment  * $vat_rate ); 

		$checkout_level->billing_amount = $checkout_level->billing_amount + ( $checkout_level->billing_amount * $vat_rate ); 
		
		echo pmpro_getLevelCost( $checkout_level );

		wp_die();
	}

}
add_action( 'wp_ajax_mypmpro_get_level_cost_text', 'mypmpro_generate_level_cost_text' );
add_action( 'wp_ajax_nopriv_mypmpro_get_level_cost_text', 'mypmpro_generate_level_cost_text' );