<?php

/**
 * Show the contents of the Terms of Service page at checkout.
 *
 * title: Show Terms of Service Page Contents at Checkout
 * layout: snippet
 * collection: checkout
 * category: registration-check
 * link: https://www.paidmembershipspro.com/show-todays-date-in-the-terms-of-service-box-at-membership-checkout/
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function pmpro_show_tos_at_checkout_with_page_contents() {
	global $pmpro_review;

	// Unhook pmpro_show_tos_at_checkout().
	remove_action( 'pmpro_checkout_before_submit_button', 'pmpro_show_tos_at_checkout', 5 );

	// We are showing the TOS checkbox on the checkout page. Unhook pmpro_unhook_pmpro_show_tos_at_checkout().
	remove_action( 'pmpro_checkout_after_tos_fields', 'pmpro_unhook_pmpro_show_tos_at_checkout' );

	// If checkout is being reviewed, don't show the TOS checkbox.
	if ( $pmpro_review ) {
		do_action_deprecated( 'pmpro_checkout_after_tos_fields', array(), '3.2' );
		return;
	}

	// Check if we have a TOS page. If not, don't show the TOS checkbox.
	$tospage = get_option( "pmpro_tospage" );
	if ( $tospage ) {
		$tospage = get_post( $tospage );
	}
	if ( empty( $tospage ) ) {
		do_action_deprecated( 'pmpro_checkout_after_tos_fields', array(), '3.2' );
		return;
	}

	// Show the TOS checkbox.
	?>
	<fieldset id="pmpro_tos_fields" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_fieldset', 'pmpro_tos_fields' ) ); ?>">
		<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_card' ) ); ?>">
			<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_card_content' ) ); ?>">
				<legend class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_legend' ) ); ?>">
					<h2 class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_heading pmpro_font-large' ) ); ?>"><?php esc_html_e( 'Terms of Service', 'pmpro-snippets-library' ); ?></h2>
				</legend>
				<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_fields' ) ); ?>">
					<?php
						if ( isset( $_REQUEST['tos'] ) ) {
							$tos = intval( $_REQUEST['tos'] );
						} else {
							$tos = "";
						}

						/**
						 * Hook to run formatting filters before displaying the content of your "Terms of Service" page at checkout.
						 *
						 * @since 2.4.1
						 * @since 2.10.1 We escape the content BEFORE the filter, so it can be overridden.
						 *
						 * @param string $pmpro_tos_content The content of the post assigned as the Terms of Service page.
						 * @param string $tospage The post assigned as the Terms of Service page.
						 *
						 * @return string $pmpro_tos_content
						 */
						$pmpro_tos_content = apply_filters( 'pmpro_tos_content', wp_kses_post( do_shortcode( $tospage->post_content ) ), $tospage );

						echo $pmpro_tos_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
					<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_field pmpro_form_field-checkbox pmpro_form_field-required' ) ); ?>">
						<label class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_label pmpro_clickable', 'tos' ) ); ?>" for="tos">
							<input type="checkbox" name="tos" value="1" id="tos" <?php checked( 1, $tos ); ?> class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_input pmpro_form_input-checkbox pmpro_form_input-required', 'tos' ) ); ?>" />
							<?php
								$tos_label = sprintf( __( 'I agree to the <a href="%1$s" target="_blank">%2$s</a>', 'pmpro-snippets-library' ), esc_url( get_permalink( $tospage->ID ) ), esc_html( $tospage->post_title ) );
								/**
								 * Filter the Terms of Service field label.
								 *
								 * @since 3.1
								 *
								 * @param string $tos_label The field label.
								 * @param object $tospage The Terms of Service page object.
								 * @return string The filtered field label.
								 */
								$tos_label = apply_filters( 'pmpro_tos_field_label', $tos_label, $tospage );
								echo wp_kses_post( $tos_label );
							?>
						</label>
					</div> <!-- end pmpro_form_field-tos -->
					<?php
						/**
						 * Allow adding text or more checkboxes after the Tos checkbox
						 * This is NOT intended to support multiple Tos checkboxes
						 *
						 * @since 2.8
						 */
						do_action_deprecated( 'pmpro_checkout_after_tos', array(), '3.2' );
					?>
				</div> <!-- end pmpro_form_fields -->
			</div> <!-- end pmpro_card_content -->
		</div> <!-- end pmpro_card -->
	</fieldset> <!-- end pmpro_tos_fields -->
	<?php
	do_action_deprecated( 'pmpro_checkout_after_tos_fields', array(), '3.2' );

}
add_action( 'pmpro_checkout_before_submit_button', 'pmpro_show_tos_at_checkout_with_page_contents', 4 ); // 4 to run before pmpro_show_tos_at_checkout().