<?php
get_header();
while ( have_posts() ) :
    the_post();
    $cpc_blog = get_page_by_path( 'blog' );
    ?>
<?php $cpc_cover = has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'full' ) : cpc_post_cover(); ?>
<section class="inner-hero article-hero<?php echo $cpc_cover ? ' has-image' : ''; ?>"<?php echo $cpc_cover ? ' style="background-image:url(' . esc_url( $cpc_cover ) . ')"' : ''; ?>><div class="wrap">
    <div class="crumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <a href="<?php echo esc_url( $cpc_blog ? get_permalink( $cpc_blog ) : home_url( '/blog' ) ); ?>">Blog</a></div>
    <h1 class="reveal-title"><?php the_title(); ?></h1>
    <div class="article-meta"><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time><span><?php echo esc_html( cpc_reading_time() ); ?></span><?php echo cpc_post_terms(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in builder. ?></div>
</div></section>
<article class="post-body prose" data-reveal><?php the_content(); ?></article>
<nav class="post-nav wrap" aria-label="More articles">
    <?php
    $cpc_prev = get_previous_post();
    $cpc_next = get_next_post();
    if ( $cpc_prev ) { echo '<a class="post-nav-link prev" href="' . esc_url( get_permalink( $cpc_prev ) ) . '"><span>← Previous</span>' . esc_html( get_the_title( $cpc_prev ) ) . '</a>'; }
    if ( $cpc_next ) { echo '<a class="post-nav-link next" href="' . esc_url( get_permalink( $cpc_next ) ) . '"><span>Next →</span>' . esc_html( get_the_title( $cpc_next ) ) . '</a>'; }
    ?>
</nav>
<section class="section related"><div class="wrap"><div class="section-head" data-reveal><div><h2>More from the blog</h2></div><a class="text-link" href="<?php echo esc_url( $cpc_blog ? get_permalink( $cpc_blog ) : home_url( '/blog' ) ); ?>">ALL ARTICLES ↗</a></div>
<div class="post-grid"><?php echo implode( '', array_map( 'cpc_card_html', cpc_related_posts() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div></div></section>
    <?php
endwhile;
get_footer();
