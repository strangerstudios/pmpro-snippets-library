<?php
/**
 * Migrate ProfilePress avatars to PMPro avatars. 
 * This snippet will copy the avatar URL from the ProfilePress user meta field to the PMPro user meta field for all users.
 * 
 * title: Migrate ProfilePress Avatars to PMPro Avatars
 * layout: snippet
 * collection: misc
 * category: migration
 * link: https://www.paidmembershipspro.com/link-to-post-if-available/ OR TBD
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Avatar sources to migrate from, checked in order for each user.
 *
 * Each source is:
 *   'meta_key' => user meta key used to discover candidate users.
 *   'resolve'  => callable( int $user_id ): string|false — absolute path to the
 *                 user's source image, or false if none.
 *   'cleanup'  => callable( int $user_id ): void — delete the source file(s)
 *                 and meta for a successfully migrated user. Optional.
 *
 * @return array[]
 */
function pmpro_avatar_migration_get_sources() {
	global $wpdb;

	$sources = array(
		// ProfilePress: filename stored in user meta, file in uploads/pp-avatar.
		'profilepress'   => array(
			'meta_key' => 'pp_profile_avatar',
			'resolve'  => function ( $user_id ) {
				$filename = get_user_meta( $user_id, 'pp_profile_avatar', true );
				if ( empty( $filename ) || ! is_string( $filename ) ) {
					return false;
				}
				$dir = defined( 'PPRESS_AVATAR_UPLOAD_DIR' ) ? PPRESS_AVATAR_UPLOAD_DIR : WP_CONTENT_DIR . '/uploads/pp-avatar/';
				return trailingslashit( $dir ) . $filename;
			},
			'cleanup'  => function ( $user_id ) {
				$filename = get_user_meta( $user_id, 'pp_profile_avatar', true );
				if ( ! empty( $filename ) && is_string( $filename ) ) {
					$dir  = defined( 'PPRESS_AVATAR_UPLOAD_DIR' ) ? PPRESS_AVATAR_UPLOAD_DIR : WP_CONTENT_DIR . '/uploads/pp-avatar/';
					$path = trailingslashit( $dir ) . $filename;
					if ( file_exists( $path ) ) {
						@unlink( $path );
					}
				}
				delete_user_meta( $user_id, 'pp_profile_avatar' );
			},
		),
		// WP User Avatar (and imports from it): attachment ID stored in
		// {prefix}user_avatar user meta.
		'wp_user_avatar' => array(
			'meta_key' => $wpdb->get_blog_prefix() . 'user_avatar',
			'resolve'  => function ( $user_id ) use ( $wpdb ) {
				$attachment_id = (int) get_user_meta( $user_id, $wpdb->get_blog_prefix() . 'user_avatar', true );
				if ( ! $attachment_id || ! wp_attachment_is_image( $attachment_id ) ) {
					return false;
				}
				return get_attached_file( $attachment_id );
			},
			'cleanup'  => function ( $user_id ) use ( $wpdb ) {
				$meta_key      = $wpdb->get_blog_prefix() . 'user_avatar';
				$attachment_id = (int) get_user_meta( $user_id, $meta_key, true );
				if ( $attachment_id ) {
					// Removes the attachment post, file, and resized variants.
					wp_delete_attachment( $attachment_id, true );
				}
				delete_user_meta( $user_id, $meta_key );
			},
		),
	);

	return $sources;
}

// ─── Discovery ────────────────────────────────────────────────────────────────

/**
 * WP_User_Query args matching users who still need migration: they have meta
 * from at least one source, no PMPro avatar, and no error/skip marker.
 *
 * @param int $number Max users to return.
 * @return array
 */
