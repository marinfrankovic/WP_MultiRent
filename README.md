<p align="center">
	<img src="docs/assets/multirent-logo.png" alt="MultiRent logo" width="420">
</p>

# WP MultiRent

WP MultiRent contains the **Multi Apartment Rental** WordPress theme and the **MultiRent Companion** plugin.

The project is designed for apartment, room, villa, and multi-unit rental websites where site owners should be able to manage the website from the WordPress dashboard without editing code.

## Repository Contents

- `MultiRent/`: installable WordPress theme source.
- `multirent-companion/`: companion plugin source for setup screens, rental units, amenities, top menu builder, color controls, recommended plugins, and in-admin README help.
- `docs/`: static GitHub Pages promo site for the theme and companion plugin.
- `local-wordpress/`: local Docker WordPress setup for testing this repository's theme and companion plugin source folders.
- `release-assets/`: latest packaged ZIP files for WordPress upload and the extract-first complete package.

## Promo Page

The one-page promotional site is in `docs/index.html` and is ready to publish with GitHub Pages using the repository's `docs/` folder. It links to the live demo site, latest GitHub release, source repository, and install workflow.

## Latest Packages

- `release-assets/multirent-theme-upload-0.1.31.zip`: theme upload ZIP.
- `release-assets/multirent-companion-plugin-upload-0.1.31.zip`: companion plugin upload ZIP.
- `release-assets/multirent-complete-package-extract-first-0.1.31.zip`: extract-first package containing both upload ZIPs.

Important: upload only `multirent-theme-upload-0.1.31.zip` in **Appearance > Themes > Upload Theme**. Do not upload `multirent-complete-package-extract-first-0.1.31.zip` or GitHub's automatic source-code ZIP as a theme, because WordPress will report that the package is missing `style.css`.

## Install Order

1. In WordPress admin, open **Appearance > Themes > Add New > Upload Theme**.
2. Upload and activate `multirent-theme-upload-0.1.31.zip`.
3. Open **Plugins > Add New > Upload Plugin**.
4. Upload and activate `multirent-companion-plugin-upload-0.1.31.zip`.
5. Open **MultiRent Setup** from the WordPress left admin menu.
6. Use **Create Starter Pages, Menu, and Amenities** on a fresh site.
7. Add or edit rental units under **MultiRent Setup > Rental Units**.

## Optional Demo Content

MultiRent Companion includes a public-release admin option for optional demo content. Open **MultiRent Setup > Website Setup**, find the **Demo Content** section, and use **Create Demo Content** to enable it or **Remove Demo Content** to disable and clean it up. This is useful when you want to preview the theme before adding real property content.

The demo action creates four example apartments, generated demo hero/featured/gallery/QR images, demo Home/Apartments/Contact/Local pages, selected amenity checkboxes, menu links, and sample contact/local-guide settings. Some demo apartments include YouTube video, apartment-specific map, and QR examples so users can compare richer detail-page states. Starter and demo pages, demo apartments, generated demo images, and starter rental units are assigned to the **MultiRent** WordPress author. Demo pages, apartments, and generated images are marked internally with `_multirent_demo_content=multirent-demo-content-v1`, so **Remove Demo Content** can delete only those generated examples and restore the previous homepage and MultiRent settings when available. Selected amenities appear on each apartment detail page.

The companion plugin uses a bundled MultiRent SVG icon in the WordPress left admin menu. Public site branding is controlled from **MultiRent Setup > Website Setup**: **Property name** overrides the WordPress site title in theme output, **Property tagline** overrides the WordPress tagline in the header, and **Page logo** is the only public header logo. When Page logo is empty, the header shows the property name without any logo.

Apartment units can include an optional YouTube video URL, QR code image, and apartment-specific map address or coordinates. The video appears as an item in the apartment gallery and opens in the lightbox player; the QR/map tile appears on the apartment detail page only when at least one of those fields is set. Contact pages can also include an optional QR code tile that stays hidden when no image is configured.

Apartments, Contact, and Local admin screens each include an assigned-page selector, template selector, and a preview button under the template dropdown. Users can keep multiple versions of these pages and switch which page is active for each role.

Do not edit demo content into real production content. Remove the demo set first, then create or import the real apartments and pages.

## Update Safety

Updating MultiRent or MultiRent Companion does not delete existing WordPress content. Always create a backup before updating. Starter content actions are intended for new sites and should not be used to reset a live site unless you understand the result. Demo content is optional and can be removed from the same setup screen.

## Detailed Setup Guides

For a beginner step-by-step workflow that starts with Docker Desktop, creates a local WordPress site, installs the packaged theme and companion plugin, exports a backup, and restores it on a live site, read:

- [Beginner Docker, backup, and live restore guide](BEGINNER_DOCKER_MIGRATION_GUIDE.md)

For the full no-code setup workflow, read the detailed companion plugin guide:

- [MultiRent Companion setup README](multirent-companion/README.md)

For theme-specific information, templates, header behavior, colors, apartment pages, and package notes, read:

- [Multi Apartment Rental theme README](MultiRent/README.md)

The companion README is also visible inside WordPress after installation under **MultiRent Setup > Help / README**.

## License And Commercial Use

Copyright 2026 MultiRent Project. All rights reserved.

This code is free to use and modify for private, non-commercial purposes, provided that the original author and the MultiRent project are clearly credited.

Commercial use, including client work, paid services, marketplace products, hosted services, SaaS products, agency projects, revenue-generating websites, or resale, requires prior written permission from the copyright holder.

See [LICENSE.md](LICENSE.md) for the full private-use license notice.

## Privacy Boundary

This repository intentionally excludes local WordPress containers, databases, credentials, auth data, private API keys, and account-specific plugin configuration.

Do not commit:

- `.env` files or database dumps.
- WordPress `wp-config.php` files containing secrets.
- Local Docker volumes, uploaded media, databases, and runtime files.
- Admin usernames, passwords, tokens, cookies, private keys, or backup archives from a live site.
- Account-specific plugin configuration from production websites.

## Release Policy

GitHub Releases should contain the latest packaged theme ZIP, plugin ZIP, and combined template ZIP.

Keep only the latest 10 GitHub Releases available. When publishing future releases, delete older releases beyond the newest 10 so users see a small, current release history.

## Disclaimer

This theme and companion plugin are provided on a best-effort basis. No guarantees are given for correctness, compatibility, security, availability, fitness for a particular purpose, or future maintenance. The end user is responsible for everything they install, configure, publish, modify, connect, or deploy with this theme and plugin.

No free or paid support is provided.
