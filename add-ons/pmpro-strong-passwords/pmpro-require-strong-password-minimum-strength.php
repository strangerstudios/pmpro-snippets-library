<?php 

/**
 * This recipe works with the PMPro Require Strong Passwords Add On to increase 
 * the minimum password strength required during membership checkout or signup.
 * Using the pmprosp_minimum_password_score filter 
 *
 * title: Require a Stronger Password Score for Member Registration
 * layout: snippet
 * collection: add-ons, pmpro-strong-passwords
 * category: password
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Set the minimum acceptable password score to 3 (strong).
 * Score Reference:
 * 0 = very weak
 * 1 = weak
 * 2 = medium
 * 3 = strong
 * 4 = very strong
 *
 */
function my_pmprosp_minimum_password_score( $min_strength, $password_strength ) {
	$min_strength = 3; // Require strong or very strong passwords.
	return $min_strength;
}
add_filter( 'pmprosp_minimum_password_score', 'my_pmprosp_minimum_password_score', 10, 2 );

