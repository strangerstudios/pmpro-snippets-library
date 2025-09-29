<?php
/**
 * Customizations to remove all mention of payments/fees/price for a 100% no-charge membership site.
 *
 * title: Remove All Price Mentions for Free Membership Sites
 * layout: snippet
 * collection: core
 * category: customization
 * link: https://www.paidmembershipspro.com/remove-all-mention-of-price-for-a-100-no-charge-membership-site/
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Update line 21 with the ID of your membership level. 
 * This recipe assumes you have a single level of membership.
*/
define( 'PMPRO_DEFAULT_LEVEL', '1' );

function my_pmpro_level_cost_text( $text, $level ) {
	if ( pmpro_isLevelFree( $level ) ) {
		return '';
	}

	return $text;
}
add_filter( 'pmpro_level_cost_text', 'my_pmpro_level_cost_text', 10, 2 );
