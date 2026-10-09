<?php
get_header();
while ( have_posts() ) :
    the_post();
    $cpc_layout = cpc_page_layout( get_the_ID() );
    $cpc_slug = get_post_field( 'post_name', get_the_ID() );
    cpc_page_hero( get_the_title(), 'blog' === $cpc_slug ? '' : 'CPC London', cpc_page_hero_image( get_the_ID() ) );
    if ( 'blog' === $cpc_slug ) {
        cpc_blog_grid();
    } elseif ( $cpc_layout ) {
        echo '<div class="cpc-design-content">';
        cpc_theme_part( $cpc_layout );
        echo '</div>';
    } else {
        echo '<article class="article prose page-' . esc_attr( $cpc_slug ) . '" data-reveal>';
        the_content();
        echo '</article>';
    }
endwhile;
get_footer();
