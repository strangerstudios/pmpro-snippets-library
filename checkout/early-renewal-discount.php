<?php
/**
 * Provide Discount for Early Membership Renewal
 *
 * title: Early Renewal Discount
 * layout: snippet
 * collection: checkout
 * category: Membership Renewal
 * link: https://www.paidmembershipspro.com/provide-discount-early-membership-renewal/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method:
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function pmpro_early_renewal_discount($level)
{
	//if a discount code is being used, ignore the renewal price
	global $discount_code;
	if(!empty($discount_code))
		return $level;
	
	//this is an array of level ids this renewal offer is available for and the renewal cost for each
	$discounts = array(
		1 => 49,
		2 => 49,
		3 => 49,
		4 => array(		//use an array to change more than initial_payment
			'initial_payment' => 49,
			'expiration_number' => 1
		),
	);

	//only if you already have the level
	if(pmpro_hasMembershipLevel($level->id) && in_array($level->id, array_keys($discounts)))
	{
		//check if an array or else (number) was given
		if(is_array($discounts[$level->id]))
		{
			//adjust each value
			foreach($discounts[$level->id] as $key => $value)
			{
				$level->$key = $value;
			}
		}
		else
		{
			//assume the initial price is meant
			$level->initial_payment = $discounts[$level->id];
		}
	}

	return $level;
}
add_filter('pmpro_checkout_level', 'pmpro_early_renewal_discount');