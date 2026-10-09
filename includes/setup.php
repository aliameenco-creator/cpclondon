<?php
/** Homepage routing and the one-click site setup (homepage Reading setting + Contact page). */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** The page used as the homepage: the Reading setting first, otherwise the imported page with slug "home". */
function cpc_home_page_id() {
    if ( 'page' === get_option( 'show_on_front' ) && (int) get_option( 'page_on_front' ) ) {
        return (int) get_option( 'page_on_front' );
    }
    $page = get_page_by_path( 'home' );
    return ( $page && 'publish' === $page->post_status ) ? (int) $page->ID : 0;
}

/** One URL per page: the homepage page is only served at the site root, never at /home/. */
add_action( 'template_redirect', function () {
    $home = cpc_home_page_id();
    if ( $home && is_page( $home ) && ! is_front_page() && ! is_preview() ) {
        wp_safe_redirect( home_url( '/' ), 301 );
        exit;
    }
} );

function cpc_setup_missing() {
    $missing = array();
    if ( 'page' !== get_option( 'show_on_front' ) && get_page_by_path( 'home' ) ) { $missing[] = 'homepage'; }
    return $missing;
}

add_action( 'admin_notices', function () {
    if ( ! current_user_can( 'manage_options' ) ) { return; }
    $missing = cpc_setup_missing();
    if ( ! $missing ) { return; }
    $labels = array( 'homepage' => 'set the imported "CPC London" page as the site homepage' );
    $url = wp_nonce_url( admin_url( 'admin-post.php?action=cpc_setup' ), 'cpc_setup' );
    echo '<div class="notice notice-warning"><p><strong>CPC London setup:</strong> ' . esc_html( implode( '; ', array_intersect_key( $labels, array_flip( $missing ) ) ) ) . '.</p><p><a class="button button-primary" href="' . esc_url( $url ) . '">Complete CPC London setup</a></p></div>';
} );

add_action( 'admin_post_cpc_setup', function () {
    if ( ! current_user_can( 'manage_options' ) ) { wp_die( 'Not allowed.' ); }
    check_admin_referer( 'cpc_setup' );
    $home = get_page_by_path( 'home' );
    if ( $home && 'page' !== get_option( 'show_on_front' ) ) {
        if ( 'publish' !== $home->post_status ) { wp_update_post( array( 'ID' => $home->ID, 'post_status' => 'publish' ) ); }
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $home->ID );
    }
    $contact = get_page_by_path( 'contact' );
    if ( ! $contact ) {
        wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Contact Us', 'post_name' => 'contact', 'post_content' => '' ) );
    } elseif ( 'publish' !== $contact->post_status ) {
        wp_update_post( array( 'ID' => $contact->ID, 'post_status' => 'publish' ) );
    }
    wp_safe_redirect( home_url( '/contact' ) );
    exit;
} );

/** /contact works before a Contact page exists: serve the contact template instead of a 404. */
function cpc_is_virtual_contact() {
    global $wp;
    return is_404() && isset( $wp->request ) && 'contact' === trim( (string) $wp->request, '/' );
}
add_filter( 'template_include', function ( $template ) {
    if ( ! cpc_is_virtual_contact() ) { return $template; }
    global $wp_query;
    $wp_query->is_404 = false;
    status_header( 200 );
    return get_theme_file_path( 'page-contact.php' );
} );
add_filter( 'pre_get_document_title', function ( $title ) {
    global $wp;
    return ( is_404() || ! have_posts() ) && isset( $wp->request ) && 'contact' === trim( (string) $wp->request, '/' ) ? 'Contact Us – ' . get_bloginfo( 'name' ) : $title;
}, 20 );
