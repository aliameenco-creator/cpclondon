# CPC London WordPress theme

Custom theme for https://www.cpclondon.com/, rebuilt from the approved HTML redesign (original brand colour `#640c3e`, original logo, favicon and site copy).

## Install / update

- First install: upload `cpc-london.zip` from the latest [release](https://github.com/aliameenco-creator/cpclondon/releases) under Appearance → Themes → Add New → Upload Theme.
- Later versions: Appearance → **CPC London Updates** → Check for updates → Update theme. Only stable numbered releases (`vX.Y.Z`) with a verified `cpc-london.zip` asset are offered; pushes alone do not update sites.
- Updates replace theme files only. Pages, posts, media, menus and settings live in the WordPress database and are not touched.

## Homepage

The homepage is the imported page with slug `home`. The site root always renders it; `/home/` redirects to `/`. An admin notice offers a one-click Settings → Reading change so WordPress and SEO metadata treat it as the homepage.

## Where content lives

- Designed layouts (homepage, service pages, header, footer) are theme files generated from the archived CPC website, so they update with theme releases. Their copy is the original CPC copy.
- To edit a designed page's text in WordPress instead, choose the page template **Editor content (no theme layout)** for that page.
- Blog articles, legal pages and celluloid cinemas are rendered from WordPress content.
- Contact form enquiries: wp-admin → **Enquiries**. Recipient: Settings → General → "CPC contact form recipient" (default office@cpclondon.com). Hosting email delivery should be checked once with a real test enquiry; an SMTP plugin is recommended if mails do not arrive.

## Building

Generated files (`parts/*.html`, `assets/style.css`, `assets/app.js`, logo, favicon, journal fallback image) come from the redesign source in the migration workspace (`tools/build_cpc_theme.py`). `python scripts/package.py` builds `dist/cpc-london.zip` from the committed files.

Content (pages, posts, images) is imported separately with the CPC Migration Importer plugin and is not stored in this repository.
