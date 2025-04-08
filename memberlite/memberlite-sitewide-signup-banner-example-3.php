<?php
/**
 * Display a Banner Encouraging Signups in Your Memberlite Site - Example 3
 * Learn more at https://www.paidmembershipspro.com/memberlite-sitewide-membership-signup-banners/
 *
 * title: Memberlite Sitewide Membership Signup Banner Example 3
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

function my_memberlite_before_footer_upgrade_3() {
	global $post;
	$memberlite_banner_bottom = get_post_meta( $post->ID, '_memberlite_banner_bottom', true );
	if ( ! is_user_logged_in() && empty( $memberlite_banner_bottom ) ) {
		?>
		<div class="banner text-center pmpro" style="background-color: var(--memberlite-color-borders);">
			<div class="row">
				<div class="medium-12 columns">
					<style>
						.pmpro_advanced_levels-div { margin: 0; }
					</style>
					<div class="pmpro_font-x-large"><strong>Choose the membership that's right for you.</strong></div>
					<?php echo do_shortcode('[pmpro_advanced_levels back_link="false" levels="1,2,3" expiration="false" layout="3col"]'); ?>
					<p>Already a Member? <a href="<?php echo wp_login_url( get_permalink() ); ?>" title="Login">Log in now &raquo;</a></p>
				</div>
			</div>
		</div>
		<?php
	}
}
add_action( 'memberlite_before_footer', 'my_memberlite_before_footer_upgrade_3' );