function pmpro_avatar_migration_candidate_args( $number ) {
	$source_metas = array( 'relation' => 'OR' );
	foreach ( pmpro_avatar_migration_get_sources() as $source ) {
		$source_metas[] = array(
			'key'     => $source['meta_key'],
			'compare' => 'EXISTS',
		);
	}

	return array(
		'fields'     => 'ID',
		'number'     => $number,
		'orderby'    => 'ID',
		'order'      => 'ASC',
		'meta_query' => array(
			'relation' => 'AND',
			$source_metas,
			array( 'key' => 'pmpro_avatar', 'compare' => 'NOT EXISTS' ),
			array( 'key' => 'pmpro_avatar_migration_error', 'compare' => 'NOT EXISTS' ),
		),
	);
}

/**
 * Count users who still need migration.
 *
 * @return int
 */
function pmpro_avatar_migration_count_candidates() {
	$query = new WP_User_Query( array_merge(
		pmpro_avatar_migration_candidate_args( 1 ),
		array( 'count_total' => true )
	) );
	return (int) $query->get_total();
}

// ─── Queueing ─────────────────────────────────────────────────────────────────

/**
 * Start (or restart) the migration: clear error markers so those users are
 * retried, reset the progress counters, and schedule the first batch task.
 * Each batch task schedules the next one until no users remain.
 *
 * @return int|WP_Error Number of users to be migrated.
 */
function pmpro_avatar_migration_queue_all() {
	if ( ! class_exists( 'PMPro_Action_Scheduler' ) ) {
		return new WP_Error( 'missing_deps', 'PMPro_Action_Scheduler is not available. Update Paid Memberships Pro.' );
	}

	// Clear error/skip markers so those users are retried on this run.
	$errored = get_users( array(
		'meta_key'     => 'pmpro_avatar_migration_error',
		'meta_compare' => 'EXISTS',
		'fields'       => 'ID',
		'number'       => -1,
	) );
	foreach ( $errored as $user_id ) {
		delete_user_meta( $user_id, 'pmpro_avatar_migration_error' );
	}

	$total = pmpro_avatar_migration_count_candidates();

	// Reset progress counters for this run.
	update_option( 'pmpro_avatar_migration_stats', array(
		'total'     => $total,
		'done'      => 0,
		'skipped'   => 0,
		'errors'    => 0,
		'queued_at' => time(),
	), false );

	if ( $total > 0 ) {
		PMPro_Action_Scheduler::instance()->maybe_add_task(
			'pmpro_avatar_migration_batch',
			array( 'batch' => 1 ),
			'pmpro_avatar_migration'
		);
	}

	return $total;
}

/**
 * Count Action Scheduler tasks still pending for this migration.
 *
 * @return int
 */
function pmpro_avatar_migration_pending_count() {
	if ( ! class_exists( 'ActionScheduler_Store' ) ) {
		return 0;
	}
	return count( as_get_scheduled_actions( array(
		'group'    => 'pmpro_avatar_migration',
		'status'   => ActionScheduler_Store::STATUS_PENDING,
		'per_page' => -1,
	), 'ids' ) );
}

// ─── Batch task ───────────────────────────────────────────────────────────────

add_action( 'pmpro_avatar_migration_batch', 'pmpro_avatar_migration_process_batch' );

/**
 * Action Scheduler callback: process one batch of users, then schedule the
 * next batch. The next batch is scheduled BEFORE processing so a fatal error
 * (e.g. out of memory on a huge image) cannot break the chain.
 *
 * @param array $args Contains 'batch' (1-based batch number).
 */
