<?php
/**
 * Adjust Your Membership Site’s Default Currency Format
 *
 * This recipe will change the format from DKK 1,495.00 to DKK 1 495,00
 *
 * title: Adjust Your Membership Site’s Default Currency Format
 * layout: snippet
 * collection: checkout
 * category: payment-plans
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * pmpro_eu_dkk_format  This function changes the values of a specific keys for a currency in the `$pmpro_currencies` array.
 *
 * @param  array $pmpro_currencies The currency array created by PMPro
 * @return array                   The adjusted currency array
 */
function pmpro_eu_dkk_format( $pmpro_currencies ) {

	$pmpro_currencies['DKK'] = array(
		'name'                => __( 'Danish Krone', 'paid-memberships-pro' ),
		'decimals'            => '2',
		'thousands_separator' => '&nbsp;',
		'decimal_separator'   => ',',
		'symbol'              => 'DKK&nbsp;',
		'position'            => 'left',
	);

	return $pmpro_currencies;

}
add_filter( 'pmpro_currencies', 'pmpro_eu_dkk_format' );
