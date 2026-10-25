<?php
/**
 * Show a page's banner right column content below the banner description in Memberlite 8.0+.
 * Memberlite 7.2 stopped showing this column and 8.0 moved saved values to the
 * memberlite_banner_right custom field. This recipe shows that field again,
 * below the banner description instead of beside it.
 *
 * title: Show the Legacy Banner Right Column in Memberlite 8.0
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
 * Show the banner right column content below the banner description.
 *
 * @return void
 */
function my_memberlite_show_banner_right() {
	$post_id = memberlite_get_banner_post_id();
	if ( empty( $post_id ) || 'page' !== get_post_type( $post_id ) ) {
		return;
	}

	$content = get_post_meta( $post_id, 'memberlite_banner_right', true );
	if ( '' === trim( (string) $content ) ) {
		return;
	}

	echo '<div class="masthead-right">' . wp_kses_post( wpautop( do_shortcode( $content ) ) ) . '</div>';
}
add_action( 'memberlite_after_masthead_inner', 'my_memberlite_show_banner_right', 15 );
