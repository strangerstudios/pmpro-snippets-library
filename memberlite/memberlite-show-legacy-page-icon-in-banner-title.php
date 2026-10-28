<?php
/**
 * Show a page's icon before the page title in Memberlite 8.0+.
 * Memberlite 8.0 removed the page icon and "Show Icon in Banner Title" settings
 * and moved saved values to the memberlite_page_icon and memberlite_banner_icon
 * custom fields. This recipe shows the icon again when a page has both fields.
 *
 * title: Show the Legacy Page Icon in the Banner Title in Memberlite 8.0
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
 * Show the page icon before the page title.
 *
 * @param string $title_html The page title HTML.
 * @return string
 */
function my_memberlite_show_page_icon( $title_html ) {
	$post_id = memberlite_get_banner_post_id();
	if ( empty( $post_id ) || 'page' !== get_post_type( $post_id ) || ! get_post_meta( $post_id, 'memberlite_banner_icon', true ) ) {
		return $title_html;
	}

	$icon = get_post_meta( $post_id, 'memberlite_page_icon', true );
	if ( empty( $icon ) ) {
		return $title_html;
	}

	$icon_html = do_shortcode( '[fa icon="' . esc_attr( $icon ) . '"]' );

	return preg_replace_callback(
		'/<h1[^>]*>/',
		function ( $matches ) use ( $icon_html ) {
			return $matches[0] . $icon_html . ' ';
		},
		$title_html,
		1
	);
}
add_filter( 'memberlite_get_page_title', 'my_memberlite_show_page_icon' );
