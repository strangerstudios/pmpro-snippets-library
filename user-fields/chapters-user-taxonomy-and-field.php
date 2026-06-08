<?php
/**
 * Register a "Chapters" user taxonomy (managed under Users > Chapters) and add a
 * "Chapter" select field to PMPro's user fields, with options built from the
 * taxonomy terms.
 *
 * title: Add a Chapters User Taxonomy and Member Field
 * layout: snippet
 * collection: user-fields
 * category: custom-fields
 * link: https://www.paidmembershipspro.com/add-user-taxonomy-field/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

defined( 'ABSPATH' ) || exit;

// Shared chapters taxonomy slug. Guarded so any of the related snippets can declare it.
if ( ! function_exists( 'my_pmpro_chapters_taxonomy' ) ) {
	function my_pmpro_chapters_taxonomy() {
		return 'chapters';
	}
}

// The PMPro user field name (and meta key) used to assign a member to a chapter.
if ( ! function_exists( 'my_pmpro_chapters_field_name' ) ) {
	function my_pmpro_chapters_field_name() {
		return 'member_chapter';
	}
}

// 1. Register the Chapters user taxonomy with PMPro.
function my_pmpro_register_chapters_taxonomy() {
	if ( ! function_exists( 'pmpro_add_user_taxonomy' ) ) {
		return;
	}

	// The slug is the sanitized singular name, so 'chapters' keeps the slug equal
	// to my_pmpro_chapters_taxonomy().
	pmpro_add_user_taxonomy( my_pmpro_chapters_taxonomy(), 'chapters' );
}
add_action( 'init', 'my_pmpro_register_chapters_taxonomy', 10 );

// 2. Add the "Chapter" select field, with options built from the taxonomy terms.
function my_pmpro_add_chapter_user_field() {
	if ( ! function_exists( 'pmpro_add_user_field' ) || ! class_exists( 'PMPro_Field' ) ) {
		return;
	}

	// Register the field group explicitly so it has a clear label.
	pmpro_add_field_group( 'Chapters', esc_html__( 'Chapters', 'pmpro-snippets-library' ) );

	// PMPro saves user-taxonomy fields by term ID, so the option keys are term IDs.
	$options = array( '' => esc_html__( '- Select a Chapter -', 'pmpro-snippets-library' ) );

	$terms = get_terms(
		array(
			'taxonomy'   => my_pmpro_chapters_taxonomy(),
			'hide_empty' => false,
		)
	);
	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$options[ $term->term_id ] = $term->name;
		}
	}

	// The taxonomy MUST be passed in the constructor - that is what makes PMPro
	// store the selection in the taxonomy rather than user meta. Setting it later
	// via the pmpro_add_user_field filter is too late and saves to user meta only.
	$field = new PMPro_Field(
		my_pmpro_chapters_field_name(),
		'select',
		array(
			'label'          => esc_html__( 'Chapter', 'pmpro-snippets-library' ),
			'options'        => $options,
			'taxonomy'       => my_pmpro_chapters_taxonomy(),
			'profile'        => true,
			'memberslistcsv' => true,
			'required'       => false,
		)
	);

	pmpro_add_user_field( 'Chapters', $field );
}
// Priority 20 so the taxonomy (registered at 10) exists before we read its terms.
add_action( 'init', 'my_pmpro_add_chapter_user_field', 20 );

// 3. Add a "Chapter" column to the PMPro Members List (Memberships > Members).

// A user's chapter name(s) as a single display string.
if ( ! function_exists( 'my_pmpro_get_user_chapter_names' ) ) {
	function my_pmpro_get_user_chapter_names( $user_id ) {
		$names = wp_get_object_terms( $user_id, my_pmpro_chapters_taxonomy(), array( 'fields' => 'names' ) );
		if ( is_wp_error( $names ) || empty( $names ) ) {
			return '';
		}

		return implode( ', ', $names );
	}
}

function my_pmpro_memberslist_chapter_column( $columns ) {
	$columns['chapter'] = esc_html__( 'Chapter', 'pmpro-snippets-library' );

	return $columns;
}
add_filter( 'pmpro_manage_memberslist_columns', 'my_pmpro_memberslist_chapter_column' );

function my_pmpro_memberslist_chapter_column_content( $column_name, $user_id, $item ) {
	if ( 'chapter' !== $column_name ) {
		return;
	}

	$names = my_pmpro_get_user_chapter_names( $user_id );
	echo $names ? esc_html( $names ) : '&#8212;';
}
add_action( 'pmpro_manage_memberslist_custom_column', 'my_pmpro_memberslist_chapter_column_content', 10, 3 );
