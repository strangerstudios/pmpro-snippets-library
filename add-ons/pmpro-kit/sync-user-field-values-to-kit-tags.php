<?php
/**
 * Connect PMPro User Fields to Kit Tags.
 *
 * For each configured PMPro User Field, reads the subscriber's field
 * value and assigns the corresponding Kit Tag.
 *
 * Update my_pmpro_kit_user_field_tag_maps() with each field name
 * you want to map and its possible values paired with Kit Tag IDs.
 *
 * title: Connect PMPro User Fields to Kit Tags
 * layout: snippet
 * collection: add-ons
 * category: kit, user-fields
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Define the field-to-tag mappings.
 *
 * Each top-level key is a PMPro User Field name.
 * Each nested array maps that field's possible values to their Kit Tag IDs.
 *
 * @return array
 */
function my_pmpro_kit_user_field_tag_maps() {
	$field_tag_maps = array(
		// "Company Size" user field.
		'company' => array(
			'solo'       => '11111111',
			'small'      => '22222222',
			'medium'     => '33333333',
			'enterprise' => '44444444',
		),
		// Add additional field mappings below, e.g.:
		// 'industry' => array(
		//     'tech'      => '5555555',
		//     'finance'   => '6666666',
		//     'nonprofit' => '7777777',
		// ),
	);

	return $field_tag_maps;
}

/**
 * Filter the list of tag IDs to assign to the subscriber.
 *
 * Iterates over every configured field and assigns the tag that
 * matches the subscriber's current value for that field.
 *
 * @param array   $new_tag_ids The list of tag IDs to assign.
 * @param WP_User $user        The WordPress user object.
 * @return array
 */
function my_pmpro_kit_user_field_subscriber_tag_ids( $new_tag_ids, $user ) {
	// If we don't have a valid user, bail.
	if ( empty( $user ) || empty( $user->ID ) ) {
		return $new_tag_ids;
	}

	foreach ( my_pmpro_kit_user_field_tag_maps() as $field_slug => $tag_map ) {
		$field_value = get_user_meta( $user->ID, $field_slug, true );

		if ( ! empty( $field_value ) && isset( $tag_map[ $field_value ] ) ) {
			$new_tag_ids[] = $tag_map[ $field_value ];
		}
	}

	return $new_tag_ids;
}
add_filter( 'pmprokit_subscriber_tag_ids', 'my_pmpro_kit_user_field_subscriber_tag_ids', 10, 2 );

/**
 * Filter the list of tag IDs that PMPro controls.
 *
 * @param array $controlled_tag_ids The list of tag IDs that PMPro controls.
 * @return array
 */
function my_pmpro_kit_user_field_controlled_tag_ids( $controlled_tag_ids ) {
	foreach ( my_pmpro_kit_user_field_tag_maps() as $tag_map ) {
		$controlled_tag_ids = array_merge( $controlled_tag_ids, array_values( $tag_map ) );
	}

	return $controlled_tag_ids;
}
add_filter( 'pmprokit_controlled_tag_ids', 'my_pmpro_kit_user_field_controlled_tag_ids' );
