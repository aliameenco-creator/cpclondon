<?php
/** CPC London presentation. Content changes remain explicit and editable. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once __DIR__ . '/includes/github-updater.php';
add_action( 'after_setup_theme', function () {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', array( 'width' => 2225, 'height' => 770, 'flex-width' => true, 'flex-height' => true ) );
    add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'editor-styles' );
    add_editor_style( array( 'assets/style.css', 'assets/wordpress.css' ) );
    register_nav_menus( array( 'primary' => 'Primary navigation', 'footer' => 'Footer navigation' ) );
} );
add_action( 'wp_enqueue_scripts', function () {
    $version = wp_get_theme()->get( 'Version' );
    wp_enqueue_style( 'cpc-design', get_theme_file_uri( 'assets/style.css' ), array(), $version );
    wp_enqueue_style( 'cpc-wordpress', get_theme_file_uri( 'assets/wordpress.css' ), array( 'cpc-design' ), $version );
    wp_enqueue_script( 'cpc-design', get_theme_file_uri( 'assets/app.js' ), array(), $version, true );
} );
/** Resolve only our controlled pattern asset/path tokens, on any subdirectory installation. */
function cpc_theme_fragment( $html ) {
    $html = preg_replace_callback( '~(?:src|href)="(/assets/[^"<>]+)"~', function ( $m ) {
        $attr = str_starts_with( $m[0], 'src' ) ? 'src' : 'href';
        return $attr . '="' . esc_url( get_theme_file_uri( ltrim( $m[1], '/' ) ) ) . '"';
    }, $html );
    return preg_replace_callback( '~href="(/(?!/)[^"<>]*)"~', function ( $m ) {
        return 'href="' . esc_url( home_url( $m[1] ) ) . '"';
    }, $html );
}
function cpc_theme_part( $name ) {
    $allowed = array( 'header', 'footer' );
    if ( ! in_array( $name, $allowed, true ) ) { return; }
    $file = get_theme_file_path( 'parts/' . $name . '.html' );
    if ( is_readable( $file ) ) { echo cpc_theme_fragment( file_get_contents( $file ) ); } // Controlled theme HTML, not user input.
}
add_action( 'wp_head', function () {
    if ( ! has_site_icon() ) {
        echo '<link rel="icon" type="image/png" sizes="16x16" href="' . esc_url( get_theme_file_uri( 'assets/cpc-london-favicon.png' ) ) . '">' . "\n";
    }
} );
add_action( 'init', function () {
    $path = get_theme_file_path( 'patterns/homepage.html' );
    if ( is_readable( $path ) ) {
        register_block_pattern( 'cpc-london/homepage', array( 'title' => 'CPC London homepage', 'categories' => array( 'featured' ), 'content' => cpc_theme_fragment( file_get_contents( $path ) ) ) );
    }
} );
function cpc_blog_grid() {
    $paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
    $query = new WP_Query( array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 12, 'paged' => $paged ) );
    echo '<section class="section"><div class="wrap"><div class="journal-grid">';
    while ( $query->have_posts() ) { $query->the_post(); get_template_part( 'parts/card' ); }
    echo '</div><nav class="pagination" aria-label="Blog pages">' . wp_kses_post( paginate_links( array( 'total' => $query->max_num_pages, 'current' => $paged ) ) ) . '</nav></div></section>';
    wp_reset_postdata();
}

/** Imported/home content uses root-relative links; keep them valid on subdirectory installs. */
add_filter( 'the_content', function ( $content ) {
    return preg_replace_callback( '~href="(/(?!/)[^"<>]*)"~', function ( $m ) {
        return 'href="' . esc_url( home_url( $m[1] ) ) . '"';
    }, $content );
}, 20 );

/** The page used as the homepage: the Reading setting first, otherwise the imported page with slug "home". */
function cpc_home_page_id() {
    if ( 'page' === get_option( 'show_on_front' ) && (int) get_option( 'page_on_front' ) ) {
        return (int) get_option( 'page_on_front' );
    }
    $page = get_page_by_path( 'home' );
    return ( $page && 'publish' === $page->post_status ) ? (int) $page->ID : 0;
}

/** Full-width design sections (homepage, service pages) must not sit inside the narrow article column. */
function cpc_is_design_layout( $id ) {
    return str_contains( (string) get_post_field( 'post_content', $id ), '<section class="' );
}

/** One URL per page: the homepage page is only served at the site root, never at /home/. */
add_action( 'template_redirect', function () {
    $home = cpc_home_page_id();
    if ( $home && is_page( $home ) && ! is_front_page() && ! is_preview() ) {
        wp_safe_redirect( home_url( '/' ), 301 );
        exit;
    }
} );

/** Offer a one-click Reading setting so SEO metadata and WordPress itself treat the page as the homepage. */
add_action( 'admin_notices', function () {
    if ( ! current_user_can( 'manage_options' ) || 'page' === get_option( 'show_on_front' ) ) { return; }
    $page = get_page_by_path( 'home' );
    if ( ! $page ) { return; }
    $url = wp_nonce_url( admin_url( 'admin-post.php?action=cpc_set_homepage' ), 'cpc_set_homepage' );
    echo '<div class="notice notice-warning"><p><strong>CPC London:</strong> the imported homepage is not set as the site homepage yet. <a class="button button-primary" href="' . esc_url( $url ) . '">Use "' . esc_html( get_the_title( $page ) ) . '" as the homepage</a></p></div>';
} );
add_action( 'admin_post_cpc_set_homepage', function () {
    if ( ! current_user_can( 'manage_options' ) ) { wp_die( 'Not allowed.' ); }
    check_admin_referer( 'cpc_set_homepage' );
    $page = get_page_by_path( 'home' );
    if ( $page ) {
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $page->ID );
    }
    wp_safe_redirect( admin_url( 'options-reading.php' ) );
    exit;
} );
