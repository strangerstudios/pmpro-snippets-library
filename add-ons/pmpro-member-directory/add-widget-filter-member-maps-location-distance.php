<?php
/**
 * This recipe will add a widget to the member directory where users can filter members based on location and distance. 
 * Requires PMPro Membership Directory v2.1+
 * 
 * title: Filter members based on location and distance in member directory
 * layout: snippet
 * collection: pmpro-membership-directory
 * category: directory, maps, widget
 * link: https://www.paidmembershipspro.com/filter-members-based-on-their-location/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
 
/**
 * Widget for filtering the directory by location and distance.
 */
class My_PMPro_Directory_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'my_pmpro_directory_widget',
			'My PMPro Directory Widget',
			array( 'description' => 'Filter the PMPro Member Directory' )
		);
	}
 
	/**
	 * Code that runs on the frontend.
	 *
	 * Modify the content in the <li> tags to
	 * create filter inputs in the sidebar
	 */
	public function widget( $args, $instance ) {
		global $post;
		$current_post = is_a( $post, 'WP_Post' ) ? $post : get_queried_object();
		if ( ! is_a( $current_post, 'WP_Post' ) || ( ! has_shortcode( $current_post->post_content, 'pmpro_member_directory' ) && ! has_block( 'pmpro-member-directory/directory', $current_post->post_content ) ) ) {
			return;
		}
		?>
		<aside id="my_pmpro_directory_widget" class="widget my_pmpro_directory_widget">
			<h3 class="widget-title">Filter by Address</h3>
			<form>
				<p><label>Location</label></p>
				<input type="text" name="location" value="<?php echo ( ! empty( $_REQUEST['location'] ) ) ? esc_attr( urldecode( $_REQUEST['location'] ) ) : ''; ?>">
				<p><label>Distance</label></p>
				<select name="distance">
				<?php
				$distance_options = array(
					'5'  => '5 miles',
					'10' => '10 miles',
					'15' => '15 miles',
					'20' => '20 miles',
					'25' => '25 miles',
					'50' => '50 miles',
				);
				$selected_distance = isset( $_REQUEST['distance'] ) ? $_REQUEST['distance'] : '';
				foreach ( $distance_options as $key => $value ) {
					$selected = ( $selected_distance === $key ) ? ' selected' : '';
					echo '<option value="' . esc_attr( $key ) . '"' . $selected . '>' . esc_html( $value ) . '</option>';
				}
				?>
				</select>
				<p><input type="submit" value="Filter"></p>
			</form>
		</aside>
		<?php
	}
}

function my_pmpro_register_directory_widget() {
	register_widget( 'My_PMPro_Directory_Widget' );
}
add_action( 'widgets_init', 'my_pmpro_register_directory_widget' );

/**
 * Remove the SQL LIMIT when filtering by location so the distance filter
 * can check all members, not just the current page's batch.
 */
function my_pmpromd_remove_limit_for_location_filter( $sql_parts ) {
	if ( ! empty( $_REQUEST['location'] ) && ! empty( $_REQUEST['distance'] ) ) {
		$sql_parts['LIMIT'] = '';
	}
	return $sql_parts;
}
add_filter( 'pmpro_member_directory_sql_parts', 'my_pmpromd_remove_limit_for_location_filter' );

/**
 * Filter directory results by distance from the searched location.
 * Uses maplocation already fetched by the directory SQL JOIN to avoid
 * extra DB queries per member, falling back to legacy pmpro_lat/pmpro_lng.
 */
function mypmpro_result_distance_filter( $theusers ) {

	if ( ! function_exists( 'pmpromd_geocode_map_address' ) ) {
		return $theusers;
	}

	if ( empty( $_REQUEST['location'] ) || empty( $_REQUEST['distance'] ) ) {
		return $theusers;
	}

	$coordinates = pmpromd_geocode_map_address( array(
		'street' => urldecode( $_REQUEST['location'] ),
		'city'   => '',
		'state'  => '',
		'zip'    => '',
	) );

	if ( ! is_array( $coordinates ) ) {
		return $theusers;
	}

	$filtered = array();
	foreach ( $theusers as $user ) {
		// maplocation is already on the object from the directory SQL JOIN — no extra DB call needed.
		$loc = ! empty( $user->maplocation ) ? maybe_unserialize( $user->maplocation ) : array();

		$lat = ! empty( $loc['latitude'] )  ? $loc['latitude']  : get_user_meta( $user->ID, 'pmpro_lat', true );
		$lng = ! empty( $loc['longitude'] ) ? $loc['longitude'] : get_user_meta( $user->ID, 'pmpro_lng', true );

		if ( empty( $lat ) || empty( $lng ) ) {
			continue;
		}

		$distance = my_pmpromd_calculate_distance( $coordinates['lat'], $coordinates['lng'], $lat, $lng, 'm' );
		if ( $distance <= floatval( $_REQUEST['distance'] ) ) {
			$filtered[] = $user;
		}
	}

	// Store the true filtered total so pagination can be corrected after render.
	$GLOBALS['my_pmpromd_filtered_total'] = count( $filtered );

	// Paginate the filtered results.
	$per_page = 15;
	$pn       = isset( $_REQUEST['pn'] ) ? max( 1, intval( $_REQUEST['pn'] ) ) : 1;
	return array_slice( $filtered, ( $pn - 1 ) * $per_page, $per_page );
}
add_filter( 'pmpromd_user_directory_results', 'mypmpro_result_distance_filter', 10, 9 );

