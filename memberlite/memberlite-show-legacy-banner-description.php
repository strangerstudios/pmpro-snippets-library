<?php
/**
 * Show a page's banner description below the page title in Memberlite 8.0+.
 * Memberlite 8.0 removed the banner description setting and moved saved values
 * to the memberlite_banner_desc custom field. This recipe shows that field again.
 *
 * title: Show the Legacy Banner Description in Memberlite 8.0
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
 * Show the banner description below the page title.
 *
 * @return void
 */
function my_memberlite_show_banner_description() {
	$post_id = memberlite_get_banner_post_id();
	if ( empty( $post_id ) || 'page' !== get_post_type( $post_id ) ) {
		return;
	}

	$description = get_post_meta( $post_id, 'memberlite_banner_desc', true );
	if ( '' === trim( (string) $description ) ) {
		return;
	}

	echo '<div class="masthead-description">' . wp_kses_post( wpautop( do_shortcode( $description ) ) ) . '</div>';
}
add_action( 'memberlite_after_masthead_inner', 'my_memberlite_show_banner_description' );
