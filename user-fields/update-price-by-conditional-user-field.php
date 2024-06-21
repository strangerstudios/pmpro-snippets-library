<?php
/**
 * Use this code recipe in combination with custom user fields to set up a checkout field that adjusts the membership price, either for the initial payment, recurring payments, or both.
 * 
 * title: Update Price based on a user field selection.
 * layout: snippet
 * collection: user-fields, conditional
 * category: custom-fields
 * link: https://www.paidmembershipspro.com/modify-level-price-at-checkout-based-on-user-selections/
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */


/**
 * If a user checked option1, then add $100 to the price.
 */
function my_pmpro_checkout_level($level) {
	if( ! empty( $_REQUEST['option1'] ) || ! empty( $_SESSION['option1'] ) ) {
		$level->initial_payment = $level->initial_payment + 100;
		//$level->billing_amount = $level->billing_amount + 100;	//to update recurring payments too
	}

	return $level;
}
add_filter("pmpro_checkout_level", "my_pmpro_checkout_level");