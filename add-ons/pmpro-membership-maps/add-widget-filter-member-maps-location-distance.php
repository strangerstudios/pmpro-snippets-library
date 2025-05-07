<?php
/**
 * This recipe will add a widget to the member directory where users can filter
 * members based on location and distance. 
 * Requires PMPro Membership Directory > v1.0 + PMPro Membership Maps Add Ons
 *
 * title: Filter members based on location and distance in member directory
 * layout: snippet
 * collection: pmpro-membership-maps
 * category: maps, directory
 * link: https://www.paidmembershipspro.com/filter-members-based-on-their-location/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Set up widget
 */
class My_PMPro_Directory_Widget extends WP_Widget {

	/**
	 * Sets up the widget
	 */
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
		// If we're not on a page with a PMPro directory, return.
		global $post;
		if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'pmpro_member_directory' ) ) {
			return;
		}
		?>
		<aside id="my_pmpro_directory_widget" class="widget my_pmpro_directory_widget">
			<h3 class="widget-title">Filter by Address</h3>
			<form>
				<p><label>Location</label></p>
				<input type="text" name="location" value="<?php echo ( !empty( $_REQUEST['location'] ) ) ? urldecode( $_REQUEST['location'] ) : ""; ?>">
						<p><label>Distance</label></p>
						<select name='distance'>
						<?php
						/**
						 * Set up values to filter for. 
						 * You can change the distance steps to yur preference here
						 */
						$colors = array(
							'5'  => '5 miles',
							'10'  => '10 miles',
							'15'  => '15 miles',
							'20'   => '20 miles',
							'25' => '25 miles',
							'50'   => '50 miles',
						);
						foreach ( $colors as $key => $value ) {
							// Check if this value should default to be checked.
							$checked_modifier = '';
              $selected_distance = isset( $_REQUEST['distance'] ) ? $_REQUEST['distance'] : '';
  if ( $selected_distance === $key ) {
              $checked_modifier = ' selected';
							}
							// Add checkbox.
							echo '<option value="' . $key . '"' . $checked_modifier . '>' . $value . '</option>';
						}						
						?>
						</select>
					<p><input type="submit" value="Filter"></p>
			</form>
		</aside>
		<?php
	}

}

/**
 * Check $_REQUEST for parameters from the widget. Filter member results here
 */
function mypmpro_result_distance_filter( $theusers ) {
	
	if( !function_exists( 'pmpromm_geocode_address' ) ){
		return $theusers;
	}

	if( empty( $_REQUEST['location'] ) || empty( $_REQUEST['distance'] ) ){
		return $theusers;
	}

	$member_address = array(
		'street' 	=> urldecode( $_REQUEST['location'] ),
		'city' 		=> '',
		'state' 	=> '',
		'zip' 		=> ''
	);

	$coordinates = pmpromm_geocode_address( $member_address );

	$filtered_members = array();

	if( is_array( $coordinates ) ){
		foreach( $theusers as $key => $user ){

			$user_id = intval( $user->ID );
			
			$lat = get_user_meta( $user_id, 'pmpro_lat', true );
			$lng = get_user_meta( $user_id, 'pmpro_lng', true );

			if( !empty( $lat ) && !empty( $lng ) ){
				$distance = mypmpromm_calculate_distance( $coordinates['lat'], $coordinates['lng'], $lat, $lng, 'm' );

				if( $distance <= $_REQUEST['distance'] ){
					$filtered_members[] = $user;
				}

			}
		}
		
		return $filtered_members;

	}

	return $theusers;

}
add_filter( 'pmpromd_user_directory_results', 'mypmpro_result_distance_filter', 10, 9 );

/**
 * Check $_REQUEST for parameters from the widget. Filter marker results here
 */
function mypmpro_marker_result_distance_filter( $markers ){

	if( !function_exists( 'pmpromm_geocode_address' ) ){
		return $markers;
	}

	if( empty( $_REQUEST['location'] ) || empty( $_REQUEST['distance'] ) ){
		return $markers;
	}

	$member_address = array(
		'street' 	=> urldecode( $_REQUEST['location'] ),
		'city' 		=> '',
		'state' 	=> '',
		'zip' 		=> ''
	);

	$coordinates = pmpromm_geocode_address( $member_address );

	$filtered_members = array();

	if( is_array( $coordinates ) ){

		foreach( $markers as $key => $marker ){

			if( !empty( $marker['marker_meta'] ) ){
				$lat = ( !empty( $marker['marker_meta']['lat'] ) ) ? $marker['marker_meta']['lat'] : '';
				$lng = ( !empty( $marker['marker_meta']['lng'] ) ) ? $marker['marker_meta']['lng'] : '';

				if( !empty( $lat ) && !empty( $lng ) ){
					$distance = mypmpromm_calculate_distance( $coordinates['lat'], $coordinates['lng'], $lat, $lng, 'm' );

					if( $distance <= $_REQUEST['distance'] ){
						$filtered_members[] = $marker;
					}
				}

			}

		}
		
		return $filtered_members;

	}

	return $markers;

}
add_filter( 'pmpromm_return_markers_array', 'mypmpro_marker_result_distance_filter', 10, 1 );

/**
 * Registers widget.
 */
function my_pmpro_register_directory_widget() {
	register_widget( 'My_PMPro_Directory_Widget' );
}
add_action( 'widgets_init', 'my_pmpro_register_directory_widget' );

/**
 * Remember filters being used while using "Next" and "Previous" buttons.
 *
 * @since 2020/06/25
 */
function my_pmpromd_pagination_url_filter_directory( $query_args ) {
	$directory_filters = array( 'location', 'distance' );
	foreach ( $directory_filters as $directory_filter ) {
		if ( ! empty( $_REQUEST[ $directory_filter ] ) ) {
        		$query_args[ $directory_filter ] =  $_REQUEST[ $directory_filter ];
    		}
	}
	return $query_args;
}
add_filter( 'pmpromd_pagination_url', 'my_pmpromd_pagination_url_filter_directory' );

function mypmpromm_calculate_distance( $lat1, $lon1, $lat2, $lon2, $unit ){

	$theta = $lon1 - $lon2;

	$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));

	$dist = acos($dist);
	$dist = rad2deg($dist);
	$miles = $dist * 60 * 1.1515;

	$unit = strtoupper($unit);

	if ($unit == "km") {
		return ($miles * 1.609344);
	} else {
		return $miles;
	}

}
