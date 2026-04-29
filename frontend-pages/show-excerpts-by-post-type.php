<?php
/**
 * Show PMPro excerpts only for the post types you specify.
 *
 * title: Allow PMPro Excerpts for Specific Post Types
 * layout: snippet
 * collection: frontend-pages
 * category: content-protection
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method:
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * To use this customization, be sure “Show Excerpts to Non-Members?”
 *  is set to “Yes, show excerpts” in Memberships → Settings → Advanced Settings → Content Settings.
 * Only show PMPro excerpts for the selected post types.
 * Add any post type slugs to the $allowed_post_types array.
 */
function my_pmpro_allow_excerpts_for_specific_post_types( $showexcerpts ) {
    global $post;

    if ( empty( $post ) ) {
        return $showexcerpts;
    }

    // Edit this list to control which post types show excerpts.
    $allowed_post_types = array(
        'movies', // Example of a custom post type slug called "movies".
        // 'post',
        // 'page',
        // 'custom_post_type_slug', // Add your custom post type slugs here.
    );

    // Return true only for the post types listed above.
    return in_array( $post->post_type, $allowed_post_types, true );
}
add_filter( 'option_pmpro_showexcerpts', 'my_pmpro_allow_excerpts_for_specific_post_types' );
