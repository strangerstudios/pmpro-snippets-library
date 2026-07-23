<?php
/**
 * Example of using the Memberlite Row and Columns shortcode.
 * Learn more at https://www.paidmembershipspro.com/documentation/memberlite/memberlite-shortcodes/column-shortcodes/
 *
 * title: Example of the Memberlite Row and Columns Shortcode.
 * layout: snippet
 * collection: memberlite
 * category: shortcodes
 * link: https://www.paidmembershipspro.com/documentation/memberlite/memberlite-shortcodes/subpage-list/
 *
 */
?>

<!-- Show a list of subpages of the page with ID 255585 with an excerpt, sorted by title -->
[memberlite_subpagelist post_parent="255585" link="true" orderby="title" order="ASC" show="excerpt"]

<!-- Show the full content of all children of the current page, without linking -->
[memberlite_subpagelist link="false" show="content"]

<!-- Show a list of links to any page that has the meta_key 'featured' -->
[memberlite_subpagelist meta_key="featured" link="true" link_text="" show="none" post_parent="-1"]
