<?php
/**
 * Add a "Members by Chapter" report (Memberships > Reports) that shows an active
 * member count per chapter and a list of the active members for the selected chapter.
 *
 * title: Add a Members by Chapter Report
 * layout: snippet
 * collection: admin-pages
 * category: admin
 * link: https://www.paidmembershipspro.com/how-to-manage-association-chapters/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

defined( 'ABSPATH' ) || exit;

// Shared chapters taxonomy slug. Guarded so any of the related snippets can declare it.
if ( ! function_exists( 'my_pmpro_chapters_taxonomy' ) ) {
	function my_pmpro_chapters_taxonomy() {
		return 'chapters'; // Update this to match your site's custom user taxonomy.
	}
}

// Register the report slug + title.
function pmpro_report_members_by_chapter_register( $reports ) {
	$reports['members_by_chapter'] = esc_html__( 'Members by Chapter', 'pmpro-snippets-library' );

	return $reports;
}
add_filter( 'pmpro_registered_reports', 'pmpro_report_members_by_chapter_register' );

// The chapter term IDs the current viewer may see, or null for all chapters.
// The Chapter Leadership snippet provides the scoping when active.
function my_pmpro_report_members_by_chapter_allowed_ids() {
	if ( function_exists( 'my_pmpro_report_allowed_chapter_ids' ) ) {
		return my_pmpro_report_allowed_chapter_ids();
	}

	return null;
}

// Count of ACTIVE members per chapter. Pass an array of term IDs to limit results,
// or null for all. The LEFT JOIN to pmpro_memberships_users (status = 'active')
// means chapters with no active members still appear with a count of 0, and
// COUNT( DISTINCT ) keeps users with more than one active level from inflating it.
function my_pmpro_get_member_counts_by_chapter( $allowed_ids = null ) {
	global $wpdb;

	$where_in = '';
	if ( is_array( $allowed_ids ) ) {
		if ( empty( $allowed_ids ) ) {
			return array();
		}
		$ids      = array_map( 'absint', $allowed_ids );
		$where_in = ' AND t.term_id IN (' . implode( ',', $ids ) . ')';
	}

	$sql = $wpdb->prepare(
		"SELECT t.term_id, t.name, COUNT( DISTINCT mu.user_id ) AS num_members
		 FROM {$wpdb->term_taxonomy} tt
		 INNER JOIN {$wpdb->terms} t ON t.term_id = tt.term_id
		 LEFT JOIN {$wpdb->term_relationships} tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
		 LEFT JOIN {$wpdb->pmpro_memberships_users} mu ON mu.user_id = tr.object_id AND mu.status = 'active'
		 WHERE tt.taxonomy = %s{$where_in}
		 GROUP BY t.term_id, t.name
		 ORDER BY t.name ASC",
		my_pmpro_chapters_taxonomy()
	);

	return $wpdb->get_results( $sql );
}

// The ACTIVE members assigned to a given chapter term. The INNER JOIN to
// pmpro_memberships_users (status = 'active') excludes users who hold the chapter
// term but have no active membership; GROUP BY collapses users with more than one
// active level to a single row. Stays in sync with the counts query above.
function my_pmpro_get_members_in_chapter( $term_id ) {
	global $wpdb;

	$sql = $wpdb->prepare(
		"SELECT u.ID, u.user_login, u.display_name, u.user_email, u.user_registered
		 FROM {$wpdb->users} u
		 INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = u.ID
		 INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
		 INNER JOIN {$wpdb->pmpro_memberships_users} mu ON mu.user_id = u.ID AND mu.status = 'active'
		 WHERE tt.taxonomy = %s AND tt.term_id = %d
		 GROUP BY u.ID, u.user_login, u.display_name, u.user_email, u.user_registered
		 ORDER BY u.display_name ASC",
		my_pmpro_chapters_taxonomy(),
		(int) $term_id
	);

	return $wpdb->get_results( $sql );
}

// The dashboard widget shown on the Reports homepage.
function pmpro_report_members_by_chapter_widget() {
	$counts = my_pmpro_get_member_counts_by_chapter( my_pmpro_report_members_by_chapter_allowed_ids() );
	?>
	<span id="pmpro_report_members_by_chapter_widget" class="pmpro_report-holder">
		<table class="widefat striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Chapter', 'pmpro-snippets-library' ); ?></th>
					<th><?php esc_html_e( 'Members', 'pmpro-snippets-library' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( ! empty( $counts ) ) { ?>
					<?php foreach ( $counts as $row ) { ?>
						<tr>
							<td><?php echo esc_html( $row->name ); ?></td>
							<td><?php echo intval( $row->num_members ); ?></td>
						</tr>
					<?php } ?>
				<?php } else { ?>
					<tr><td colspan="2"><?php esc_html_e( 'No chapters found.', 'pmpro-snippets-library' ); ?></td></tr>
				<?php } ?>
			</tbody>
		</table>
		<?php if ( function_exists( 'pmpro_report_members_by_chapter_page' ) ) { ?>
			<p class="pmpro_report-button">
				<a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=pmpro-reports&report=members_by_chapter' ) ); ?>"><?php esc_html_e( 'Details', 'pmpro-snippets-library' ); ?></a>
			</p>
		<?php } ?>
	</span>
	<?php
}

// The full report page: a per-chapter overview, or the selected chapter's members.
function pmpro_report_members_by_chapter_page() {
	$taxonomy = my_pmpro_chapters_taxonomy();
	$selected = isset( $_REQUEST['chapter'] ) ? (int) $_REQUEST['chapter'] : 0;

	// null = all chapters (full admins); an array = only those chapter IDs.
	$allowed = my_pmpro_report_members_by_chapter_allowed_ids();
	$counts  = my_pmpro_get_member_counts_by_chapter( $allowed );

	// Don't let a scoped viewer open a chapter outside their set via the URL.
	if ( is_array( $allowed ) && ! empty( $selected ) && ! in_array( $selected, array_map( 'absint', $allowed ), true ) ) {
		$selected = 0;
	}
	?>
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Members by Chapter', 'pmpro-snippets-library' ); ?></h1>

	<form method="get" action="">
		<input type="hidden" name="page" value="pmpro-reports" />
		<input type="hidden" name="report" value="members_by_chapter" />
		<label for="pmpro_report_chapter"><?php esc_html_e( 'Chapter:', 'pmpro-snippets-library' ); ?></label>
		<select name="chapter" id="pmpro_report_chapter" onchange="this.form.submit()">
			<option value="0"><?php esc_html_e( '- All Chapters -', 'pmpro-snippets-library' ); ?></option>
			<?php foreach ( $counts as $row ) { ?>
				<option value="<?php echo esc_attr( $row->term_id ); ?>" <?php selected( $selected, $row->term_id ); ?>>
					<?php echo esc_html( $row->name ); ?> (<?php echo intval( $row->num_members ); ?>)
				</option>
			<?php } ?>
		</select>
		<noscript><button type="submit" class="button"><?php esc_html_e( 'Go', 'pmpro-snippets-library' ); ?></button></noscript>
	</form>

	<?php if ( empty( $selected ) ) { ?>
		<table class="widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Chapter', 'pmpro-snippets-library' ); ?></th>
					<th><?php esc_html_e( 'Number of Members', 'pmpro-snippets-library' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( ! empty( $counts ) ) { ?>
					<?php foreach ( $counts as $row ) { ?>
						<tr>
							<td><a href="<?php echo esc_url( admin_url( 'admin.php?page=pmpro-reports&report=members_by_chapter&chapter=' . $row->term_id ) ); ?>"><?php echo esc_html( $row->name ); ?></a></td>
							<td><?php echo intval( $row->num_members ); ?></td>
						</tr>
					<?php } ?>
				<?php } else { ?>
					<tr><td colspan="2"><?php esc_html_e( 'No chapters found. Add chapters under Users > Chapters.', 'pmpro-snippets-library' ); ?></td></tr>
				<?php } ?>
			</tbody>
		</table>
	<?php
	} else {
		$members      = my_pmpro_get_members_in_chapter( $selected );
		$term         = get_term( $selected, $taxonomy );
		$chapter_name = ( $term && ! is_wp_error( $term ) ) ? $term->name : '';
		?>
		<h2><?php echo esc_html( sprintf( __( 'Members in %s', 'pmpro-snippets-library' ), $chapter_name ) ); ?></h2>
		<table class="widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Username', 'pmpro-snippets-library' ); ?></th>
					<th><?php esc_html_e( 'Name', 'pmpro-snippets-library' ); ?></th>
					<th><?php esc_html_e( 'Email', 'pmpro-snippets-library' ); ?></th>
					<th><?php esc_html_e( 'Level', 'pmpro-snippets-library' ); ?></th>
					<th><?php esc_html_e( 'Registered', 'pmpro-snippets-library' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( ! empty( $members ) ) { ?>
					<?php
					foreach ( $members as $member ) {
						$level      = function_exists( 'pmpro_getMembershipLevelForUser' ) ? pmpro_getMembershipLevelForUser( $member->ID ) : false;
						$level_name = ! empty( $level->name ) ? $level->name : __( '— None —', 'pmpro-snippets-library' );
						?>
						<tr>
							<td><a href="<?php echo esc_url( add_query_arg( 'user_id', $member->ID, self_admin_url( 'user-edit.php' ) ) ); ?>"><?php echo esc_html( $member->user_login ); ?></a></td>
							<td><?php echo esc_html( $member->display_name ); ?></td>
							<td><a href="mailto:<?php echo esc_attr( $member->user_email ); ?>"><?php echo esc_html( $member->user_email ); ?></a></td>
							<td><?php echo esc_html( $level_name ); ?></td>
							<td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $member->user_registered ) ) ); ?></td>
						</tr>
					<?php } ?>
				<?php } else { ?>
					<tr><td colspan="5"><?php esc_html_e( 'No active members in this chapter.', 'pmpro-snippets-library' ); ?></td></tr>
				<?php } ?>
			</tbody>
		</table>
		<p>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=pmpro-reports&report=members_by_chapter' ) ); ?>">&larr; <?php esc_html_e( 'Back to all chapters', 'pmpro-snippets-library' ); ?></a>
		</p>
	<?php } ?>
	<?php
}
