<?php

/**
 * Create a shortcode to display the most recent bbPress topics with linked titles, author name, and date.
 *
 * title: Shortcode to Display Recent bbPress Topics
 * layout: snippet
 * collection: bbpress
 * category: shortcode, bbpress
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function custom_bbpress_recent_topics($atts) {
    $atts = shortcode_atts(array(
        'show' => 3,
    ), $atts, 'bbpress_recent_topics');

    $args = array(
        'posts_per_page' => intval($atts['show']),
        'post_type'      => 'topic',
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    $topics = new WP_Query($args);
    ob_start();

    if ($topics->have_posts()) {
        $count = 1;
        while ($topics->have_posts()) {
            $topics->the_post();
            ?>
            <div class="bbpress-recent-topic-row row-<?php echo $count % 2 === 0 ? 'even' : 'odd'; ?>">
                <div><strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong></div>
                <small>By: <?php the_author(); ?> on <?php the_time(get_option('date_format')); ?></small>
            </div>
            <hr />
            <?php
            $count++;
        }
        wp_reset_postdata();
    } else {
        echo '<p>No recent topics found.</p>';
    }

    return ob_get_clean();
}
add_shortcode('bbpress_recent_topics', 'custom_bbpress_recent_topics');