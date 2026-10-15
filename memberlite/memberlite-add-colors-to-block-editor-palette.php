<?php
/**
 * Add custom colors to the Block Editor color palette.
 *
 * title: Add Custom Colors to the Block Editor Color Palette
 * layout: snippet
 * collection: memberlite
 * category: design
 * link: https://www.paidmembershipspro.com/add-custom-colors-block-editor-palette/
 *
 * Edit the $new_colors array below to add your own colors. Each color needs a
 * name (shown in the editor), a unique slug, and a hex color value.
 *
 * Each color is available in the block editor color picker and as a CSS custom
 * property, e.g. var(--wp--preset--color--my-dark-blue).
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_memberlite_add_colors_to_palette( $theme_json ) {
	$new_colors = array(
		array(
			'name'  => 'My Dark Blue',
			'slug'  => 'my-dark-blue',
			'color' => '#0c3d54',
		),
		array(
			'name'  => 'My Bright Blue',
			'slug'  => 'my-bright-blue',
			'color' => '#20d7e8',
		),
	);

	$data = $theme_json->get_data();
	$existing_colors = $data['settings']['color']['palette']['theme'] ?? array();
	$data['settings']['color']['palette']['theme'] = array_merge( $existing_colors, $new_colors );

	return $theme_json->update_with( $data );
}
add_filter( 'wp_theme_json_data_theme', 'my_memberlite_add_colors_to_palette', 20 );
