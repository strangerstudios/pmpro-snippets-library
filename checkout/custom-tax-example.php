<?php
/**
 * Custom tax structure where all levels except level 1 have 7.25% tax if billing state is CA.
 *
 * title: Custom tax structure where all levels except level 1 have 7.25% tax if billing state is CA.
 * layout: snippet
 * collection: checkout
 * category: tax
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */


/*
    Custom Tax Example.
    - Requires PMPro 1.3.13 or higher.
    - Leave the tax fields blank in the payment settings.
    - Level 1 has no tax.
    - Other levels have 7.25% tax for CA customers only.
    - We update the price description to include the tax amount.
*/

//Adjust the tax
function my_pmpro_tax( $tax, $values, $order ) {

    //only applicable for levels > 1 - remove if this should apply to all levels
    if( $order->membership_id > 1 ) {
        if( trim( strtoupper( $order->billing->state ) ) == "CA" ) {
            $tax = round( (float)$values['price'] * 0.075, 2 );       
        }
    }

    return $tax;
}
add_filter( "pmpro_tax", "my_pmpro_tax", 10, 3 );

//Adjut the level cost text
function my_pmpro_level_cost_text( $cost, $level ) {

    //only applicable for levels > 1 - remove if this should apply to all levels
    if( $level->id > 1 ) {
        $cost .= " Customers in CA will be charged 7.25% tax.";
    }

    return $cost;
}
add_filter( "pmpro_level_cost_text", "my_pmpro_level_cost_text", 10, 2 );