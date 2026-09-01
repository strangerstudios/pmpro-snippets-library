<?php
/**
 * Auto-subscribe members to their level's Mailchimp audience, with an
 * opt-out checkbox at checkout instead of the plugin's default opt-in.
 * 
 * title: Opt-out Checkbox for Mailchimp
 * layout: snippet
 * collection: pmpro-mailchimp
 * category: snippet
 * link: TBD
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
define( 'PMPROMC_OPTOUT_FIELD', 'pmpromc_opt_out' );
define( 'PMPROMC_OPTOUT_META_KEY', 'pmpromc_opt_out' );

function pmpromc_optout_get_level_lists( $level_id ) {
	$options = get_option( 'pmpromc_options' );
	if ( empty( $options[ 'level_' . $level_id . '_lists' ] ) ) {
		return array();
	}
	return (array) $options[ 'level_' . $level_id . '_lists' ];
}

/**
 * Drop any already-queued subscribe update for this user/audience so nothing
 * is sent to Mailchimp at all -- no contact record gets created or touched.
 *
 * @param int   $user_id The user ID.
 * @param array $lists   The audience IDs to remove from the queue.
 */
function pmpromc_optout_remove_from_queue( $user_id, $lists ) {
	global $pmpromc_audience_member_updates;
	if ( empty( $pmpromc_audience_member_updates ) ) {
		return;
	}
	foreach ( (array) $lists as $audience ) {
		unset( $pmpromc_audience_member_updates[ $audience ][ $user_id ] );
	}
}

// Show an opt-out checkbox at checkout when the selected level has a Mailchimp audience configured.
function pmpromc_optout_checkout_box() {
	global $pmpro_level;

	if ( empty( $pmpro_level ) || empty( pmpromc_optout_get_level_lists( $pmpro_level->id ) ) ) {
		return;
	}

	$checked = ! empty( $_REQUEST[ PMPROMC_OPTOUT_FIELD ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	?>
	<div class="pmpro_checkout-box" id="pmpromc_optout_box">
		<h3><?php esc_html_e( 'Email Updates', 'your-textdomain' ); ?></h3>
		<div class="pmpro_checkout-fields">
			<div class="pmpro_checkout-field pmpro_checkout-field-checkbox">
				<input type="checkbox" name="<?php echo esc_attr( PMPROMC_OPTOUT_FIELD ); ?>" id="<?php echo esc_attr( PMPROMC_OPTOUT_FIELD ); ?>" value="1" <?php checked( $checked ); ?> />
				<label for="<?php echo esc_attr( PMPROMC_OPTOUT_FIELD ); ?>">I do not want to receive email updates related to my membership.</label>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'pmpro_checkout_boxes', 'pmpromc_optout_checkout_box', 20 );

// Save the opt-out choice and reconcile the subscription right after checkout.
function pmpromc_optout_save_after_checkout( $user_id, $order ) {
	
	$lists = pmpromc_optout_get_level_lists( $order->membership_id );
	if ( empty( $lists ) ) {
		return;
	}

	$opted_out = ! empty( $_REQUEST[ PMPROMC_OPTOUT_FIELD ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	update_user_meta( $user_id, PMPROMC_OPTOUT_META_KEY, $opted_out ? 1 : 0 );

	if ( $opted_out ) {
		pmpromc_optout_remove_from_queue( $user_id, $lists );
	}
}
add_action( 'pmpro_after_checkout', 'pmpromc_optout_save_after_checkout', 20, 2 );

/**
 * Remove queued Mailchimp updates for opted-out users when membership level changes.
 *
 * @param int $level_id   The new membership level ID.
 * @param int $user_id    The user ID whose level is changing.
 */
function pmpromc_optout_reconcile_on_level_change( $level_id, $user_id ) {
	if ( empty( get_user_meta( $user_id, PMPROMC_OPTOUT_META_KEY, true ) ) ) {
		return;
	}

	$lists = pmpromc_optout_get_level_lists( $level_id );
	if ( ! empty( $lists ) ) {
		pmpromc_optout_remove_from_queue( $user_id, $lists );
	}
}
add_action( 'pmpro_after_change_membership_level', 'pmpromc_optout_reconcile_on_level_change', 20, 2 );

function pmpromc_optout_remove_reconcile_during_checkout() {
	remove_action( 'pmpro_after_change_membership_level', 'pmpromc_optout_reconcile_on_level_change', 20 );
}
add_action( 'pmpro_checkout_before_change_membership_level', 'pmpromc_optout_remove_reconcile_during_checkout' );
