<?php
/**
 * Migrate data from Sponsored Members to Group Accounts Add On.
 *
 * READ BEFORE YOU RUN:
 * These scripts make irreversable changes to your database. Back up your database before using,
 * and contact PMPro support if you have any questions BEFORE running each script.
 *
 * TO RUN THE SCRIPTS:
 * Navigate to the new Memberships > Sponsored Members Migration page in the WP Dashboard.
 * The scripts are designed to be run IN ORDER. If there is still data remaining after a script is
 * run, contact PMPro support and we will be able to help you look into why this may be.
 *
 * Some custom pricing setups may not be able to be replicated in the Group Accounts Add On.
 * This migration script tries to match your previous Sponsored Members setup as closely as possible.
 *
 * Use this migration script at your own risk.
 *
 * title: Migrate data from Sponsored Members to Group Accounts Add On
 * layout: snippet
 * collection: add-ons, pmpro-group-accounts
 * category: migrate, sponsored members, group accounts
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Creates a new settings page for migrating Sponsored Members data to Group Accounts.
 */
function pmprogroupacct_add_sponsored_members_migration_settings() {
	add_submenu_page(
		'pmpro-dashboard',
		__( 'Sponsored Members Migration', 'pmpro-group-accounts' ),
		__( 'Sponsored Members Migration', 'pmpro-group-accounts' ),
		'manage_options',
		'pmprogroupacct-sponsored-members-migration',
		'pmprogroupacct_sponsored_members_migration_page'
	);
}
add_action( 'admin_menu', 'pmprogroupacct_add_sponsored_members_migration_settings' );

/**
 * The Sponsored Members Migration settings page.
 */
