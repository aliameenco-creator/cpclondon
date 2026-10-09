<?php
/**
 * Contact page: the original CPC enquiry form plus studio details. Used for a page with slug
 * "contact", and also served at /contact when no such page has been created yet.
 */
get_header();
$cpc_contact_page = get_page_by_path( 'contact' );
$cpc_contact_page = ( $cpc_contact_page && 'publish' === $cpc_contact_page->post_status ) ? $cpc_contact_page : null;
cpc_page_hero( $cpc_contact_page ? get_the_title( $cpc_contact_page ) : 'Contact Us', 'Contact' );
$cpc_intro = $cpc_contact_page ? trim( apply_filters( 'the_content', $cpc_contact_page->post_content ) ) : '';
?>
<section class="section contact-section"><div class="wrap contact-grid">
    <div class="contact-info" data-reveal>
        <span class="eyebrow">CONTACT US</span>
        <h2>Lets discuss your next project</h2>
        <?php if ( $cpc_intro ) : ?><div class="prose"><?php echo $cpc_intro; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Filtered post content. ?></div><?php endif; ?>
        <dl class="contact-list">
            <div><dt>Address</dt><dd>Black Hangar Studios<br>Alton, GU34 5SR, UK</dd></div>
            <div><dt>Email</dt><dd><a href="mailto:office@cpclondon.com">office@cpclondon.com</a></dd></div>
            <div><dt>Phone</dt><dd><a href="tel:+441256384235">+44 (0) 1256 384235</a></dd></div>
        </dl>
        <?php echo cpc_social_links( 'socials big' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Generated theme markup. ?>
    </div>
    <div class="contact-form-card" data-reveal><?php cpc_contact_form(); ?></div>
</div></section>
<?php
get_footer();
