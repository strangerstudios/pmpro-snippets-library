<?php
/**
 * Display a Banner Encouraging Signups in Your Memberlite Site - Example 2
 * Learn more at https://www.paidmembershipspro.com/memberlite-sitewide-membership-signup-banners/
 *
 * title: Memberlite Sitewide Membership Signup Banner Example 2
 * layout: snippet
 * collection: memberlite
 * category: banner
 *
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_memberlite_before_footer_upgrade_2()  {
	global $post;
	$memberlite_banner_bottom = get_post_meta( $post->ID, '_memberlite_banner_bottom', true );
	if ( ! is_user_logged_in() && empty( $memberlite_banner_bottom ) ) {
		?>
		<div id="banner_bottom" class="pmpro text-center">
			<div class="row">
				<div class="medium-10 medium-offset-1 columns">
					<div class="pmpro_font-x-large"><strong>Join Today to Access Our Exclusive Members-Only Content</strong></div>
					<div class="pmpro_spacer"></div>
					<div class="row">
						<div class="medium-6 columns">
							<p class="pmpro_font-large"><strong>Free Signup</strong> for New Members</p>
							<?php echo do_shortcode('[memberlite_btn style="action" href="/membership-checkout/?level=1" text="Sign Up Now" icon="heart"]'); ?>
						</div>
						<div class="medium-6 columns">
							<p class="pmpro_font-large">Already a Member?</p>
							<a class="btn btn_primary" href="<?php echo wp_login_url( get_permalink() ); ?>" title="Login">Log In Now</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
add_action( 'memberlite_before_footer', 'my_memberlite_before_footer_upgrade_2' );