function pmpro_avatar_migration_process_batch( $args ) {
	$batch_size = 20;

	$user_ids = get_users( pmpro_avatar_migration_candidate_args( $batch_size ) );
	if ( empty( $user_ids ) ) {
		return; // All done — end the chain.
	}

	$batch = isset( $args['batch'] ) ? (int) $args['batch'] : 1;
	PMPro_Action_Scheduler::instance()->maybe_add_task(
		'pmpro_avatar_migration_batch',
		array( 'batch' => $batch + 1 ),
		'pmpro_avatar_migration'
	);

	foreach ( $user_ids as $user_id ) {
		$user_id = (int) $user_id;

		// Mark the user before processing so a fatal error can't leave them
		// matching the candidate query forever; cleared or overwritten below.
		update_user_meta( $user_id, 'pmpro_avatar_migration_error', 'Processing did not complete. Re-queue to retry.' );

		// First source that resolves to an existing file wins.
		$source_path = false;
		foreach ( pmpro_avatar_migration_get_sources() as $source ) {
			$path = call_user_func( $source['resolve'], $user_id );
			if ( $path && file_exists( $path ) ) {
				$source_path = $path;
				break;
			}
		}

		if ( ! $source_path ) {
			update_user_meta( $user_id, 'pmpro_avatar_migration_error', 'No source avatar file found.' );
			pmpro_avatar_migration_increment( 'skipped' );
			continue;
		}

		$result = pmpro_avatar_migration_copy_and_process( $user_id, $source_path );

		if ( is_wp_error( $result ) ) {
			update_user_meta( $user_id, 'pmpro_avatar_migration_error', $result->get_error_message() );
			pmpro_avatar_migration_increment( 'errors' );
			PMPro_Action_Scheduler::add_task_log(
				'pmpro_avatar_migration_batch',
				'error',
				"Avatar migration failed for user {$user_id}: " . $result->get_error_message()
			);
			continue;
		}

		delete_user_meta( $user_id, 'pmpro_avatar_migration_error' );
		pmpro_avatar_migration_increment( 'done' );
	}
}

/**
 * Copy the source image into PMPro's avatar directory, crop to a square,
 * generate bucket sizes, and write the pmpro_avatar user meta.
 *
 * Mirrors the processing in pmpro_avatar_process_upload() but works from a
 * filesystem path rather than $_FILES.
 *
 * @param int    $user_id
 * @param string $source_path Absolute path to the source image.
 * @return true|WP_Error
 */
function pmpro_avatar_migration_copy_and_process( $user_id, $source_path ) {
	if ( ! function_exists( 'pmpro_avatar_get_upload_dir' ) ) {
		return new WP_Error( 'pmpro_missing', 'PMPro avatar functions are not available. Update Paid Memberships Pro.' );
	}

	$filetype = wp_check_filetype( $source_path );
	$orig_ext = ! empty( $filetype['ext'] ) ? $filetype['ext'] : pathinfo( $source_path, PATHINFO_EXTENSION );
	$save_ext = pmpro_avatar_get_save_extension( $orig_ext );

	if ( ! $save_ext ) {
		return new WP_Error( 'bad_extension', "Unsupported image extension '{$orig_ext}'." );
	}

	pmpro_avatar_setup_directory();
	$user_dir = pmpro_avatar_get_upload_dir( $user_id );
	if ( ! file_exists( $user_dir ) ) {
		wp_mkdir_p( $user_dir );
	}

	$image = wp_get_image_editor( $source_path );
	if ( is_wp_error( $image ) ) {
		return new WP_Error( 'editor_failed', 'Could not open image: ' . $image->get_error_message() );
	}

	// Crop to a square from the center.
	$size    = $image->get_size();
	$min_dim = min( $size['width'], $size['height'] );
	$cropped = $image->crop( ( $size['width'] - $min_dim ) / 2, ( $size['height'] - $min_dim ) / 2, $min_dim, $min_dim );
	if ( is_wp_error( $cropped ) ) {
		return new WP_Error( 'crop_failed', 'Could not crop image: ' . $cropped->get_error_message() );
	}

	$max_dim = pmpro_avatar_get_max_dimension();
	if ( $min_dim > $max_dim ) {
		$image->resize( $max_dim, $max_dim, true );
	}

	$image->set_quality( 90 );

	// Save to a temp file first so any existing avatar is preserved on failure.
	$saved = $image->save( $user_dir . 'avatar-tmp.' . $save_ext );
	if ( is_wp_error( $saved ) ) {
		return new WP_Error( 'save_failed', 'Could not save processed image: ' . $saved->get_error_message() );
	}

	// Trust the extension the editor actually saved with.
	$save_ext  = pathinfo( $saved['path'], PATHINFO_EXTENSION );
	$base_path = $user_dir . 'avatar.' . $save_ext;

	if ( ! rename( $saved['path'], $base_path ) ) {
		@unlink( $saved['path'] );
		return new WP_Error( 'rename_failed', 'Could not move processed image into place.' );
	}

	// Remove old sized variants or base files with a different extension.
	foreach ( (array) glob( $user_dir . 'avatar*' ) as $old_file ) {
		if ( is_file( $old_file ) && $old_file !== $base_path ) {
			@unlink( $old_file );
		}
	}

	// Generate bucket sizes smaller than the base image.
	$base_image = wp_get_image_editor( $base_path );
	if ( ! is_wp_error( $base_image ) ) {
		$base_size = $base_image->get_size();
		foreach ( pmpro_avatar_get_bucket_sizes() as $bucket ) {
			if ( $bucket >= $base_size['width'] ) {
				continue;
			}
			$bucket_image = wp_get_image_editor( $base_path );
			if ( ! is_wp_error( $bucket_image ) ) {
				$bucket_image->resize( $bucket, $bucket, true );
				$bucket_image->set_quality( 90 );
				$bucket_image->save( $user_dir . sprintf( 'avatar-%1$dx%1$d.%2$s', $bucket, $save_ext ) );
			}
		}
	}

	update_user_meta( $user_id, 'pmpro_avatar', array(
		'extension' => $save_ext,
		'uploaded'  => time(),
	) );

	return true;
}

