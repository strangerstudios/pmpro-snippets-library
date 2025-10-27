<?php
/**
 * Hide discount code field on the PMPro checkout page for specified levels.
 * 
 * title: Hide Discount Dode Field on the PMPro Checkout Page for Specified Levels.
 * layout: snippet
 * collection: discount-codes
 * category: checkout
 * link: https://www.paidmembershipspro.com/hide-discount-code-field-on-membership-checkout-for-free-levels/
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function hide_discount_code_field_for_specific_levels($show)
{
  global $pmpro_level;
  
  // Change the array with your Level ID's  
  if( in_array( $pmpro_level->id, array(1,2) ) )
  {
	  $show = false;
  }
  return $show;
}
add_filter('pmpro_show_discount_code', 'hide_discount_code_field_for_specific_levels');