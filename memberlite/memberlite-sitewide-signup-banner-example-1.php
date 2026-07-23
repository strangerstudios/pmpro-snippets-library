<?php
/**
 * Display a Banner Encouraging Signups in Your Memberlite Site - Example 1
 * Learn more at https://www.paidmembershipspro.com/memberlite-sitewide-membership-signup-banners/
 *
 * title: Memberlite Sitewide Membership Signup Banner Example 1
 * layout: snippet
 * collection: memberlite
 * category: banner
 * link: https://www.paidmembershipspro.com/memberlite-sitewide-membership-signup-banners/
 *
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_memberlite_before_footer_upgrade_1() {
	global $post;
	$memberlite_banner_bottom = get_post_meta( $post->ID, '_memberlite_banner_bottom', true );
	if( ( ! is_user_logged_in() || pmpro_hasMembershipLevel( 1 ) ) && empty( $memberlite_banner_bottom ) ) {
		?>
		<div id="banner_bottom">
			<div class="row">
				<div class="medium-9 columns">
					<h1>Show Your Support for Our Community!</h1>
					<p class="text-2x">Your Membership helps fund non-profit projects and spread our message of hope.</p>
				</div>
				<div class="medium-3 columns">
					<?php echo do_shortcode('[memberlite_btn style="action,block" href="/membership-checkout/?level=2" text="Sign Up Now &mdash; $10" icon="heart"]'); ?>
				</div>
			</div>
		</div>
		<?php
	}
}
add_action( 'memberlite_before_footer', 'my_memberlite_before_footer_upgrade_1' );
