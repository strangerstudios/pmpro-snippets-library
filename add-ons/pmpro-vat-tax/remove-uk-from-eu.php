<?php
/**
 * This recipe will remove the UK from the list of EU countries to validate VAT numbers.
 *
 * title: Remove UK from list of EU countries.
 * layout: snippet
 * collection: add-ons, pmpro-vat-tax
 * category: UK, EU
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
 function mypmpro_remove_uk_from_eu( $countries ){

 	unset( $countries['GB'] );

 	return $countries;

 }
 add_filter( 'pmpro_vat_by_country', 'mypmpro_remove_uk_from_eu', 10, 1 );
 add_filter( 'pmpro_european_union', 'mypmpro_remove_uk_from_eu', 10, 1 );
