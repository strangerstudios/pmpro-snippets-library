<?php
/**
 * Hide author avatars in Memberlite post loop views and on single post masthead view.
 *
 * title: Remove Memberlite Author Avatars
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

function my_memberlite_show_author_avatar( ) {
	return false;
}
add_filter( 'memberlite_show_author_avatar', 'my_memberlite_show_author_avatar' );