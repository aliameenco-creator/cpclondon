<?php
/** Category, tag, search and fallback listings. */
get_header();
global $wp_query;
$cpc_title = is_category() || is_tag() ? single_term_title( '', false ) : ( is_search() ? 'Search: ' . get_search_query() : 'Blog' );
cpc_page_hero( $cpc_title, is_category() || is_tag() ? 'Blog' : '' );
if ( is_search() ) { echo '<div class="wrap search-wrap">'; get_search_form(); echo '</div>'; }
cpc_render_post_list( $wp_query, max( 1, (int) get_query_var( 'paged' ) ), is_category() ? get_queried_object_id() : -1 );
get_footer();
