<?php
/**
 * Register a custom font family in theme.json so it appears in the block editor font picker.
 *
 * title: Register a Custom Font Family in the Memberlite Block Editor
 * layout: snippet
 * collection: memberlite
 * category: design
 *
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_memberlite_google_fonts_wp_theme_json_data_theme( $theme_json ) {
	$new_font = array(
		'fontFamily' => '"Pacifico", cursive',
		'name'       => 'Pacifico',
		'slug'       => 'pacifico',
		'fontFace'   => array(
			array(
				'fontFamily'  => 'Pacifico',
				'fontStyle'   => 'normal',
				'fontWeight'  => '400',
				'fontDisplay' => 'swap',
				'src'         => array( get_stylesheet_directory_uri() . '/fonts/Pacifico/Pacifico-Regular.ttf' ),
			),
		),
	);

	$data = $theme_json->get_data();
	$existing_fonts = $data['settings']['typography']['fontFamilies']['theme'] ?? array();
	$data['settings']['typography']['fontFamilies']['theme'] = array_merge( $existing_fonts, array( $new_font ) );

	return $theme_json->update_with( $data );
}
add_filter( 'wp_theme_json_data_theme', 'my_memberlite_google_fonts_wp_theme_json_data_theme' );