function pmprogroupacct_sponsored_members_migration_page() {
	global $wpdb, $pmprosm_sponsored_account_levels;

	// Check if the user has the capability to manage options.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'pmpro-group-accounts' ) );
	}

	// Make sure that Group Accounts is enabled.
	if ( ! function_exists( 'pmprogroupacct_get_settings_for_level' ) ) {
		wp_die( __( 'Group Accounts Add On is not enabled.', 'pmpro-group-accounts' ) );
	}

	// Run migration steps.
	if ( isset( $_REQUEST['pmprosm_migrate_level_setup'] ) ) {
		// Check the nonce.
		check_admin_referer( 'pmprosm_migrate_level_setup', 'pmprosm_migrate_nonce' );

		// Migrate the Sponsored Members level setup.
		if ( empty( $pmprosm_sponsored_account_levels ) ) {
			wp_die( __( 'Cannot find $pmprosm_sponsored_account_levels global. Please enable Sponsored Members setup code to migrate.', 'pmpro-group-accounts' ) );
		}

		foreach ( $pmprosm_sponsored_account_levels as $level_id => $level_setup ) {
			// Check if the level has already been set up in Group Accounts.
			$group_account_settings = pmprogroupacct_get_settings_for_level( $level_id );
			if ( ! empty( $group_account_settings ) ) {
				continue;
			}

			// Create the group account settings for the level.
			$apply_to_initial = ( ! isset( $level_setup['apply_seat_cost_to_initial_payment'] ) || ! empty( $level_setup['apply_seat_cost_to_initial_payment'] ) );
			$apply_to_recurring = ( ! isset( $level_setup['apply_seat_cost_to_billing_amount'] ) || ! empty( $level_setup['apply_seat_cost_to_billing_amount'] ) );
			if ( $apply_to_initial && $apply_to_recurring ) {
				$price_application = 'both';
			} elseif ( $apply_to_initial ) {
				$price_application = 'initial';
			} elseif ( $apply_to_recurring ) {
				$price_application = 'recurring';
			} else {
				// This is an invalid state, but we'll handle it anyway.
				$price_application = 'both';
			}
			$group_account_settings = array(
				'child_level_ids' => array_map( 'intval', is_array( $level_setup['sponsored_level_id'] ) ? $level_setup['sponsored_level_id'] : array( $level_setup['sponsored_level_id'] ) ),
				'min_seats' => (int)( isset( $level_setup['min_seats'] ) ? $level_setup['min_seats'] : $level_setup['seats'] ),
				'max_seats' => (int)( isset( $level_setup['max_seats'] ) ? $level_setup['max_seats'] : $level_setup['seats'] ),
				'pricing_model_settings' => empty( $level_setup['seat_cost'] ) ? '0' : $level_setup['seat_cost'],
				'pricing_model' => ( empty( $level_setup['seat_cost'] ) ? 'none' : 'fixed' ),
				'price_application' => $price_application,
			);
			update_pmpro_membership_level_meta( $level_id, 'pmprogroupacct_settings', $group_account_settings );

		}
	} elseif ( isset( $_REQUEST['pmprosm_migrate_groups'] ) ) {
		// Check the nonce.
		check_admin_referer( 'pmprosm_migrate_groups', 'pmprosm_migrate_nonce' );

		// Migrate the Sponsored Members groups.
		$code_user_ids = get_option( 'pmpro_code_user_ids' );
		if ( empty( $code_user_ids ) ) {
			wp_die( __( 'No parent users found in Sponsored Members.', 'pmpro-group-accounts' ) );
		}

		foreach ( $code_user_ids as $code_id => $user_id ) {
			// Check if the parent user already has a group set up in Group Accounts.
			$group = PMProGroupAcct_Group::get_groups(
				array(
					'group_parent_user_id' => $user_id,
					'limit'   => 1,
				)
			);
			if ( ! empty( $group ) || empty( get_userdata( $user_id ) ) ) {
				continue;
			}

			// Get the parent level that the user has.
			$levels = pmpro_getMembershipLevelsForUser( $user_id );
			if ( empty( $levels ) ) {
				continue;
			}
			$parent_level = null;
			foreach ( $levels as $level ) {
				if ( ! empty( pmprogroupacct_get_settings_for_level( $level->id ) ) ) {
					$parent_level = $level;
					break;
				}
			}
			if ( empty( $parent_level ) ) {
				continue;
			}

			// Get the number of seats for the parent user.
			// This will be the "uses" column value for the Sponsored Members discount code.
			$seats = (int)$wpdb->get_var( "SELECT uses FROM $wpdb->pmpro_discount_codes WHERE id = '" . esc_sql( $code_id ) . "'" );
			if ( empty( $seats ) ) {
				continue;
			}

			// Create the group for the parent user.
			PMProGroupAcct_Group::create( $user_id, $parent_level->id, $seats );
		}
	} elseif ( isset( $_REQUEST['pmprosm_migrate_members'] ) ) {
		// Check the nonce.
		check_admin_referer( 'pmprosm_migrate_members', 'pmprosm_migrate_nonce' );

		// Migrate the Sponsored Members members.
		$code_user_ids = get_option( 'pmpro_code_user_ids' );
		if ( empty( $code_user_ids ) ) {
			wp_die( __( 'No parent users found in Sponsored Members.', 'pmpro-group-accounts' ) );
		}

		foreach ( $code_user_ids as $code_id => $user_id ) {
			// Check if the parent user already has a group set up in Group Accounts.
			$group = PMProGroupAcct_Group::get_groups(
				array(
					'group_parent_user_id' => $user_id,
					'limit'   => 1,
				)
			);
			if ( empty( $group ) ) {
				continue;
			}
			$group = reset( $group );

			// Get the levels that can be claimed with the group.
			$group_account_settings = pmprogroupacct_get_settings_for_level( $group->group_parent_level_id );
			if ( empty( $group_account_settings ) ) {
				continue;
			}
			$child_level_ids = $group_account_settings['child_level_ids'];

			// Get the children for the parent user.
			$children = $wpdb->get_col( "SELECT user_id FROM $wpdb->pmpro_discount_codes_uses WHERE code_id = '" . esc_sql( $code_id ) . "'" );
			if ( empty( $children ) ) {
				continue;
			}

			// Get the members for the group.
			$group_members = PMProGroupAcct_Group_Member::get_group_members(
				array(
					'group_id' => $group->id,
				)
			);

			// Get the members that have not yet been migrated to the group.
			$children_to_migrate_for_group = array_diff( array_map( 'intval', $children ), wp_list_pluck( $group_members, 'group_child_user_id' ) );
			foreach ( $children as $child_user_id ) {
				// Make sure we have a user.
				if ( empty( get_userdata( $child_user_id ) ) ) {
					continue;
				}

				// Get the level that the child user has.
				$levels = pmpro_getMembershipLevelsForUser( $child_user_id );
				if ( empty( $levels ) ) {
					continue;
				}
				$child_level = null;
				foreach ( $levels as $level ) {
					if ( in_array( (int)$level->id, $child_level_ids, true ) ) {
						$child_level = $level;
						break;
					}
				}
				if ( empty( $child_level ) ) {
					continue;
				}

				// Add the child user to the group.
				PMProGroupAcct_Group_Member::create( $child_user_id, $child_level->id, $group->id );
			}
		}
	} elseif ( isset( $_REQUEST['pmprosm_delete_data'] ) ) {
		// Check the nonce.
		check_admin_referer( 'pmprosm_delete_data', 'pmprosm_migrate_nonce' );

		// Delete the Sponsored Members data.
		$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key = 'pmprosm_seats'" );
		delete_option( 'pmpro_code_user_ids' );
	} elseif ( isset( $_REQUEST['pmprosm_delete_discount_codes'] ) ) {
		// Check the nonce.
		check_admin_referer( 'pmprosm_delete_discount_codes', 'pmprosm_migrate_nonce' );

		// Delete the Sponsored Members discount codes.
		// Get all the codes first.
		$discount_codes = $wpdb->get_col( "SELECT id FROM $wpdb->pmpro_discount_codes WHERE code REGEXP '^[S]{1}[A-Z0-9]{10}$'" );
		if ( ! empty( $discount_codes ) ) {
			$wpdb->query( "DELETE FROM $wpdb->pmpro_discount_codes_uses WHERE code_id IN (" . implode( ', ', $discount_codes ) . ")" );
			$wpdb->query( "DELETE FROM $wpdb->pmpro_discount_codes WHERE id IN (" . implode( ', ', $discount_codes ) . ")" );
		}
	}

	?>
	<div class="wrap">
		<h2><?php esc_html_e( 'Sponsored Members Migration', 'pmpro-group-accounts' ); ?></h2>
		<div>
			<h3><?php esc_html_e( 'Migrate Level Setup', 'pmpro-group-accounts' ); ?></h3>
			<p><?php esc_html_e( 'Migrate the level setup from Sponsored Members to Group Accounts.', 'pmpro-group-accounts' ); ?></p>
			<form method="post">
				<?php
				wp_nonce_field( 'pmprosm_migrate_level_setup', 'pmprosm_migrate_nonce' );
				// Get the level information and group account settings of all levels that have already been set up in Group Accounts.
				$group_account_levels = $wpdb->get_results(
					"SELECT lm.pmpro_membership_level_id, lm.meta_value, l.name
					FROM $wpdb->pmpro_membership_levelmeta lm
					LEFT JOIN $wpdb->pmpro_membership_levels l ON lm.pmpro_membership_level_id = l.id
					WHERE lm.meta_key = 'pmprogroupacct_settings'"
				);
				if ( ! empty( $group_account_levels ) ) {
					?>
					<p><strong><?php esc_html_e( 'The following levels have already been set up in Group Accounts:', 'pmpro-group-accounts' ); ?></strong></p>
					<ul>
						<?php
						foreach ( $group_account_levels as $group_account_level ) {
							?>
							<h4><?php echo esc_html( $group_account_level->name ); ?></h4>
							<?php
							foreach ( maybe_unserialize( $group_account_level->meta_value ) as $key => $value ) {
								?>
								<li><?php echo esc_html( $key ); ?>: <?php echo ( is_array( $value ) ? implode( ', ', $value ) : $value ); ?></li>
								<?php
							}
						}
						?>
					</ul>
					<?php
				} else {
					?>
					<p><strong><?php esc_html_e( 'No levels have been set up in Group Accounts yet.', 'pmpro-group-accounts' ); ?></strong></p>
					<?php
				}

				// Get the level information and group account settings of all levels that have been set up in Sponsored Members.
				if ( empty( $pmprosm_sponsored_account_levels ) ) {
					?>
					<p><strong><?php esc_html_e( 'Cannot find $pmprosm_sponsored_account_levels global. Please enable Sponsored Members setup code to migrate.', 'pmpro-group-accounts' ); ?></strong></p>
					<?php
				} else {
					// Unset any levels that have already been set up in Group Accounts.
					foreach ( $group_account_levels as $group_account_level ) {
						unset( $pmprosm_sponsored_account_levels[ $group_account_level->pmpro_membership_level_id ] );
					}
					if ( empty( $pmprosm_sponsored_account_levels ) ) {
						?>
						<p><strong><?php esc_html_e( 'All levels have been migrated from Sponsored Members.', 'pmpro-group-accounts' ); ?></strong></p>
						<?php
					} else {
						?>
						<p><strong><?php esc_html_e( 'The following levels have not yet been migrated from Sponsored Members:', 'pmpro-group-accounts' ); ?></strong></p>
						<ul>
							<?php
							foreach ( $pmprosm_sponsored_account_levels as $level_id => $level_setup ) {
								// Get the level name for the key.
								$level = pmpro_getLevel( $level_id );
								$level_name = empty( $level ) ? '[' . sprintf( __( 'Deleted Level %d', 'pmpro_group_accounts' ), (int)$level_id ) . ']' : $level->name;
								?>
								<h4><?php echo esc_html( $level_name ); ?></h4>
								<li>child_level_ids: <?php echo esc_html( is_array( $level_setup['sponsored_level_id'] ) ? implode( ', ', $level_setup['sponsored_level_id'] ) : $level_setup['sponsored_level_id'] ); ?></li>
								<li>min_seats: <?php echo ( isset( $level_setup['min_seats'] ) ? $level_setup['min_seats'] : $level_setup['seats'] ); ?></li>
								<li>max_seats: <?php echo ( isset( $level_setup['max_seats'] ) ? $level_setup['max_seats'] : $level_setup['seats'] ); ?></li>
								<li>pricing_model: <?php echo ( empty( $level_setup['seat_cost'] ) ? 'none' : 'fixed' ); ?></li>
								<li>pricing_model_settings: <?php echo ( empty( $level_setup['seat_cost'] ) ? '0' : $level_setup['seat_cost'] ); ?></li>
								<li>price_application:
									<?php
									$apply_to_initial = ( ! isset( $level_setup['apply_seat_cost_to_initial_payment'] ) || ! empty( $level_setup['apply_seat_cost_to_initial_payment'] ) );
									$apply_to_recurring = ( ! isset( $level_setup['apply_seat_cost_to_billing_amount'] ) || ! empty( $level_setup['apply_seat_cost_to_billing_amount'] ) );
									if ( $apply_to_initial && $apply_to_recurring ) {
										echo esc_html( 'both' );
									} elseif ( $apply_to_initial ) {
										echo esc_html( 'initial' );
									} elseif ( $apply_to_recurring ) {
										echo esc_html( 'recurring' );
									} else {
										// This is an invalid state, but we'll handle it anyway.
										echo esc_html( 'both' );
									}
									?>
								</li>
								<?php
							}
							?>
						</ul>
						<?php
					}

				}
				?>
				<input type="submit" name="pmprosm_migrate_level_setup" class="button button-primary" value="<?php esc_attr_e( 'Migrate Level Setup', 'pmpro-group-accounts' ); ?>" <?php if ( empty( $pmprosm_sponsored_account_levels ) ) { echo 'disabled'; } ?>>
			</form>
		</div>

		<div>
			<h3><?php esc_html_e( 'Migrate Groups', 'pmpro-group-accounts' ); ?></h3>
			<p><?php esc_html_e( 'Migrate the groups from Sponsored Members to Group Accounts.', 'pmpro-group-accounts' ); ?></p>
			<form method="post">
				<?php
				wp_nonce_field( 'pmprosm_migrate_groups', 'pmprosm_migrate_nonce' );

				// Get all the parent users from Sponsored Members.
				$code_user_ids = get_option( 'pmpro_code_user_ids' );
				if ( empty( $code_user_ids ) ) {
					?>
					<p><strong><?php esc_html_e( 'No parent users found in Sponsored Members.', 'pmpro-group-accounts' ); ?></strong></p>
					<?php
				} else {
					// Remove any parent users that already have a group set up in Group Accounts.
					foreach ( $code_user_ids as $code_id => $user_id ) {
						$group = PMProGroupAcct_Group::get_groups(
							array(
								'group_parent_user_id' => $user_id,
								'limit'   => 1,
							)
						);
						if ( ! empty( $group ) ) {
							unset( $code_user_ids[ $code_id ] );
						}
					}
					if ( empty( $code_user_ids ) ) {
						?>
						<p><strong><?php esc_html_e( 'All parent users have been migrated to groups in Group Accounts.', 'pmpro-group-accounts' ); ?></strong></p>
						<?php
					} else {
						?>
						<p><strong><?php esc_html_e( 'The following parent users have not yet been migrated to groups in Group Accounts:', 'pmpro-group-accounts' ); ?></strong></p>
						<ul>
							<?php
							foreach ( $code_user_ids as $code_id => $user_id ) {
								$user = get_userdata( $user_id );
								?>
								<li><?php echo esc_html( empty( $user ) ? 'Deleted user #' . $user_id : $user->display_name ); ?></li>
								<?php
							}
							?>
						</ul>
						<?php
					}
				}
				?>
				<input type="submit" name="pmprosm_migrate_groups" class="button button-primary" value="<?php esc_attr_e( 'Migrate Groups', 'pmpro-group-accounts' ); ?>" <?php if ( empty( $code_user_ids ) ) { echo 'disabled'; } ?>>
			</form>
		</div>

		<div>
			<h3><?php esc_html_e( 'Migrate Members', 'pmpro-group-accounts' ); ?></h3>
			<p><?php esc_html_e( 'Migrate the members from Sponsored Members to Group Accounts.', 'pmpro-group-accounts' ); ?></p>
			<form method="post">
				<?php
				wp_nonce_field( 'pmprosm_migrate_members', 'pmprosm_migrate_nonce' );

				// Get all code user IDs again.
				$code_user_ids = get_option( 'pmpro_code_user_ids' );
				if ( empty( $code_user_ids ) ) {
					?>
					<p><strong><?php esc_html_e( 'No parent users found in Sponsored Members.', 'pmpro-group-accounts' ); ?></strong></p>
					<?php
				} else {
					// For each parent user, list the child users that have not yet been migrated to a group in Group Accounts.
					$children_to_migrate = array(); // $parent_user_id => $child_user_ids
					foreach ( $code_user_ids as $code_id => $user_id ) {
						$group = PMProGroupAcct_Group::get_groups(
							array(
								'group_parent_user_id' => $user_id,
								'limit'   => 1,
							)
						);
						if ( empty( $group ) ) {
							continue;
						}
						$group = reset( $group );

						$children = $wpdb->get_col( "SELECT user_id FROM $wpdb->pmpro_discount_codes_uses WHERE code_id = '" . esc_sql( $code_id ) . "'" );
						if ( empty( $children ) ) {
							continue;
						}

						$group_members = PMProGroupAcct_Group_Member::get_group_members(
							array(
								'group_id' => $group->id,
							)
						);

						$children_to_migrate_for_group = array_diff( array_map( 'intval', $children ), wp_list_pluck( $group_members, 'group_child_user_id' ) );
						if ( ! empty( $children_to_migrate_for_group ) ) {
							$children_to_migrate[ $user_id ] = $children_to_migrate_for_group;
						}
					}

					if ( empty( $children_to_migrate ) ) {
						?>
						<p><strong><?php esc_html_e( 'All members have been migrated to groups in Group Accounts.', 'pmpro-group-accounts' ); ?></strong></p>
						<?php
					} else {
						?>
						<p><strong><?php esc_html_e( 'The following members have not yet been migrated to groups in Group Accounts:', 'pmpro-group-accounts' ); ?></strong></p>
						<ul>
							<?php
							foreach ( $children_to_migrate as $parent_user_id => $child_user_ids ) {
								$user = get_userdata( $parent_user_id );
								?>
								<h4><?php echo esc_html( empty( $user ) ? 'Deleted user #' . $user_id : $user->display_name ); ?></h4>
								<ul>
									<?php
									foreach ( $child_user_ids as $child_user_id ) {
										$user = get_userdata( $child_user_id );
										?>
										<li><?php echo esc_html( empty( $user ) ? 'Deleted user #' . $user_id : $user->display_name ); ?></li>
										<?php
									}
									?>
								</ul>
								<?php
							}
							?>
						</ul>
						<?php
					}
				}
				?>
				<input type="submit" name="pmprosm_migrate_members" class="button button-primary" value="<?php esc_attr_e( 'Migrate Members', 'pmpro-group-accounts' ); ?>" <?php if ( empty( $children_to_migrate ) ) { echo 'disabled'; } ?>>
			</form>
		</div>

		<div>
			<h3><?php esc_html_e( 'Delete Data', 'pmpro-group-accounts' ); ?></h3>
			<p><?php esc_html_e( 'Delete the Sponsored Members data.', 'pmpro-group-accounts' ); ?></p>
			<form method="post" onsubmit="return confirm('This will permanently delete data from your database. Ensure that you have a backup of your data before proceeding. Continue?');">
				<?php
				$seats_user_meta_exists = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->usermeta WHERE meta_key = 'pmprosm_seats'" );
				$pmpro_code_user_ids_option_exists = ! empty( get_option( 'pmpro_code_user_ids' ) );

				if ( ! $seats_user_meta_exists && ! $pmpro_code_user_ids_option_exists ) {
					?>
					<p><strong><?php esc_html_e( 'No Sponsored Members data found.', 'pmpro-group-accounts' ); ?></strong></p>
					<?php
				} else {
					?>
					<p><strong><?php esc_html_e( 'The following data will be deleted:', 'pmpro-group-accounts' ); ?></strong></p>
					<ul>
						<?php
						if ( $seats_user_meta_exists ) {
							?>
							<li><?php esc_html_e( 'User meta: pmprosm_seats', 'pmpro-group-accounts' ); ?></li>
							<?php
						}
						if ( $pmpro_code_user_ids_option_exists ) {
							?>
							<li><?php esc_html_e( 'WP Option: pmpro_code_user_ids', 'pmpro-group-accounts' ); ?></li>
							<?php
						}
						?>
					</ul>
					<?php
				}
				wp_nonce_field( 'pmprosm_delete_data', 'pmprosm_migrate_nonce' );
				?>
				<input type="submit" name="pmprosm_delete_data" class="button button-primary" value="<?php esc_attr_e( 'Delete Data', 'pmpro-group-accounts' ); ?>" <?php if ( ! $seats_user_meta_exists && ! $pmpro_code_user_ids_option_exists ) { echo 'disabled'; } ?>>
			</form>
		</div>

		<div>
			<h3><?php esc_html_e( 'Delete Discount Codes', 'pmpro-group-accounts' ); ?></h3>
			<p><?php esc_html_e( 'Delete the Sponsored Members discount codes.', 'pmpro-group-accounts' ); ?></p>
			<form method="post" onsubmit="return confirm('This will permanently delete discount code data. Ensure that you have a backup of your data before proceeding. Continue?');">
				<?php
				// Check that the code begins with upper case "S" and has 11 characters.
				$discount_codes = $wpdb->get_col( "SELECT code FROM $wpdb->pmpro_discount_codes WHERE code REGEXP '^[S]{1}[A-Z0-9]{10}$'" );
				if ( empty( $discount_codes ) ) {
					?>
					<p><strong><?php esc_html_e( 'No Sponsored Members discount codes found.', 'pmpro-group-accounts' ); ?></strong></p>
					<?php
				} else {
					?>
					<p><strong><?php esc_html_e( 'The following discount codes will be deleted:', 'pmpro-group-accounts' ); ?></strong></p>
					<ul>
						<?php
						foreach ( $discount_codes as $discount_code ) {
							?>
							<li><?php echo esc_html( $discount_code ); ?></li>
							<?php
						}
						?>
					</ul>
					<?php
				}
				wp_nonce_field( 'pmprosm_delete_discount_codes', 'pmprosm_migrate_nonce' );
				?>
				<input type="submit" name="pmprosm_delete_discount_codes" class="button button-primary" value="<?php esc_attr_e( 'Delete Discount Codes', 'pmpro-group-accounts' ); ?>" <?php if ( empty( $discount_codes ) ) { echo 'disabled'; } ?>>
			</form>
	</div>
	<?php
}
