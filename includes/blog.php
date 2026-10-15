<?php
/** Blog index, cards, article helpers. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Imported Strikingly articles end with the old site's post navigation, comment box and cookie
 * banner ("Previous/Next", "Return to site", "Submit", "Cookie Use"...). Cut from the first such
 * navigation paragraph; the theme renders its own navigation. Only exact, whole-paragraph matches.
 */
function cpc_strip_strikingly_tail( $content ) {
    $pattern = '~(?:<!-- wp:paragraph -->\s*)?<p>\s*<a [^>]*>\s*(?:Previous|Next|Return to site)\s*</a>\s*</p>~i';
    if ( preg_match( $pattern, $content, $m, PREG_OFFSET_CAPTURE ) ) {
        $content = substr( $content, 0, $m[0][1] );
    }
    return $content;
}
add_filter( 'the_content', function ( $content ) {
    if ( 'post' !== get_post_type() ) { return $content; }
    // The old blog's author avatar ("Profile picture") is page chrome, not article content.
    $content = preg_replace( '~(?:<!-- wp:image -->\s*)?<figure class="wp-block-image">\s*<img alt="Profile picture"[^>]*>\s*</figure>(?:\s*<!-- /wp:image -->)?~', '', $content );
    return cpc_strip_strikingly_tags( cpc_strip_strikingly_tail( $content ), get_the_ID() );
}, 5 );

/**
 * Some imported articles open with the old blog's tag line: a "·" paragraph followed by one
 * paragraph per category ("Film Preservation,", "Archive Film,"...) and an empty heading. The
 * theme already shows categories as chips, so hide that run. Only paragraphs that exactly match
 * one of the article's own categories/tags (optionally with a trailing comma) are removed;
 * the stored post content is not changed.
 */
function cpc_strip_strikingly_tags( $content, $post_id ) {
    $names = array();
    foreach ( array_merge( (array) get_the_category( $post_id ), (array) get_the_tags( $post_id ) ) as $term ) {
        if ( $term instanceof WP_Term ) { $names[] = strtolower( html_entity_decode( $term->name, ENT_QUOTES ) ); }
    }
    $wrap = '(?:<!-- wp:paragraph -->\s*)?<p>%s</p>(?:\s*<!-- /wp:paragraph -->)?\s*';
    $dot  = sprintf( $wrap, '\s*(?:·|&middot;|&#183;|\.)\s*' );
    if ( ! $names || ! preg_match( '~' . $dot . '~u', $content, $m, PREG_OFFSET_CAPTURE ) || $m[0][1] > 2000 ) {
        return $content;
    }
    $start = $m[0][1];
    $end   = $start + strlen( $m[0][0] );
    while ( preg_match( '~\G' . sprintf( $wrap, '([^<]{1,60})' ) . '~u', $content, $p, 0, $end ) ) {
        $label = strtolower( trim( rtrim( trim( html_entity_decode( $p[1], ENT_QUOTES ) ), ',' ) ) );
        if ( ! in_array( $label, $names, true ) ) { break; }
        $end += strlen( $p[0] );
    }
    if ( $end === $start + strlen( $m[0][0] ) ) { return $content; } // A lone dot with no tag run is left alone.
    // An empty heading block left directly after the tag line.
    if ( preg_match( '~\G(?:<!-- wp:heading -->\s*)?<h[1-6][^>]*>\s*</h[1-6]>(?:\s*<!-- /wp:heading -->)?\s*~', $content, $h, 0, $end ) ) {
        $end += strlen( $h[0] );
    }
    return substr( $content, 0, $start ) . substr( $content, $end );
}

/** Original blog cover image (from the old site's article metadata), shipped with the theme. */
function cpc_post_cover( $post = null ) {
    static $covers = null;
    if ( null === $covers ) {
        $file = get_theme_file_path( 'parts/post-covers.json' );
        $covers = is_readable( $file ) ? (array) json_decode( (string) file_get_contents( $file ), true ) : array();
    }
    $slug = get_post_field( 'post_name', get_post( $post ) );
    return isset( $covers[ $slug ] ) ? get_theme_file_uri( ltrim( $covers[ $slug ], '/' ) ) : '';
}

function cpc_excerpt( $post = null, $words = 24 ) {
    $post = get_post( $post );
    if ( has_excerpt( $post ) ) { return get_the_excerpt( $post ); }
    $text = wp_strip_all_tags( strip_shortcodes( cpc_strip_strikingly_tags( cpc_strip_strikingly_tail( $post->post_content ), $post->ID ) ) );
    $title = trim( wp_strip_all_tags( get_the_title( $post ) ) );
    // Many imported articles repeat their title as the first line.
    if ( $title && str_starts_with( ltrim( $text ), $title ) ) { $text = substr( ltrim( $text ), strlen( $title ) ); }
    return wp_trim_words( $text, $words );
}

function cpc_reading_time( $post = null ) {
    $words = str_word_count( wp_strip_all_tags( cpc_strip_strikingly_tail( get_post( $post )->post_content ) ) );
    return max( 1, (int) round( $words / 220 ) ) . ' min read';
}

function cpc_card_image( $post = null, $size = 'large' ) {
    $post = get_post( $post );
    if ( has_post_thumbnail( $post ) ) {
        return get_the_post_thumbnail( $post, $size, array( 'loading' => 'lazy', 'alt' => '' ) );
    }
    $cover = cpc_post_cover( $post );
    if ( $cover ) { return '<img src="' . esc_url( $cover ) . '" alt="" loading="lazy">'; }
    // First real image in the article (not the old author avatar), otherwise the theme's film photograph.
    if ( preg_match_all( '~<img([^>]+)>~', $post->post_content, $all ) ) {
        foreach ( $all[1] as $attrs ) {
            if ( str_contains( $attrs, 'Profile picture' ) || str_contains( $attrs, 'gravatar' ) || ! preg_match( '~src="([^"]+)"~', $attrs, $m ) ) { continue; }
            return '<img src="' . esc_url( $m[1] ) . '" alt="" loading="lazy">';
        }
    }
    return '<img src="' . esc_url( get_theme_file_uri( 'assets/journal-fallback.webp' ) ) . '" alt="" loading="lazy">';
}

