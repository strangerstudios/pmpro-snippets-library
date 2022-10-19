<?php
/** 
 * Sell a la carte access to additional categories at checkout.
 *
 * title: Sell a la carte access to additional categories at checkout.
 * layout: snippet
 * collection: checkout
 * category: fields, restrictions, categories
 *
 * Require specific category user meta to view posts in designated categories.
 * Custom price-adjusting fields are added via user fields.
 * The initial payment and billing amount is adjusted based on cat selections.
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_additional_categories( $hasaccess, $thepost, $theuser, $post_membership_levels ) {
	global $wpdb;

	// If PMPro says false already, return false.
	if( ! $hasaccess ) {
		return false;
	}

	// If the post is in the cat and the user has not purchased, then block.
	if( has_category( 'cooking', $thepost ) ) {
		$cooking = get_user_meta( $theuser->ID, 'cooking', true );
		if( empty( $cooking ) ) {
			$hasaccess = false;
		}
	}

	if( has_category( 'baking', $thepost ) ) {
		$baking = get_user_meta( $theuser->ID, 'baking', true );
		if( empty( $baking ) ) {
			$hasaccess = false;
		}
	}

	if( has_category( 'crafts', $thepost ) ) {
		$crafts = get_user_meta( $theuser->ID, 'crafts', true );
		if( empty( $crafts ) ) {
			$hasaccess = false;
		}
	}

	return $hasaccess;
}
add_filter( 'pmpro_has_membership_access_filter', 'my_pmpro_additional_categories', 10, 4);

/**
 * Add checkboxes to checkout for our cats.
 * Alternatively, you could add these fields via the user fields settings.
 * Make sure that the field name (meta key) matches the custom code.
 */
function my_add_cat_checkboxes() {
    //don't break if Register Helper is not loaded
    if( ! function_exists( 'pmpro_add_user_field' ) ) {
        return false;
    }

	// Define our fields.
    $fields[] = new PMPro_Field(
        'cooking',        // input name, will also be used as meta key
        'checkbox',       // type of field
        array(
            'text' => 'Cooking Category ($10)',
            'memberslistcsv' => true,
            'profile' => 'admin_only',
            'showmainlabel' => false,
        ));
    $fields[] = new PMPro_Field(
        'baking',         // input name, will also be used as meta key
        'checkbox',       // type of field
        array(
            'text' => 'Baking Category ($10)',
            'memberslistcsv' => true,
            'profile' => 'admin_only',
            'showmainlabel' => false,
        ));
    $fields[] = new PMPro_Field(
        'crafts',         // input name, will also be used as meta key
        'checkbox',       // type of field
        array(
            'text' => 'Crafts Category ($10)',
            'memberslistcsv' => true,
            'profile' => 'admin_only',
            'showmainlabel' => false,
        ));

	// Add our field group and add fields to it.
    pmpro_add_field_group ( 'additional_categories', 'Purchase Additional Access' );
    foreach( $fields as $field ) {
		pmpro_add_user_field(
            'additional_categories', // location on checkout page
            $field            // PMProRH_Field object
        );
	}
}
add_action( 'init', 'my_add_cat_checkboxes' );

/**
 * If a user checked options, then adjust the price.
 */
function my_adjust_level_price_for_cats( $level ) {
	if( ! empty( $_REQUEST['cooking'] ) ) {
		$level->initial_payment = $level->initial_payment + 10;
		$level->billing_amount = $level->billing_amount + 10;
	}

	if( ! empty( $_REQUEST['baking'] ) ) {
		$level->initial_payment = $level->initial_payment + 10;
		$level->billing_amount = $level->billing_amount + 10;
	}

	if( ! empty( $_REQUEST['crafts'] ) ) {
		$level->initial_payment = $level->initial_payment + 10;
		$level->billing_amount = $level->billing_amount + 10;
	}

	return $level;
}
add_filter( 'pmpro_checkout_level', 'my_adjust_level_price_for_cats' );
