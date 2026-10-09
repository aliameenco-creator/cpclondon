<?php get_header(); ?>
<section class="inner-hero"><div class="wrap"><h1><?php if ( is_archive() ) { echo esc_html( get_the_archive_title() ); } elseif ( is_search() ) { echo 'Search'; } else { echo 'Blog'; } ?></h1></div></section>
<section class="section"><div class="wrap">
<?php if ( is_search() ) { get_search_form(); } ?>
<div class="journal-grid"><?php while ( have_posts() ) : the_post(); get_template_part( 'parts/card' ); endwhile; ?></div>
<?php if ( ! have_posts() && 0 === $wp_query->post_count ) : ?><p>No results found.</p><?php endif; ?>
<nav class="pagination" aria-label="Pages"><?php the_posts_pagination(); ?></nav>
</div></section>
<?php get_footer(); ?>