function cpc_post_terms( $post = null ) {
    $out = array();
    foreach ( get_the_category( get_post( $post )->ID ) as $term ) {
        if ( 'uncategorized' === $term->slug ) { continue; }
        $out[] = '<a class="chip" href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>';
    }
    return implode( '', $out );
}

function cpc_primary_category( $post = null ) {
    foreach ( get_the_category( get_post( $post )->ID ) as $term ) {
        if ( 'uncategorized' !== $term->slug ) { return $term->name; }
    }
    return '';
}

function cpc_card_html( $post = null, $class = 'post-card' ) {
    $post = get_post( $post );
    $link = get_permalink( $post );
    $cat  = cpc_primary_category( $post );
    return '<article class="' . esc_attr( $class ) . '" data-reveal><a class="card-media" href="' . esc_url( $link ) . '" tabindex="-1" aria-hidden="true">' . cpc_card_image( $post ) . ( $cat ? '<span class="card-cat">' . esc_html( $cat ) . '</span>' : '' ) . '</a><div class="card-body"><div class="card-meta"><time datetime="' . esc_attr( get_the_date( DATE_W3C, $post ) ) . '">' . esc_html( get_the_date( 'j M Y', $post ) ) . '</time><span>' . esc_html( cpc_reading_time( $post ) ) . '</span></div><h3><a href="' . esc_url( $link ) . '">' . esc_html( get_the_title( $post ) ) . '</a></h3><p>' . esc_html( cpc_excerpt( $post ) ) . '</p></div></article>';
}

function cpc_latest_posts_html( $count = 3 ) {
    $posts = get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => $count ) );
    if ( ! $posts ) { return ''; }
    return '<div class="post-grid">' . implode( '', array_map( 'cpc_card_html', $posts ) ) . '</div>';
}

function cpc_category_chips( $current = 0 ) {
    $terms = get_categories( array( 'hide_empty' => true, 'exclude' => array( (int) get_option( 'default_category' ) ) ) );
    if ( ! $terms ) { return ''; }
    $blog = get_page_by_path( 'blog' );
    $all = $blog ? get_permalink( $blog ) : home_url( '/blog' );
    $out = '<nav class="chips" aria-label="Blog categories"><a class="chip' . ( $current ? '' : ' active' ) . '" href="' . esc_url( $all ) . '">All</a>';
    foreach ( $terms as $term ) {
        $out .= '<a class="chip' . ( (int) $current === (int) $term->term_id ? ' active' : '' ) . '" href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . ' <span>' . (int) $term->count . '</span></a>';
    }
    return $out . '</nav>';
}

/** Blog listing: newest article featured on page one, then a card grid with pagination. */
function cpc_render_post_list( WP_Query $query, $paged = 1, $current_term = 0 ) {
    echo '<section class="section blog-index"><div class="wrap">' . cpc_category_chips( $current_term ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in builder.
    if ( ! $query->have_posts() ) {
        echo '<p class="empty-state">No articles found.</p></div></section>';
        return;
    }
    $posts = $query->posts;
    if ( 1 === $paged && ! $current_term ) {
        $first = array_shift( $posts );
        $cat = cpc_primary_category( $first );
        echo '<article class="featured-post" data-reveal><a class="featured-link" href="' . esc_url( get_permalink( $first ) ) . '">' . cpc_card_image( $first, 'full' ) . '<div class="featured-body"><span class="eyebrow script">Latest article</span><div class="card-meta">' . ( $cat ? '<span>' . esc_html( $cat ) . '</span>' : '' ) . '<time datetime="' . esc_attr( get_the_date( DATE_W3C, $first ) ) . '">' . esc_html( get_the_date( 'j M Y', $first ) ) . '</time><span>' . esc_html( cpc_reading_time( $first ) ) . '</span></div><h2>' . esc_html( get_the_title( $first ) ) . '</h2><p>' . esc_html( cpc_excerpt( $first, 34 ) ) . '</p><span class="button light">READ ARTICLE</span></div></a></article>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    echo '<div class="post-grid">' . implode( '', array_map( 'cpc_card_html', $posts ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    $links = paginate_links( array( 'total' => $query->max_num_pages, 'current' => $paged, 'prev_text' => 'Newer', 'next_text' => 'Older' ) );
    if ( $links ) { echo '<nav class="pagination" aria-label="Blog pages">' . wp_kses_post( $links ) . '</nav>'; }
    echo '</div></section>';
}

function cpc_blog_grid() {
    $paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
    $query = new WP_Query( array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 13, 'paged' => $paged ) );
    cpc_render_post_list( $query, $paged );
    wp_reset_postdata();
}

function cpc_related_posts( $post = null, $count = 3 ) {
    $post = get_post( $post );
    $cats = wp_get_post_categories( $post->ID );
    $args = array( 'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => $count, 'post__not_in' => array( $post->ID ) );
    if ( $cats ) { $args['category__in'] = $cats; }
    $posts = get_posts( $args );
    if ( count( $posts ) < $count ) {
        $posts = array_merge( $posts, get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => $count - count( $posts ), 'post__not_in' => array_merge( array( $post->ID ), wp_list_pluck( $posts, 'ID' ) ) ) ) );
    }
    return $posts;
}
