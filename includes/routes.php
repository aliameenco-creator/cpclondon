<?php
/**
 * Original CPC addresses (/blog/<article>, /blog/<category>, /pages/cookie-policy) resolved from the
 * request path on every request. The importer's rewrite rules only work while WordPress's stored rule
 * list contains them; any later rebuild without them turned every article into a 404 (2026-10-23).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function cpc_request_path() {
    $path = trim( rawurldecode( (string) wp_parse_url( (string) ( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_PATH ) ), '/' );
    $base = trim( (string) wp_parse_url( home_url(), PHP_URL_PATH ), '/' );
    return ( $base && str_starts_with( $path, $base . '/' ) ) ? substr( $path, strlen( $base ) + 1 ) : $path;
}

/** Query vars for a preserved path, or null when the path is not one of ours. */
function cpc_route_query( $path ) {
    $paged = 0;
    if ( preg_match( '~^(.+)/page/([0-9]+)$~', $path, $m ) ) {
        $path  = $m[1];
        $paged = (int) $m[2];
    }
    $route = get_option( '_cpcmi_preserved_routes', array() )[ '/' . $path ] ?? null;
    if ( is_array( $route ) && isset( $route['post'] ) && ! $paged ) {
        $post = get_post( (int) $route['post'] );
        if ( $post ) {
            return 'page' === $post->post_type ? array( 'page_id' => $post->ID ) : array( 'p' => $post->ID, 'post_type' => $post->post_type );
        }
    }
    if ( is_array( $route ) && isset( $route['term'], $route['taxonomy'] ) && get_term( (int) $route['term'], $route['taxonomy'] ) instanceof WP_Term ) {
        $vars = 'category' === $route['taxonomy'] ? array( 'cat' => (int) $route['term'] ) : array( 'tag_id' => (int) $route['term'] );
        return $paged ? $vars + array( 'paged' => $paged ) : $vars;
    }
    // Fallback when the importer's route list is missing: /blog/<slug> is an article or a category.
    if ( preg_match( '~^blog/([^/]+)$~', $path, $m ) && ! get_page_by_path( $path ) ) {
        $slug = sanitize_title( $m[1] );
        if ( ! $paged ) {
            $found = get_posts( array( 'post_type' => 'post', 'name' => $slug, 'post_status' => array( 'publish', 'private', 'future', 'draft', 'pending' ), 'numberposts' => 1, 'fields' => 'ids', 'suppress_filters' => false ) );
            if ( $found ) { return array( 'p' => (int) $found[0], 'post_type' => 'post' ); }
        }
        $term = get_term_by( 'slug', $slug, 'category' );
        if ( $term ) { return $paged ? array( 'cat' => $term->term_id, 'paged' => $paged ) : array( 'cat' => $term->term_id ); }
    }
    return null;
}

add_filter( 'request', function ( $vars ) {
    if ( is_admin() ) { return $vars; }
    $path = cpc_request_path();
    if ( '' === $path ) { return $vars; }
    $query = cpc_route_query( $path );
    if ( null === $query ) { return $vars; }
    $GLOBALS['cpc_routed_path'] = preg_replace( '~/page/[0-9]+$~', '', $path );
    $keep = array_intersect_key( $vars, array_flip( array( 'preview', 'preview_id', 'preview_nonce', 'cpage', 'feed' ) ) );
    return array_merge( $keep, $query );
}, 5 );

// Original addresses have no trailing slash; /blog/<article>/ redirects to /blog/<article> so each has one URL.
add_action( 'template_redirect', function () {
    $routed = $GLOBALS['cpc_routed_path'] ?? '';
    if ( '' === $routed || is_404() || is_preview() || ! in_array( $_SERVER['REQUEST_METHOD'] ?? '', array( 'GET', 'HEAD' ), true ) ) { return; }
    $uri = (string) wp_parse_url( (string) ( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_PATH );
    if ( ! str_ends_with( $uri, '/' ) ) { return; }
    $query = (string) wp_parse_url( (string) ( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_QUERY );
    wp_safe_redirect( home_url( '/' . rtrim( cpc_request_path(), '/' ) ) . ( '' !== $query ? '?' . $query : '' ), 301 );
    exit;
}, 1 );

// A mistyped article address shows the 404 page instead of a fuzzy redirect to a similarly named file.
add_filter( 'strict_redirect_guess_404_permalink', '__return_true' );
