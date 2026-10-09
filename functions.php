<?php
/** CPC London theme. Layouts are theme files; pages, posts and media live in WordPress. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once __DIR__ . '/includes/github-updater.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/setup.php';
require_once __DIR__ . '/includes/blog.php';
require_once __DIR__ . '/includes/contact-form.php';

add_action( 'after_setup_theme', function () {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', array( 'width' => 2225, 'height' => 770, 'flex-width' => true, 'flex-height' => true ) );
    add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'editor-styles' );
    add_editor_style( array( 'assets/style.css', 'assets/cpc.css' ) );
    register_nav_menus( array( 'primary' => 'Primary navigation', 'footer' => 'Footer navigation' ) );
} );

add_action( 'wp_enqueue_scripts', function () {
    $version = wp_get_theme()->get( 'Version' );
    wp_enqueue_style( 'cpc-design', get_theme_file_uri( 'assets/style.css' ), array(), $version );
    wp_enqueue_style( 'cpc-wordpress', get_theme_file_uri( 'assets/wordpress.css' ), array( 'cpc-design' ), $version );
    wp_enqueue_style( 'cpc-theme', get_theme_file_uri( 'assets/cpc.css' ), array( 'cpc-wordpress' ), $version );
    wp_enqueue_script( 'cpc-theme', get_theme_file_uri( 'assets/cpc.js' ), array(), $version, array( 'strategy' => 'defer', 'in_footer' => true ) );
} );

/** Mark the document before first paint so scroll animations never hide content when JavaScript is off. */
add_action( 'wp_head', function () {
    echo "<script>document.documentElement.classList.add('js')</script>\n";
    if ( ! has_site_icon() ) {
        echo '<link rel="icon" type="image/png" sizes="16x16" href="' . esc_url( get_theme_file_uri( 'assets/cpc-london-favicon.png' ) ) . '">' . "\n";
    }
}, 1 );

/** Imported content uses root-relative links; keep them valid on subdirectory installs. */
add_filter( 'the_content', function ( $content ) {
    return preg_replace_callback( '~href="(/(?!/)[^"<>]*)"~', function ( $m ) {
        return 'href="' . esc_url( home_url( $m[1] ) ) . '"';
    }, $content );
}, 20 );
