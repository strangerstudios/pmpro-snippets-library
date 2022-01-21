<?php
/**
 * Show a login/register link for non-members when using the [membership] shortcode
 *
 * Note this doesn't check for specific membership levels.
 * 
 * title: Show a login/register link for non-members when using the [membership] shortcode
 * layout: snippet
 * collection: misc
 * category: login
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function show_login_register_links_on_member_pages($content) {

    //bail if PMPro isn't loaded
    if(!function_exists('pmpro_has_membership_access'))
        return $content;

    //does this post use the membership shortcode?
    if( has_shortcode( $content, "membership" ) && !pmpro_hasMembershipLevel() && pmpro_has_membership_access() ) {

        $pmpro_content_message_pre = '<div class="pmpro_content_message">';
        $pmpro_content_message_post = '</div>';
        $sr_search = array( "!!levels!!", "!!referrer!!" );
        $sr_replace = array( "", urlencode( site_url( $_SERVER['REQUEST_URI'] ) ) );

        //get the correct message to show at the bottom
        if( is_feed() ) {

            $newcontent = apply_filters( "pmpro_rss_text_filter", stripslashes( pmpro_getOption( "rsstext" ) ) );
            $content .= $pmpro_content_message_pre . str_replace( $sr_search, $sr_replace, $newcontent ) . $pmpro_content_message_post;
        
        } else if( is_user_logged_in() ) {

            //not a member
            $newcontent = apply_filters( "pmpro_non_member_text_filter", stripslashes( pmpro_getOption( "nonmembertext" ) ) );
            $content .= $pmpro_content_message_pre . str_replace( $sr_search, $sr_replace, $newcontent ) . $pmpro_content_message_post;
        
        } else {

            //not logged in!
            $newcontent = apply_filters( "pmpro_not_logged_in_text_filter", stripslashes( pmpro_getOption( "notloggedintext" ) ) );
            $content .= $pmpro_content_message_pre . str_replace( $sr_search, $sr_replace, $newcontent ) . $pmpro_content_message_post;
        }

    }

    return $content;
}
add_filter( 'the_content', 'show_login_register_links_on_member_pages', 5 );