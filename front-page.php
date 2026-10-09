<?php
/** The site root always shows the CPC homepage page, even before Settings > Reading is changed. */
get_header();
$cpc_home = cpc_home_page_id();
if ( $cpc_home ) {
    $post = get_post( $cpc_home ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
    setup_postdata( $post );
    echo '<div class="cpc-home-content">';
    the_content();
    echo '</div>';
    wp_reset_postdata();
} else {
    cpc_blog_grid();
}
get_footer();