// ─── Progress counters ────────────────────────────────────────────────────────

function pmpro_avatar_migration_increment( $key ) {
	$stats = pmpro_avatar_migration_get_stats();
	$stats[ $key ]++;
	update_option( 'pmpro_avatar_migration_stats', $stats, false );
}

function pmpro_avatar_migration_get_stats() {
	return wp_parse_args( (array) get_option( 'pmpro_avatar_migration_stats', array() ), array(
		'total'     => 0,
		'done'      => 0,
		'skipped'   => 0,
		'errors'    => 0,
		'queued_at' => 0,
	) );
}

// ─── Cleanup ──────────────────────────────────────────────────────────────────

/**
 * WP_User_Query args matching migrated users who still have old source data
 * to clean up. Also used to hide the cleanup UI once cleanup has run.
 *
 * @param int $number Max users to return.
 * @return array
 */
function pmpro_avatar_migration_cleanable_args( $number ) {
	$source_metas = array( 'relation' => 'OR' );
	foreach ( pmpro_avatar_migration_get_sources() as $source ) {
		$source_metas[] = array(
			'key'     => $source['meta_key'],
			'compare' => 'EXISTS',
		);
	}

	return array(
		'fields'     => 'ID',
		'number'     => $number,
		'meta_query' => array(
			'relation' => 'AND',
			array( 'key' => 'pmpro_avatar', 'compare' => 'EXISTS' ),
			$source_metas,
		),
	);
}

/**
 * Count migrated users who still have old source data to clean up.
 *
 * @return int
 */
function pmpro_avatar_migration_count_cleanable() {
	$query = new WP_User_Query( array_merge(
		pmpro_avatar_migration_cleanable_args( 1 ),
		array( 'count_total' => true )
	) );
	return (int) $query->get_total();
}

/**
 * Delete source avatar files and meta for users with a confirmed PMPro avatar.
 * Users who errored (no pmpro_avatar) are left untouched.
 *
 * @return int Number of users cleaned.
 */
