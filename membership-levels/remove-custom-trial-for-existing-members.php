<?php
/**
 * Remove custom trial for existing members (when existing member changes levels/renews)
 * Adjust the level ID on line 16 to match your needs.
 * Add this code to your WordPress site by following this guide - https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_level_adjustment( $level ) {

    // Bail if the user currently doesn't have a membership level.
    if ( ! pmpro_hasMembershipLevel() ) {
        return $level;
    }

    // If it's not the level with the trial amount, just bail.
    if ( $level->id != '9' ) {
        return $level;
    }


    $level->trial_limit = '0';
    $level->trial_amount = '0';

    return $level;
}
add_filter( 'pmpro_checkout_level', 'my_pmpro_level_adjustment', 10, 1 );
