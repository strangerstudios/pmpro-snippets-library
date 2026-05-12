<?php
/**
 * Add Australian GST to your membership site
 *
 * title: Custom tax structure where all levels except level 1 have 7.25% tax if billing state is CA.
 * layout: snippet
 * collection: checkout
 * category: tax, AU
 * link: https://www.paidmembershipspro.com/australian-gst/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/*  
  Tax solution for Australia
  
  This solution assume the GST tax rate of 10% from March 15th, 2017. Ask your accountant how much tax you must charge.
  More info: https://www.business.gov.au/info/run/tax/register-for-goods-and-services-tax-gst
  
  Edit as needed, then save this file in your plugins folder and activate it through the plugins page in the WP dashboard.
*/ 

//add tax info to cost text. this is enabled if the Australia checkbox is checked.
function agst_pmpro_tax( $tax, $values, $order ) {
	$tax = round( ( float )$values['price'] * 0.1, 2 );
	return $tax;
}
//add tax info to cost text.
function agst_pmpro_level_cost_text( $cost, $level ) {
	//only applicable for levels > 1
	$cost .= __( ' Customers in Australia will be charged a 10% GST.', 'pmpro-snippets-library' );
	return $cost;
}
add_filter( 'pmpro_level_cost_text', 'agst_pmpro_level_cost_text', 10, 2 );
 
//set the default country to Australia
function agst_pmpro_default_country( $country ) {
	return 'AU';
}
add_filter( 'pmpro_default_country', 'agst_pmpro_default_country' );
 
//add AU checkbox to the checkout page
function agst_pmpro_checkout_boxes() {
?>
	<div class='pmpro_card'>
		<div class='pmpro_card_content'>
			<legend class='pmpro_form_legend'>
				<h2 class='pmpro_form_heading pmpro_font-large'>
					<?php _e( 'Australian Residents', 'pmpro-snippets-library' );?>
				</h2>
			</legend>
			<div id='pmpro_pricing_fields'>
				<input id='taxregion' name='taxregion' type='checkbox' value='1'
				<?php if( !empty( $_REQUEST['taxregion'] ) || !empty( $_SESSION['taxregion'] ) ) { ?>
				checked='checked'<?php } ?> />
				<label for='taxregion' class='pmpro_normal pmpro_label-inline pmpro_clickable'>
					<?php _e( 'Check this box if your billing address is in Australia.', 'pmpro-snippets-library' );?>
				</label>
			</div>
		</div>
	</div>
<?php
}
add_action( 'pmpro_checkout_boxes', 'agst_pmpro_checkout_boxes' );
 
//update tax calculation if buyer is Australian
function agst_pmpro_region_tax_check() {
	//check request and session
	if ( isset( $_REQUEST['taxregion'] ) ) {
		//update the session var
		$_SESSION['taxregion'] = $_REQUEST['taxregion'];

		//not empty? setup the tax function
		if ( ! empty( $_REQUEST['taxregion'] ) ) {
			add_filter('pmpro_tax', 'agst_pmpro_tax', 10, 3);
		}
	} elseif ( !empty( $_SESSION['taxregion'] ) ) {
		//add the filter
		add_filter( 'pmpro_tax', 'agst_pmpro_tax', 10, 3 );
	} else {
		//check state and country
		if ( !empty( $_REQUEST['bcountry'] ) ) {
			$bcountry = trim( strtolower( $_REQUEST['bcountry'] ) );
			if ( $bcountry == 'au' ) {
				//billing address is in AU
				add_filter( 'pmpro_tax', 'agst_pmpro_tax', 10, 3 );
			}
		}
	}
}
add_action( 'init', 'agst_pmpro_region_tax_check' );
 
//remove the taxregion session var on checkout
function agst_pmpro_after_checkout() {
	if ( isset( $_SESSION['taxregion'] ) ) {
		unset( $_SESSION['taxregion'] );
	}
}
add_action( 'pmpro_after_checkout', 'agst_pmpro_after_checkout' );