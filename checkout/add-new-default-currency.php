<?php
/**
 * Add and Set a New Default Currency using $pmpro_currencies
 *
 * title: Add and Set a New Default Currency using $pmpro_currencies
 * layout: snippet
 * collection: checkout
 * category: currency
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function pmpro_currencies_ruble( $currencies ) {

	$currencies['RUB'] = __( 'Russian Ruble (RUB)', 'pmpro' );

	return $currencies;

}
add_filter( 'pmpro_currencies', 'pmpro_currencies_ruble' );
