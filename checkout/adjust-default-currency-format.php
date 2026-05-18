<?php
/**
 * Adjust Your Membership Site’s Default Currency Format
 *
 * This example changes the default Danish Krone currency format from DKK 1,495.00 to DKK 1 495,00.
 * You can adjust the settings for any currency supported by PMPro using this same method.
 *
 * title: Adjust Your Membership Site’s Default Currency Format
 * layout: snippet
 * collection: checkout
 * category: currency
 * link: https://www.paidmembershipspro.com/how-to-adjust-your-membership-sites-default-currency-format/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Change the Danish Krone currency format settings.
 *
 * @param array $pmpro_currencies The currency array created by PMPro.
 * @return array The adjusted currency array.
 */
function pmpro_eu_dkk_format( $pmpro_currencies ) {

	$pmpro_currencies['DKK'] = array(
		'name'                => __( 'Danish Krone', 'pmpro-snippets-library' ),
		'decimals'            => '2',
		'thousands_separator' => '&nbsp;',
		'decimal_separator'   => ',',
		'symbol'              => 'DKK&nbsp;',
		'position'            => 'left',
	);

	return $pmpro_currencies;
}
add_filter( 'pmpro_currencies', 'pmpro_eu_dkk_format' );
