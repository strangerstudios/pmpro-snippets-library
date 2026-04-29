<?php
/**
 * Turn embeddable links into clickable links (do not render oEmbeds) on the Member Directory.
 * Update line 19 with the field name you want to disable oEmbed for.
 * Update line 20 with the link display type you want to use.
 * Link types include "embedded" (default), "clickable_link", "clickable_label", or '' for plain text.
 *
 * title: Disable oEmbed on Member Directory and Profile Pages
 * layout: snippet
 * collection: pmpro-member-directory
 * category: directory, embeds, oembed, clickable, links
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_field_value_link_display_type( $link_display_type, $value, $field ) {
	if ( isset( $field->name ) && $field->name === 'youtube_video' ) {
		return 'clickable_link';
	}
	return $link_display_type;
}
add_filter( 'pmpro_field_value_link_display_type', 'my_pmpro_field_value_link_display_type', 10, 3 );
