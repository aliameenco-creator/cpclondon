<?php
/**
 * Template Name: Editor content (no theme layout)
 * Shows exactly what is in the WordPress editor instead of the theme's designed layout for this page.
 */
get_header();
while ( have_posts() ) :
    the_post();
    cpc_page_hero( get_the_title(), 'CPC London', cpc_page_hero_image( get_the_ID() ) );
    echo '<article class="article prose">';
    the_content();
    echo '</article>';
endwhile;
get_footer();
