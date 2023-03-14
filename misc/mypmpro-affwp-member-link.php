<?php
/**
 * Add an Affiliate Area link to the bottom of the member links section
 * on the member's account page.
 *
 * title: Add an Affiliate Area link to the bottom of the member links section on the member's account page.
 * layout: snippet
 * collection: misc
 * category: Affiliate WP
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_affiliate_member_link() {
	
	//Change the /affiliate-area/ slug to the URL of your own Affiliate Area
	if ( function_exists( 'affwp_is_affiliate') && affwp_is_affiliate() ) { ?>
		<li><a href="/affiliate-area/"><i class="fa-regular fa-user"></i> Affiliate Area</a></li>
		<?php
		}
	?>
	<?php
}
add_filter( 'pmpro_member_links_bottom','my_pmpro_affiliate_member_link' );