# WP MultiRent

WP MultiRent contains the **Multi Apartment Rental** WordPress theme and the **MultiRent Companion** plugin.

The project is designed for apartment, room, villa, and multi-unit rental websites where site owners should be able to manage the website from the WordPress dashboard without editing code.

## Repository Contents

- `MultiRent/`: installable WordPress theme source.
- `multirent-companion/`: companion plugin source for setup screens, rental units, amenities, top menu builder, color controls, recommended plugins, and in-admin README help.
- `release-assets/`: latest packaged ZIP files for WordPress upload and the combined template bundle.

## Latest Packages

- `release-assets/MultiRent-0.1.14.zip`: theme upload ZIP.
- `release-assets/multirent-companion-0.1.14.zip`: companion plugin upload ZIP.
- `release-assets/multirent-template-0.1.14.zip`: convenience bundle containing both upload ZIPs.

## Install Order

1. In WordPress admin, open **Appearance > Themes > Add New > Upload Theme**.
2. Upload and activate `MultiRent-0.1.14.zip`.
3. Open **Plugins > Add New > Upload Plugin**.
4. Upload and activate `multirent-companion-0.1.14.zip`.
5. Open **MultiRent Setup** from the WordPress left admin menu.
6. Use **Create Starter Pages, Menu, and Amenities** on a fresh site.
7. Add or edit rental units under **MultiRent Setup > Rental Units**.

## Detailed Setup Guides

For a beginner step-by-step workflow that starts with Docker Desktop, creates a local WordPress site, installs the packaged theme and companion plugin, exports a backup, and restores it on a live site, read:

- [Beginner Docker, backup, and live restore guide](BEGINNER_DOCKER_MIGRATION_GUIDE.md)

For the full no-code setup workflow, read the detailed companion plugin guide:

- [MultiRent Companion setup README](multirent-companion/README.md)

For theme-specific information, templates, header behavior, colors, apartment pages, and package notes, read:

- [Multi Apartment Rental theme README](MultiRent/README.md)

The companion README is also visible inside WordPress after installation under **MultiRent Setup > Help / README**.

## License And Commercial Use

Copyright 2026 Marin Frankovic. All rights reserved.

This code is provided for non-commercial use only. It may not be used, copied, modified, redistributed, included, bundled, adapted, sold, licensed, hosted, deployed, or otherwise used as part of any commercial project, paid product, paid service, client project, agency project, marketplace product, hosted service, SaaS product, or revenue-generating website without prior written permission from Marin Frankovic.

See [LICENSE.md](LICENSE.md) for the full non-commercial license notice.

## Privacy Boundary

This repository intentionally excludes local WordPress containers, databases, credentials, auth data, private API keys, and account-specific plugin configuration.

Do not commit:

- `.env` files or database dumps.
- WordPress `wp-config.php` files containing secrets.
- Local Docker volumes or `local-wordpress-*` folders.
- Admin usernames, passwords, tokens, cookies, private keys, or backup archives from a live site.
- Account-specific plugin configuration from production websites.

## Release Policy

GitHub Releases should contain the latest packaged theme ZIP, plugin ZIP, and combined template ZIP.

Keep only the latest 10 GitHub Releases available. When publishing future releases, delete older releases beyond the newest 10 so users see a small, current release history.

## Author

Created by Marin Frankovic.

Website: https://frankovic.net

Email: marin.frankovic@outlook.com

## Disclaimer

This theme and companion plugin are provided on a best-effort basis. No guarantees are given for correctness, compatibility, security, availability, fitness for a particular purpose, or future maintenance. The end user is responsible for everything they install, configure, publish, modify, connect, or deploy with this theme and plugin.

No free or paid support is provided.
