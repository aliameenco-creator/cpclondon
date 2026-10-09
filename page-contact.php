<?php
/** Contact page (slug "contact"): the original CPC enquiry form plus studio details. */
get_header();
while ( have_posts() ) :
    the_post();
    cpc_page_hero( get_the_title(), 'Contact' );
    ?>
<section class="section contact-section"><div class="wrap contact-grid">
    <div class="contact-info" data-reveal>
        <span class="eyebrow">CONTACT US</span>
        <h2>Lets discuss your next project</h2>
        <?php if ( trim( get_the_content() ) ) : ?><div class="prose"><?php the_content(); ?></div><?php endif; ?>
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
endwhile;
get_footer();
