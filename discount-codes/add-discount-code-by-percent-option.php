<?php
/**
 * This recipe will add a the option to add percentage discount to your discount code. This is visible on the
 * Discount Codes page for admin reference.
 *
 * title: Add Percentage option to discount codes
 * layout: snippet
 * collection: discount-codes
 * category: custom-fields, description, percent
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function pmpropd_pmpro_discount_code_after_level_settings( $code_id, $level ) {
	$percents = pmpro_getDCPDs( $code_id );
	if ( ! empty( $percents [ $level->id ] ) ) {
		$percent = $percents [ $level->id ];
	} else {
		$percent = '';
	}
	?>
	<table>
		<tbody class="form-table">
		<tr>
			<td>
		<tr>
			<th scope="row" valign="top"><label for="percent_discount"> Percent Discount:</label></th>
			<td><input name="percent_discount[]" type="text" size="20" value="<?php echo esc_attr( $percent); ?>" />% <small> Percentage Discount. This will override the other level settings.  </small></td>
		</tr>
		</td>
		</tr>
		</tbody>
	</table>


	<?php
}
add_action( 'pmpro_discount_code_after_level_settings', 'pmpropd_pmpro_discount_code_after_level_settings', 10, 2 );


function pmpropd_pmpro_save_discount_code_level( $code_id, $level_id ) {
	$all_levels_a         = $_REQUEST['all_levels'];                   
	$percent_discount_a = $_REQUEST['percent_discount'];  

	if ( ! empty( $all_levels_a ) ) {
		$key                 = array_search( $level_id, $all_levels_a );  
		$percents = pmpro_getDCPDs( $code_id );                
		$percents [ $level_id ] = $percent_discount_a[ $key ]; 
		pmpro_saveDCPDs( $code_id, $percents); 
	}
}
add_action( 'pmpro_save_discount_code_level', 'pmpropd_pmpro_save_discount_code_level', 10, 2 );


function pmpro_saveDCPDs( $code_id, $percents ) {
	$all_percents             = get_option( 'pmpro_discount_code_percent_discounts', array() );
	$all_percents[ $code_id ] = $percents;
	update_option( 'pmpro_discount_code_percent_discounts', $all_percents );
}


function pmpro_getDCPDs( $code_id ) {
	$all_percents = get_option( 'pmpro_discount_code_percent_discounts', array() );
	if ( ! empty( $all_percents ) && ! empty( $all_percents[ $code_id ] ) ) {
		return $all_percents[ $code_id ];
	} else {
		return false;
	}
}

function pmpropd_pmpro_discount_code_level($level, $code_id) {

	$percent = pmpro_getDCPDs($code_id);
	
	if(isset($percent) && isset($percent[$level->id])) {
		$original_level = pmpro_getLevel($level);
		$percent_off = $percent[$level->id];
		$level = $original_level;
		$level->initial_payment = (1 - 0.01*$percent_off)*$level->initial_payment;
		$level->billing_amount = (1 - 0.01*$percent_off)*$level->billing_amount;
	}
	
	return $level;
}
add_filter("pmpro_discount_code_level", "pmpropd_pmpro_discount_code_level", 10, 2);