/**
 * Fix the rendered directory HTML when a distance filter is active:
 * replaces both the pagination nav and the "Showing X-Y of Z Results" line
 * with values derived from the actual filtered count rather than the SQL total.
 */
function my_pmpromd_fix_directory_output( $html ) {
	if ( ! isset( $GLOBALS['my_pmpromd_filtered_total'] ) ) {
		return $html;
	}

	$filtered_total = (int) $GLOBALS['my_pmpromd_filtered_total'];
	$per_page       = 15;
	$pn             = isset( $_REQUEST['pn'] ) ? max( 1, intval( $_REQUEST['pn'] ) ) : 1;
	$start          = ( $pn - 1 ) * $per_page;
	$end            = min( $pn * $per_page, $filtered_total );

	// Build the correct "Showing" text.
	if ( $filtered_total === 0 ) {
		$showing_text = '';
	} elseif ( $filtered_total === 1 ) {
		$showing_text = '<p>' . esc_html__( 'Showing 1 Result', 'pmpro-member-directory' ) . '</p>';
	} else {
		$showing_text = '<p>' . esc_html( sprintf(
			__( 'Showing %1$s-%2$s of %3$s Results', 'pmpro-member-directory' ),
			$start + 1,
			$end,
			$filtered_total
		) ) . '</p>';
	}

	// Replace the "Showing X-Y of Z Results" paragraph.
	$html = preg_replace(
		'/<p>\s*Showing[^<]*<\/p>/i',
		$showing_text,
		$html
	);

	// Build correct pagination nav.
	global $post;
	$current_post           = is_a( $post, 'WP_Post' ) ? $post : get_queried_object();
	$target_page_query_args = apply_filters( 'pmpromd_pagination_url', array(
		'ps'    => isset( $_REQUEST['ps'] ) ? sanitize_text_field( $_REQUEST['ps'] ) : '',
		'limit' => $per_page,
	) );
	$correct_pagination = pmpro_getPaginationString(
		$pn,
		$filtered_total,
		$per_page,
		1,
		esc_url( add_query_arg( $target_page_query_args, get_permalink( $current_post->ID ) ) ),
		'&pn=',
		__( 'Member Directory Pagination', 'pmpro-member-directory' )
	);

	// Replace the pagination nav.
	$replaced = preg_replace(
		'/<nav[^>]+class="[^"]*pmpro_pagination[^"]*"[^>]*>[\s\S]*?<\/nav>/i',
		$correct_pagination,
		$html
	);
	if ( $replaced !== null ) {
		$html = $replaced;
	}

	return $html;
}

function my_pmpromd_fix_block_output( $block_content ) {
	return my_pmpromd_fix_directory_output( $block_content );
}
add_filter( 'render_block_pmpro-member-directory/directory', 'my_pmpromd_fix_block_output' );

function my_pmpromd_fix_shortcode_output( $output, $tag ) {
	if ( $tag !== 'pmpro_member_directory' ) {
		return $output;
	}
	return my_pmpromd_fix_directory_output( $output );
}
add_filter( 'do_shortcode_tag', 'my_pmpromd_fix_shortcode_output', 10, 2 );

/**
 * Carry location and distance params through directory pagination links.
 */
function my_pmpromd_pagination_url_filter_directory( $query_args ) {
	foreach ( array( 'location', 'distance' ) as $key ) {
		if ( ! empty( $_REQUEST[ $key ] ) ) {
			$query_args[ $key ] = $_REQUEST[ $key ];
		}
	}
	return $query_args;
}
add_filter( 'pmpromd_pagination_url', 'my_pmpromd_pagination_url_filter_directory' );

/**
 * Calculate distance in miles (or km) between two lat/lng coordinates.
 */
function my_pmpromd_calculate_distance( $lat1, $lon1, $lat2, $lon2, $unit ) {
	$theta = $lon1 - $lon2;
	$dist  = sin( deg2rad( $lat1 ) ) * sin( deg2rad( $lat2 ) )
	       + cos( deg2rad( $lat1 ) ) * cos( deg2rad( $lat2 ) ) * cos( deg2rad( $theta ) );
	$miles = rad2deg( acos( $dist ) ) * 60 * 1.1515;

	return ( strtoupper( $unit ) === 'KM' ) ? $miles * 1.609344 : $miles;
}
