<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>
<section class="inner-hero"><div class="wrap"><div class="crumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / CPC London</div><h1><?php the_title(); ?></h1></div></section>
<?php if ( 'blog' === get_post_field( 'post_name', get_the_ID() ) ) : cpc_blog_grid(); ?>
<?php elseif ( cpc_is_design_layout( get_the_ID() ) ) : ?>
<div class="cpc-design-content"><?php the_content(); ?></div>
<?php else : ?>
<article class="article"><?php the_content(); ?></article>
<?php endif; endwhile; ?>
<?php get_footer(); ?>
