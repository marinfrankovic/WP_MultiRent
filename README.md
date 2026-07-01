<p align="center">
	<img src="docs/assets/multirent-logo.png" alt="MultiRent logo" width="420">
</p>

# WP MultiRent

WP MultiRent contains the **Multi Apartment Rental** WordPress theme and the **MultiRent Companion** plugin.

Use the [MultiRent Complete User Guide](docs/MULTIRENT_USER_GUIDE.md) as the primary instructions file for building a WordPress rental website. It includes the full step-by-step workflow, in-text Mermaid process diagrams, and links to editable Draw.io diagrams.

MultiRent is built for apartment, room, villa, and multi-unit rental websites. It helps site owners manage listings, images, amenities, maps, QR codes, local information, menus, colors, and contact details from the WordPress dashboard without editing theme files.

- Promotional site: [https://multirent.online](https://multirent.online)
- Demo site: [https://demo.multirent.online](https://demo.multirent.online)

## What Is Included

- `MultiRent/`: WordPress theme source.
- `multirent-companion/`: companion plugin for setup screens, rental units, amenities, menus, colors, starter content, hosted demo preview links, and in-admin help.
- `release-assets/`: latest packaged ZIP files for WordPress upload.
- `docs/MULTIRENT_USER_GUIDE.md`: primary setup and user documentation.
- `docs/diagrams/`: Draw.io diagram files for the documented workflows.

## Optional Local Docker Workflow

Docker is optional. Use it when you want a private WordPress site on your own computer before working on a staging or live site.

For a full beginner workflow, including Docker Desktop, local WordPress, backups, and live restore, read the [Beginner Docker, backup, and live restore guide](BEGINNER_DOCKER_MIGRATION_GUIDE.md).

For repository-based testing where the theme and plugin source folders are mounted directly into WordPress, read [Local WordPress Docker setup](local-wordpress/README.md).

## Compatibility

Version `0.3.0` is tested with WordPress `7.0` and requires PHP `8.4` or newer. The theme and companion plugin are classic WordPress extensions; WordPress 7.0 block-editor and AI additions are optional and do not need extra setup for the public rental pages.

## Install

Use the packaged ZIP files from `release-assets/` or the [latest GitHub release](https://github.com/marinfrankovic/WP_MultiRent/releases/latest).

For the full installation and site-building workflow, follow the [MultiRent Complete User Guide](docs/MULTIRENT_USER_GUIDE.md).

## Documentation

- [MultiRent Complete User Guide](docs/MULTIRENT_USER_GUIDE.md): primary step-by-step site-building guide with in-text Mermaid process diagrams and Draw.io diagram files.
- [Beginner Docker, backup, and live restore guide](BEGINNER_DOCKER_MIGRATION_GUIDE.md): local Docker setup, backup, and live restore workflow.
- [MultiRent Companion README](multirent-companion/README.md): plugin/admin reference used by the in-admin Help / README screen.
- [Local WordPress Docker setup](local-wordpress/README.md): local commands, seeding, stopping, and volume cleanup.
- [Project notes](docs/PROJECT_NOTES.md): maintainer notes, update safety, release notes, warranty, and support notes.
- [Release policy](RELEASE_POLICY.md): maintainer packaging and release-retention notes.

The companion README is also visible inside WordPress after installation under **MultiRent Setup > Help / README**.

## License

MultiRent is free for private, non-commercial use with attribution. Commercial use requires prior written permission.

See [LICENSE.md](LICENSE.md) for the full private-use license notice.

