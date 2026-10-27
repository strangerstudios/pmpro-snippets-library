<?php
/**
 * Show a landing page's membership level price and checkout button in the Memberlite 8.0+ masthead,
 * and the checkout button and level upsell after the page content.
 * Memberlite 8.0 removed the Landing Page template and moved saved values to the
 * memberlite_landing_page_level, memberlite_landing_page_checkout_button and
 * memberlite_landing_page_upsell custom fields. This recipe shows them again.
 *
 * Requires Paid Memberships Pro. The upsell requires the Advanced Levels Page Shortcode Add On.
 *
 * title: Show the Legacy Landing Page Level and Checkout Button in Memberlite 8.0
 * layout: snippet
 * collection: memberlite
 * category: design
 * link: https://www.paidmembershipspro.com/memberlite-legacy-page-settings/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Get the membership level set for the current landing page.
 *
 * @return object|false The level, or false if the page has no landing page level.
 */
function my_memberlite_get_landing_page_level() {
	// Only single pages, not the posts page: the Landing Page template never applied there.
	if ( ! is_page() || ! function_exists( 'pmpro_getLevel' ) ) {
		return false;
	}

	$level_id = (int) get_post_meta( get_queried_object_id(), 'memberlite_landing_page_level', true );

	return $level_id ? pmpro_getLevel( $level_id ) : false;
}

/**
 * Get the checkout button HTML for a landing page level.
 *
 * @param object $level The membership level.
 * @return string
 */
function my_memberlite_get_landing_page_button( $level ) {
	$text = get_post_meta( get_queried_object_id(), 'memberlite_landing_page_checkout_button', true );
	if ( empty( $text ) ) {
		$text = __( 'Select', 'pmpro-snippets-library' );
	}

	return '<p><a class="btn btn_action" href="' . esc_url( add_query_arg( 'level', $level->id, pmpro_url( 'checkout' ) ) ) . '">' . esc_html( $text ) . '</a></p>';
}

/**
 * Show the level description, price and checkout button in the masthead.
 *
 * @return void
 */
function my_memberlite_landing_page_masthead() {
	$level = my_memberlite_get_landing_page_level();
	if ( empty( $level ) ) {
		return;
	}

	// The level description showed when the page had no banner description.
	if ( '' === trim( (string) get_post_meta( get_queried_object_id(), 'memberlite_banner_desc', true ) ) ) {
		echo wp_kses_post( wpautop( do_shortcode( $level->description ) ) );
	}

	echo '<p class="pmpro_level-price">' . wp_kses_post( pmpro_getLevelCost( $level, true, true ) ) . '</p>';
	echo my_memberlite_get_landing_page_button( $level ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'memberlite_after_masthead_inner', 'my_memberlite_landing_page_masthead', 20 );

/**
 * Add styles for the level price in the masthead.
 *
 * @return void
 */
function my_memberlite_landing_page_css() {
	wp_add_inline_style( 'memberlite_style', '.masthead .pmpro_level-price { font-size: var(--wp--preset--font-size--36); } .masthead .pmpro_level-subprice { display: block; font-size: var(--wp--preset--font-size--18); }' );
}
add_action( 'wp_enqueue_scripts', 'my_memberlite_landing_page_css', 20 );

/**
 * Show the checkout button and level upsell after the page content.
 *
 * @return void
 */
function my_memberlite_landing_page_after_content() {
	$level = my_memberlite_get_landing_page_level();
	if ( empty( $level ) ) {
		return;
	}

	echo my_memberlite_get_landing_page_button( $level ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	$upsell = (int) get_post_meta( get_queried_object_id(), 'memberlite_landing_page_upsell', true );
	if ( $upsell && shortcode_exists( 'pmpro_advanced_levels' ) ) {
		echo '<hr />';
		echo do_shortcode( '[pmpro_advanced_levels levels="' . $upsell . '" back_link="false"]' );
	}
}
add_action( 'memberlite_after_content_page', 'my_memberlite_landing_page_after_content' );
