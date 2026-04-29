<?php
/**
 * Show non-discounted price with strike through.
 *
 * Edit the membership level to use the discounted price.
 * This specifically changes occurrences of $47 to ~$97~ $47.
 * You will need to adjust for your specific prices.
 *
 * title: Run a “Sale” on Your Membership Site and Show the Regular Price with Strikethrough Style.
 * layout: snippet
 * collection: misc
 * category:  level-cost-text,frontend-pages
 * link: https://www.paidmembershipspro.com/run-sale-membership-site-show-regular-price-strikethrough-style/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_level_cost_text_sale_strikethrough( $text, $level, $tags, $short ) {
	// Bail if we're in the WP admin.
	if ( is_admin() ) {
		return $text;
	}

	// Adjust for your specific level ID and prices.
	if ( '2' === $level->id ) {
		$regular_price       = pmpro_formatPrice( 97 );
		$sale_price          = pmpro_formatPrice( 47 );
		$strikethrough_price = '<span style="text-decoration: line-through">' . $regular_price . '</span> ' . $sale_price;
		$text                = str_replace( $sale_price, $strikethrough_price, $text );
	}

	return $text;
}
add_filter( 'pmpro_level_cost_text', 'my_pmpro_level_cost_text_sale_strikethrough', 10, 4 );
