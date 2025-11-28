<?php
/**
 * Hide discount code field on the PMPro checkout page for all free levels.
 * 
 * title: Hide discount code field on the PMPro checkout page for all free levels.
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
function my_pmpro_hide_discount_code_field_for_free_levels( $show ) {
  global $pmpro_level;
  
  if ( function_exists( 'pmpro_isLevelFree' ) && pmpro_isLevelFree( $pmpro_level ) ) {
    $show = false;
  }

  return $show;
}
add_filter('pmpro_show_discount_code', 'my_pmpro_hide_discount_code_field_for_free_levels');
