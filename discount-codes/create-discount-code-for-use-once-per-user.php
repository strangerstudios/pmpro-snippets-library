<?php

/**
 * Create a discount code that can be used only once by each user, ideal for trial membership levels
 * & Restrict the discount code usage to one-time use per user with this code
 * Update the $one_time_use_codes array on line 23
 *
 * title: Restrict discount code usage to one-time per user
 * layout: snippet
 * collection: discount-codes
 * category: checkout, trial
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

//check if codes have been used already
function my_pmpro_check_discount_code($okay, $dbcode, $level_id, $code)
{
	//define one time use codes - replace TRIAL and TRIALTWO with your discount code name
	$one_time_use_codes = array('TRIAL', 'TRIALTWO'); 	//make them all uppercase - use array('TRIAL'); for one discount code only
	
	//check if the code being used is a one time code
	if(is_user_logged_in() && in_array(strtoupper($code), $one_time_use_codes))
	{
		//see if user has used this code already
		global $current_user;
		$used_codes = $current_user->pmpro_used_codes;	//stored in user meta
		if(is_array($used_codes) && in_array(strtoupper($code), $used_codes))
			return "You have already used the discount code provided.";
	}
	
	return $okay;
}
add_filter('pmpro_check_discount_code', 'my_pmpro_check_discount_code', 10, 4);

//remember which codes have been used after checkout
function my_pmpro_after_checkout($user_id)
{
	global $discount_code;
	
	if(!empty($discount_code))
	{
		$used_codes = get_user_meta($user_id, 'pmpro_used_codes', true);
		if(empty($used_codes))
			$used_codes = array();
			
		$used_codes[] = strtoupper($discount_code);
		
		update_user_meta($user_id, "pmpro_used_codes", $used_codes);
	}
}

add_action('pmpro_after_checkout', 'my_pmpro_after_checkout');
