<?php
/**
 * Add extra banner padding to pages that had the setting turned on before Memberlite 8.0.
 * Memberlite 8.0 removed the extra banner padding setting and moved saved values
 * to the memberlite_banner_extra_padding custom field. This recipe adds the padding again.
 *
 * title: Add the Legacy Extra Banner Padding in Memberlite 8.0
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
 * Add a body class on pages with the memberlite_banner_extra_padding custom field.
 *
 * @param array $classes Body classes.
 * @return array
 */
function my_memberlite_banner_extra_padding_class( $classes ) {
	if ( ! function_exists( 'memberlite_get_banner_post_id' ) ) {
		return $classes;
	}

	$post_id = memberlite_get_banner_post_id();
	if ( ! empty( $post_id ) && 'page' === get_post_type( $post_id ) && get_post_meta( $post_id, 'memberlite_banner_extra_padding', true ) ) {
		$classes[] = 'memberlite-banner-extra-padding';
	}

	return $classes;
}
add_filter( 'body_class', 'my_memberlite_banner_extra_padding_class' );

/**
 * Add the extra masthead padding styles.
 *
 * @return void
 */
function my_memberlite_banner_extra_padding_css() {
	wp_add_inline_style( 'memberlite_style', '.memberlite-banner-extra-padding .masthead .row > .columns { padding-top: var(--wp--preset--spacing--50); padding-bottom: var(--wp--preset--spacing--50); }' );
}
add_action( 'wp_enqueue_scripts', 'my_memberlite_banner_extra_padding_css', 20 );
