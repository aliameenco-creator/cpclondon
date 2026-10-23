# Changelog

## 0.4.1
- Fixes every blog article, blog category page and /pages/cookie-policy showing "Page not found". The original addresses (/blog/<article>, /blog/categories/<category>) are now recognised directly by the theme on every visit, so they keep working even when WordPress rebuilds its link settings after an update.
- /blog/<article>/ (with a trailing slash) redirects to the original address without it, so each article has one address. Old /post/<article> links keep redirecting to the article.

## 0.4.0
- New fonts taken from the logo: Questrial for headings and menus (the round "cpc" letters), Arimo for text (the "35 mm. Film Printing Company" line), and a handwritten script, like "London", for a few small labels. Fonts are included in the theme; nothing loads from Google.
- Simpler header: Services (dropdown with all services), Clients, Blog and a Contact us button. FAQ's, Celluloid Cinemas, Terms & Conditions and GDPR moved to the footer. New drawn dropdown arrow.
- Buttons, cards, tiles, images, form fields and the dropdown now have softly rounded corners.
- Homepage: client logos straight under the hero, in their original colours; new FAQ's section with the first six questions from the FAQ's page.
- Client logos show in colour everywhere (Clients page, homepage).
- Page headers: lighter photo overlay with a film-strip edge taken from the logo's sprocket holes.
- Blog: large featured article, cleaner cards with category labels, softer category filters.
- Articles: the old blog's tag line at the top of some posts (a "·" followed by category names) is no longer shown; categories already appear in the article header.
- Cleaner footer: logo, socials, Services / Resources / Contact columns, current-year copyright, legal links. The contact band above it is now a rounded card ("Let’s" apostrophe fixed).
- Phones: two-column footer links, equal-width hero buttons, compact client logo strip and FAQ cards, full-width buttons where useful. Checked at 390, 360 and 320 px.

## 0.3.1
- /contact shows the contact form straight away, without needing a Contact page or the setup button.
- Phones: header no longer floats below the top when logged in; menu panel opens directly under the header.
- Phones: two-column layout where content is short (service tiles, workflow steps, step/option cards, blog cards, client logos, gallery, footer links); long-text sections stay one column. Category filters scroll sideways.
- Phones: testimonials become a swipeable one-card carousel with dots and gentle auto-advance.

## 0.3.0
- Homepage redesign from the original site's own content: animated hero headline, the eight original service photo tiles, 35mm workflow, moving testimonials (all 10 archived quotes), moving client-logo strip, the original CPC YouTube video with all five social profiles, latest blog articles.
- Subtle motion throughout: line-by-line hero text, fade-up reveals on scroll, hover lifts; all disabled for visitors who prefer reduced motion.
- Header: original menu structure with hover/tap dropdowns (More services, Resources), sticky header, animated mobile menu.
- Contact page (/contact) with the original CPC form (Name, Email, Project Title, Runtime, Delivery Address, Message; original thank-you text). Enquiries are saved under wp-admin → Enquiries and emailed to office@cpclondon.com (change under Settings → General). Spam protection without third-party services.
- Service pages (35mm prints, TrueGrain™ scanning, Moviestrips™, Film Art™, Paper on Film™ archive, Clients, FAQs) rebuilt as multi-column layouts: intro split, step/feature cards, alternating image/video rows, galleries with lightbox, FAQ accordion, logo grid, quote bands. Original banner photographs used as page heroes.
- Blog: featured latest article, category filter chips, cards with images, dates, reading time and excerpts; redesigned article template with wider typography, previous/next and related articles. Leftover Strikingly navigation, comment box and cookie text are no longer shown under articles.
- Footer with all five social profiles (Facebook, LinkedIn, X, Instagram, YouTube).
- "Complete CPC London setup" admin button sets the homepage and publishes the Contact page.
- Page template "Editor content (no theme layout)" lets an editor show the WordPress editor content instead of a designed layout.

## 0.2.0
- Homepage always renders at the site root with the full design; `/home/` redirects to `/`.
- Full-width design pages (homepage, 35mm prints) no longer render inside the narrow article column, fixing the boxed hero and invisible button text.
- Admin notice with a one-click "use as homepage" Reading setting.
- GitHub release updater (Appearance → CPC London Updates).

## 0.1.0
- Initial theme from the approved HTML design.
