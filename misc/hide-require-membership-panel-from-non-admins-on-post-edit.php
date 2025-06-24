<?php
/**
 * Hide the PMPro "Require Membership" panel in the WordPress Block Editor
 * sidebar from non-admins on the add/edit post screen.
 *
 * title: Hide the PMPro Require Membership panel from non-admins
 * layout: snippet
 * collection: misc
 * category: content
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_maybe_dequeue_sidebar_panel() {
	// Only run in block editor.
	if ( ! is_admin() || ! function_exists( 'get_current_screen' ) ) {
		return;
	}

	$screen = get_current_screen();

	if ( empty( $screen ) || 'post' !== $screen->base ) {
		return;
	}

	// If not an administrator, dequeue the sidebar panel script.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_dequeue_script( 'pmpro-sidebar-editor-script' );
		wp_deregister_script( 'pmpro-sidebar-editor-script' );
	}
}
add_action( 'enqueue_block_editor_assets', 'my_pmpro_maybe_dequeue_sidebar_panel', 20 );
