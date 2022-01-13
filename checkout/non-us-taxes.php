<?php
/**
 * Example of applying Canadian Tax to Paid Memberships Pro Checkouts. Adjust the code to match the requirements needed.
 * 
 * title: Working with non-us tax
 * layout: snippet
 * collection: checkout
 * category: tax
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
// Set the tax to 7% for all Canadian customers.
function customtax_pmpro_tax( $tax, $values, $order ) {  	
	$tax = round( (float)$values[price] * 0.07, 2 );		
	return $tax;
}
 
function customtax_pmpro_level_cost_text( $cost, $level ) {
	//only applicable for levels > 1
	$cost .= ' Members in British Columbia will be charged a 7% tax.';
	
	return $cost;
}
add_filter( 'pmpro_level_cost_text', 'customtax_pmpro_level_cost_text', 10, 2 );
 
//add BC checkbox to the checkout page
function customtax_pmpro_checkout_boxes() {
?>
<table id='pmpro_pricing_fields' class='pmpro_checkout' width='100%' cellpadding='0' cellspacing='0' border='0'>
<thead>
	<tr>
		<th>
			British Columbia Residents
		</th>						
	</tr>
</thead>
<tbody>                
	<tr>	
		<td>
			<div>				
				<input id='taxregion' name='taxregion' type='checkbox' value='1' <?php if ( ! empty( $_REQUEST['taxregion'] ) || ! empty( $_SESSION['taxregion'] ) ) {?>checked='checked'<?php } ?> /> Check this box if your billing address is in British Columbia, Canada.
			</div>				
		</td>
	</tr>
</tbody>
</table>
<?php
}
add_action( 'pmpro_checkout_boxes', 'customtax_pmpro_checkout_boxes' );
 
//update tax calculation if buyer is danish
function customtax_region_tax_check() {
	//check request and session
	if ( isset( $_REQUEST['taxregion'] ) ) {
		//update the session var
		$_SESSION['taxregion'] = $_REQUEST['taxregion'];	
		
		//not empty? setup the tax function
		if ( ! empty( $_REQUEST['taxregion'] ) ) {
			add_filter( 'pmpro_tax', 'customtax_pmpro_tax', 10, 3 );
        }
	} else if( ! empty( $_SESSION['taxregion'] ) ) {
		//add the filter
		add_filter( 'pmpro_tax', 'customtax_pmpro_tax', 10, 3 );
	} else {
		//check state and country
		if ( ! empty( $_REQUEST['bstate'] ) && ! empty( $_REQUEST['bcountry'] ) ) {
			$bstate = trim( strtolower( $_REQUEST['bstate'] ) );
			$bcountry = trim( strtolower( $_REQUEST['bcountry'] ) );
			if ( ( $bstate == "bc" || $bstate == 'british columbia' ) && $bcountry == 'ca' ) {
				//billing address is in BC
				add_filter( 'pmpro_tax', 'customtax_pmpro_tax', 10, 3 );
			}
		}
	}
}
add_action( 'init', 'customtax_region_tax_check' );
 
//remove the taxregion session var on checkout
function customtax_pmpro_after_checkout() {
	if ( isset( $_SESSION['taxregion'] ) ) {
        unset( $_SESSION['taxregion'] );
    }	
}
add_action( 'pmpro_after_checkout', 'customtax_pmpro_after_checkout' );