<?php
/**
 * Disable content-only editing mode for unsynced patterns in the block editor.
 *
 * title: Disable Content-Only Editing Mode for Unsynced Patterns
 * layout: snippet
 * collection: misc
 * category: block editor, patterns
 * link: https://www.paidmembershipspro.com/disable-content-only-mode-unsynced-patterns/
 *
 * Starting in WordPress 7.0, blocks inserted from an unsynced pattern open in
 * content-only mode, where you can only edit text and images. This snippet lets
 * you edit every block in an inserted pattern, including its layout and styles.
 *
 * Synced patterns and template parts are not affected.
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_disable_content_only_for_unsynced_patterns( $settings ) {
	$settings['disableContentOnlyForUnsyncedPatterns'] = true;

	return $settings;
}
add_filter( 'block_editor_settings_all', 'my_disable_content_only_for_unsynced_patterns' );