function pmpro_avatar_migration_cleanup() {
	$migrated = get_users( pmpro_avatar_migration_cleanable_args( -1 ) );

	$sources = pmpro_avatar_migration_get_sources();

	foreach ( $migrated as $user_id ) {
		$user_id = (int) $user_id;
		foreach ( $sources as $source ) {
			if ( ! empty( $source['cleanup'] ) && is_callable( $source['cleanup'] ) ) {
				call_user_func( $source['cleanup'], $user_id );
			}
		}
		delete_user_meta( $user_id, 'pmpro_avatar_migration_error' );
	}

	return count( $migrated );
}

// ─── Admin page ───────────────────────────────────────────────────────────────

add_action( 'admin_menu', function () {
	add_management_page(
		'PMPro Avatar Migration',
		'PMPro Avatar Migration',
		'manage_options',
		'pmpro-avatar-migration',
		'pmpro_avatar_migration_admin_page'
	);
} );

function pmpro_avatar_migration_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized.' );
	}

	$message = '';

	if ( isset( $_POST['pmpro_avatar_migration_start'] ) && check_admin_referer( 'pmpro_avatar_migration' ) ) {
		$queued = pmpro_avatar_migration_queue_all();

		if ( is_wp_error( $queued ) ) {
			$message = '<div class="notice notice-error"><p>' . esc_html( $queued->get_error_message() ) . '</p></div>';
		} elseif ( 0 === $queued ) {
			$message = '<div class="notice notice-warning"><p>No users need migration. Either no source avatars were found, or all users already have a PMPro avatar.</p></div>';
		} else {
			$message = '<div class="notice notice-success"><p>Queued <strong>' . esc_html( $queued ) . '</strong> users. Action Scheduler will process them in the background — refresh this page to watch progress.</p></div>';
		}
	} elseif ( isset( $_POST['pmpro_avatar_migration_cleanup'] ) && check_admin_referer( 'pmpro_avatar_migration' ) ) {
		$cleaned = pmpro_avatar_migration_cleanup();
		$message = '<div class="notice notice-success"><p>'
			. sprintf( 'Cleanup complete. Removed old avatar data for <strong>%d</strong> migrated users.', $cleaned )
			. '</p></div>';
	}

	// Per-source discovery preview.
	$source_counts = array();
	foreach ( pmpro_avatar_migration_get_sources() as $name => $source ) {
		$source_counts[ $name ] = array(
			'meta_key' => $source['meta_key'],
			'count'    => count( get_users( array(
				'meta_key'     => $source['meta_key'],
				'meta_compare' => 'EXISTS',
				'fields'       => 'ID',
				'number'       => -1,
			) ) ),
		);
	}

	$candidates_count = pmpro_avatar_migration_count_candidates();
	$migrated_count   = count( get_users( array(
		'meta_key'     => 'pmpro_avatar',
		'meta_compare' => 'EXISTS',
		'fields'       => 'ID',
		'number'       => -1,
	) ) );
	$stats           = pmpro_avatar_migration_get_stats();
	$pending         = pmpro_avatar_migration_pending_count();
	$cleanable_count = pmpro_avatar_migration_count_cleanable();

	$errored_users = get_users( array(
		'meta_key'     => 'pmpro_avatar_migration_error',
		'meta_compare' => 'EXISTS',
		'number'       => 100,
	) );
	?>
	<div class="wrap">
		<h1>PMPro Avatar Migration</h1>
		<p>Migrates existing user avatars to PMPro's avatar system via Action Scheduler. Users who already have a <code>pmpro_avatar</code> are skipped, so it is safe to queue multiple times. Errored users are retried when you re-queue.</p>

		<?php echo $message; ?>

		<h2>Source discovery</h2>
		<table class="widefat fixed striped" style="max-width:520px">
			<thead><tr><th>Source</th><th>User meta key</th><th>Users</th></tr></thead>
			<tbody>
				<?php foreach ( $source_counts as $name => $info ) : ?>
				<tr>
					<td><?php echo esc_html( $name ); ?></td>
					<td><code><?php echo esc_html( $info['meta_key'] ); ?></code></td>
					<td><strong><?php echo esc_html( $info['count'] ); ?></strong></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<p><strong><?php echo esc_html( $candidates_count ); ?></strong> users need migration. <strong><?php echo esc_html( $migrated_count ); ?></strong> already have a PMPro avatar.</p>

		<?php if ( $stats['queued_at'] ) : ?>
		<h2>Migration progress</h2>
		<table class="widefat fixed striped" style="max-width:420px">
			<tbody>
				<tr><td>Total users queued</td><td><strong><?php echo esc_html( $stats['total'] ); ?></strong></td></tr>
				<tr><td>Completed successfully</td><td><strong><?php echo esc_html( $stats['done'] ); ?></strong></td></tr>
				<tr><td>No source file (skipped)</td><td><strong><?php echo esc_html( $stats['skipped'] ); ?></strong></td></tr>
				<tr><td>Errors</td><td><strong><?php echo esc_html( $stats['errors'] ); ?></strong></td></tr>
				<tr><td>Remaining</td><td><strong><?php echo esc_html( $candidates_count ); ?></strong></td></tr>
				<tr><td>Last queued</td><td><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $stats['queued_at'] ) ); ?></td></tr>
			</tbody>
		</table>
		<?php if ( $pending > 0 ) : ?>
			<p><em>Action Scheduler is processing users in the background. Refresh this page to update.</em></p>
		<?php endif; ?>
		<?php endif; ?>

		<h2>Run migration</h2>
		<form method="post">
			<?php wp_nonce_field( 'pmpro_avatar_migration' ); ?>
			<p>
				<button type="submit" name="pmpro_avatar_migration_start" class="button button-primary">
					<?php echo $stats['queued_at'] ? 'Re-queue Migration' : 'Queue Migration'; ?>
				</button>
			</p>
			<p class="description">Processes users in batches of 20 in the background. Requires WP-Cron or a real server cron to fire.</p>
		</form>

		<?php if ( $errored_users ) : ?>
		<h2>Users with errors</h2>
		<p>These users failed to migrate or had no source file. Fix the underlying issue (see the message), then click Re-queue Migration — they are retried automatically.</p>
		<table class="widefat fixed striped" style="max-width:640px">
			<thead><tr><th>User</th><th>Error</th></tr></thead>
			<tbody>
			<?php foreach ( $errored_users as $u ) : ?>
				<tr>
					<td><a href="<?php echo esc_url( get_edit_user_link( $u->ID ) ); ?>"><?php echo esc_html( $u->user_login ); ?></a> (ID: <?php echo esc_html( $u->ID ); ?>)</td>
					<td><code><?php echo esc_html( get_user_meta( $u->ID, 'pmpro_avatar_migration_error', true ) ); ?></code></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>

		<?php if ( $cleanable_count > 0 && 0 === $pending ) : ?>
		<h2>Cleanup old avatar data</h2>
		<p>
			Permanently deletes the original source files and user meta for the
			<strong><?php echo esc_html( $cleanable_count ); ?></strong> migrated users who still have old avatar data.
			Users who errored during migration are left untouched.
		</p>
		<p style="color:#b32d2e"><strong>This is irreversible.</strong> Only run this after confirming avatars look correct on the front end.</p>
		<form method="post">
			<?php wp_nonce_field( 'pmpro_avatar_migration' ); ?>
			<p>
				<button type="submit" name="pmpro_avatar_migration_cleanup"
					class="button button-secondary"
					onclick="return confirm('Delete all old avatar source files and meta for <?php echo esc_js( $cleanable_count ); ?> migrated users? This cannot be undone.');">
					Delete old avatar data (<?php echo esc_html( $cleanable_count ); ?> users)
				</button>
			</p>
		</form>
		<?php endif; ?>
	</div>
	<?php
}
