<a class="journal-card" href="<?php the_permalink(); ?>">
<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'large', array( 'loading' => 'lazy', 'alt' => '' ) ); } else { echo '<img src="' . esc_url( get_theme_file_uri( 'assets/journal-fallback.webp' ) ) . '" alt="" loading="lazy">'; } ?>
<span class="eyebrow">BLOG</span><h2><?php the_title(); ?></h2><span class="text-link">Read more ↗</span></a>
