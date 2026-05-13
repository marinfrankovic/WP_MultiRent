# WP MultiRent

WP MultiRent contains the **Multi Apartment Rental** WordPress theme and the **MultiRent Companion** plugin.

The project is designed for apartment, room, villa, and multi-unit rental websites where site owners should be able to manage the website from the WordPress dashboard without editing code.

## Repository Contents

- `MultiRent/`: installable WordPress theme source.
- `multirent-companion/`: companion plugin source for setup screens, rental units, amenities, top menu builder, color controls, recommended plugins, and in-admin README help.
- `local-wordpress/`: local Docker WordPress setup for testing this repository's theme and companion plugin source folders.
- `release-assets/`: latest packaged ZIP files for WordPress upload and the extract-first complete package.

## Latest Packages

- `release-assets/multirent-theme-upload-0.1.20.zip`: theme upload ZIP.
- `release-assets/multirent-companion-plugin-upload-0.1.20.zip`: companion plugin upload ZIP.
- `release-assets/multirent-complete-package-extract-first-0.1.20.zip`: extract-first package containing both upload ZIPs.

Important: upload only `multirent-theme-upload-0.1.20.zip` in **Appearance > Themes > Upload Theme**. Do not upload `multirent-complete-package-extract-first-0.1.20.zip` or GitHub's automatic source-code ZIP as a theme, because WordPress will report that the package is missing `style.css`.

## Install Order

1. In WordPress admin, open **Appearance > Themes > Add New > Upload Theme**.
2. Upload and activate `multirent-theme-upload-0.1.20.zip`.
3. Open **Plugins > Add New > Upload Plugin**.
4. Upload and activate `multirent-companion-plugin-upload-0.1.20.zip`.
5. Open **MultiRent Setup** from the WordPress left admin menu.
6. Use **Create Starter Pages, Menu, and Amenities** on a fresh site.
7. Add or edit rental units under **MultiRent Setup > Rental Units**.

## Optional Demo Content

MultiRent Companion includes a public-release admin option for optional demo content. Open **MultiRent Setup > Demo Content** and use **Create Demo Content** to enable it or **Remove Demo Content** to disable and clean it up. This is useful when you want to preview the theme before adding real property content.

The demo action creates four example apartments, generated demo hero/featured/gallery images, demo Home/Apartments/Contact/Local pages, selected amenity checkboxes, menu links, and sample contact/local-guide settings. Demo pages, apartments, and generated images are marked internally with `_multirent_demo_content=multirent-demo-content-v1`, so **Remove Demo Content** can delete only those generated examples and restore the previous homepage and MultiRent settings when available. Selected amenities appear on each apartment detail page.

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
