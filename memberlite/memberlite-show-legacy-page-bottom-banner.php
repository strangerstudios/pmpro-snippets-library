<?php
/**
 * Show a page's bottom banner above the footer in Memberlite 8.0+.
 * Memberlite 8.0 removed the page bottom banner setting and moved saved values
 * to the memberlite_banner_bottom custom field. This recipe shows that field again
 * and adds the banner styles Memberlite no longer includes.
 *
 * title: Show the Legacy Page Bottom Banner in Memberlite 8.0
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
 * Show the page bottom banner above the footer.
 *
 * @return void
 */
function my_memberlite_show_banner_bottom() {
	$post_id = memberlite_get_banner_post_id();
	if ( empty( $post_id ) || 'page' !== get_post_type( $post_id ) ) {
		return;
	}

	$content = get_post_meta( $post_id, 'memberlite_banner_bottom', true );
	if ( '' === trim( (string) $content ) ) {
		return;
	}
	?>
	<div id="banner_bottom">
		<div class="row"><div class="large-12 columns">
			<?php echo apply_filters( 'the_content', wp_kses_post( $content ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div></div>
	</div>
	<?php
}
add_action( 'memberlite_after_content', 'my_memberlite_show_banner_bottom' );

/**
 * Add styles for the page bottom banner.
 *
 * @return void
 */
function my_memberlite_banner_bottom_css() {
	wp_add_inline_style( 'memberlite_style', '
		#banner_bottom { background-color: var(--memberlite-color-secondary); color: var(--memberlite-color-white); padding: 4rem 0; }
		#banner_bottom p { font-size: var(--wp--preset--font-size--21); }
		#banner_bottom :is(h1, h2, h3, h4, h5) { color: var(--memberlite-color-white); margin: 0 0 1.6rem 0; }
		#banner_bottom :is(h1, h2, h3, h4, h5) a { color: var(--memberlite-color-white); text-decoration: none; border-bottom: 1px dotted var(--memberlite-color-white); }
		@media print { #banner_bottom { display: none; } }
	' );
}
add_action( 'wp_enqueue_scripts', 'my_memberlite_banner_bottom_css', 20 );
