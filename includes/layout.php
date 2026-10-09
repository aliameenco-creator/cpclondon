<?php
/** Theme-owned layout parts generated from the archived CPC site (tools/build_cpc_theme.py). */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Resolve our controlled /assets/ tokens and root-relative links, on any (sub)directory install. */
function cpc_theme_fragment( $html ) {
    $html = preg_replace_callback( '~(src|href)="(/assets/[^"<>]+)"~', function ( $m ) {
        return $m[1] . '="' . esc_url( get_theme_file_uri( ltrim( $m[2], '/' ) ) ) . '"';
    }, $html );
    $html = preg_replace_callback( '~url\((/assets/[^)"\'<>]+)\)~', function ( $m ) {
        return 'url(' . esc_url( get_theme_file_uri( ltrim( $m[1], '/' ) ) ) . ')';
    }, $html );
    return preg_replace_callback( '~href="(/(?!/)[^"<>]*)"~', function ( $m ) {
        return 'href="' . esc_url( home_url( $m[1] ) ) . '"';
    }, $html );
}

/** Path of a generated part: header, footer, home, or pages/<slug>. Never user-controlled paths. */
function cpc_part_file( $name ) {
    if ( ! preg_match( '~^(?:header|footer|home|pages/[a-z0-9-]+)$~D', $name ) ) { return ''; }
    $file = get_theme_file_path( 'parts/' . $name . '.html' );
    return is_readable( $file ) ? $file : '';
}

function cpc_theme_part( $name ) {
    $file = cpc_part_file( $name );
    if ( ! $file ) { return; }
    $html = cpc_theme_fragment( file_get_contents( $file ) );
    if ( str_contains( $html, '<!--cpc:latest-posts-->' ) ) {
        $html = str_replace( '<!--cpc:latest-posts-->', cpc_latest_posts_html( 3 ), $html );
    }
    echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Generated theme HTML, not user input.
}

/** A designed layout for this page exists in the theme and the editor has not opted out of it. */
function cpc_page_layout( $id ) {
    if ( 'template-editor-content.php' === get_page_template_slug( $id ) ) { return ''; }
    $slug = get_post_field( 'post_name', $id );
    return cpc_part_file( 'pages/' . $slug ) ? 'pages/' . $slug : '';
}

/** Background photograph for a page hero, from the original page's title banner. */
function cpc_page_hero_image( $id ) {
    static $heroes = null;
    if ( null === $heroes ) {
        $file = get_theme_file_path( 'parts/page-heroes.json' );
        $heroes = is_readable( $file ) ? (array) json_decode( (string) file_get_contents( $file ), true ) : array();
    }
    $slug = get_post_field( 'post_name', $id );
    return isset( $heroes[ $slug ] ) ? get_theme_file_uri( ltrim( $heroes[ $slug ], '/' ) ) : '';
}

/** Page hero: title, optional eyebrow trail and the page's original banner photograph. */
function cpc_page_hero( $title, $trail = '', $image = '' ) {
    $style = $image ? ' style="background-image:url(' . esc_url( $image ) . ')"' : '';
    echo '<section class="inner-hero' . ( $image ? ' has-image' : '' ) . '"' . $style . '><div class="wrap"><div class="crumb"><a href="' . esc_url( home_url( '/' ) ) . '">Home</a>' . ( $trail ? ' / ' . wp_kses_post( $trail ) : '' ) . '</div><h1 class="reveal-title">' . esc_html( $title ) . '</h1></div></section>';
}

function cpc_social_links( $class = 'socials' ) {
    $file = get_theme_file_path( 'parts/footer.html' );
    if ( ! is_readable( $file ) || ! preg_match( '~<ul class="socials">.*?</ul>~s', (string) file_get_contents( $file ), $m ) ) { return ''; }
    return str_replace( 'class="socials"', 'class="' . esc_attr( $class ) . '"', $m[0] );
}
