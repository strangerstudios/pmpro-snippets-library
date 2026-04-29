<?php
/**
 * Remove Payment References for Free Membership Sites
 *
 * title: Remove Payment References for Free Membership Sites
 * layout: snippet
 * collection: membership-levels
 * category: free-membership
 * link: https://www.paidmembershipspro.com/how-to-set-up-a-membership-site-for-free-members-only/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method:
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/*
	Customizations to remove all mention of payments/fees/price for a 100% no-charge membership site.
*/

function my_pmpro_level_cost_text( $text, $level ) {
	if ( pmpro_isLevelFree( $level ) ) {
		return '';
	} else {
		return $text;
	}
}
add_filter( 'pmpro_level_cost_text', 'my_pmpro_level_cost_text', 10, 2 );
