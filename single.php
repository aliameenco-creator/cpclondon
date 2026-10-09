<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>
<section class="inner-hero"><div class="wrap"><div class="crumb"><a href="<?php echo esc_url( home_url( '/blog' ) ); ?>">Blog</a></div><h1><?php the_title(); ?></h1><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></div></section>
<article class="article"><?php the_content(); ?></article>
<?php endwhile; ?>
<?php get_footer(); ?>
