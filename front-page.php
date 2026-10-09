<?php
/** Site root: the designed CPC homepage (theme layout generated from the original site's content). */
get_header();
if ( cpc_part_file( 'home' ) ) {
    cpc_theme_part( 'home' );
} else {
    $cpc_home = cpc_home_page_id();
    if ( $cpc_home ) {
        echo '<div class="cpc-home-content">' . apply_filters( 'the_content', get_post_field( 'post_content', $cpc_home ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
get_footer();
