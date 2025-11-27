<?php
/**
 * Edit the text 'Make a Gift' for PMPro Donations Add On
 * 
 * title: Change "Make a Gift" Text for PMPro Donations Add On
 * layout: snippet
 * collection: add-ons, pmpro-donations
 * category: text-change, customization
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_donations_change_text( $change_text, $text, $domain ) {
    switch ( $change_text ) {
        case 'Make a Gift' :
            $change_text = __( 'Your Replacement Text Here', 'pmpro-donations' ); //Replace the text in the brackets to adjust the "Make a Gift" wording shown.
            break;
    }
    return $change_text;
}
add_filter( 'gettext', 'my_pmpro_donations_change_text', 20, 3 );
