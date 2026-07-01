# Changelog

All notable changes to the MultiRent theme and MultiRent Companion plugin are documented here. The theme and companion plugin share a single version number.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project uses [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.3.0] - 2026-07-01

### Added
- Companion plugin now loads its own text domain (`load_plugin_textdomain`) and both the theme and companion ship `.pot` translation templates in a `languages/` folder.
- `aria-current="page"` on the active custom navigation item for better accessibility.
- Developer tooling: `composer.json` (PHP_CodeSniffer, WordPress Coding Standards, PHPCompatibility, PHPStan), `phpcs.xml.dist`, `phpstan.neon.dist` with a baseline, and a GitHub Actions CI workflow that lints and runs static analysis on PHP 8.4 and 8.5.

### Changed
- The MultiRent Companion plugin is being reorganized into an `includes/` structure for maintainability (menu-building helpers extracted; plugin path constants added). No behavior change; all functions, hooks, and stored options are unchanged.
- The Website Setup admin screen is grouped into tabs (Homepage & Brand, Menu, Colors) for easier navigation. Settings storage and field names are unchanged.
- The **Help / README** screen is now **Help & Guides**: a list of direct links to the live website, GitHub-rendered guides (User Guide, Docker/backup guide, README), the latest release, and the public demo. Each opens in a new tab.
- Coding-standards formatting normalized across the theme and companion plugin.

### Removed
- Removed the redundant **Save Settings Only** button on Website Setup (it duplicated **Save Website Settings**). The two remaining buttons now carry clear descriptions.
- Removed the confusing empty **Plugin Placeholders** and **Page Administration** sections from Website Setup; the page-location guidance is kept as a short note near the top.

## [0.2.6] - 2026-07-01

### Fixed
- Clearing the homepage Hero eyebrow in Website Setup now correctly hides the label instead of falling back to the default text. Existing sites that never changed the field keep the default label.

## [0.2.5] - 2026-07-01

### Changed
- Internationalization: added `translators:` comments for placeholder strings and documented intentional `phpcs:ignore` annotations. Verified clean against PHP 8.4 and 8.5 with no security or correctness issues. No functional changes.

## [0.2.4] - 2026-07-01

### Added
- Hero eyebrow is now editable from MultiRent Setup > Website Setup (directly above Hero Title), in addition to the Customizer. Defaults to the previous text; leave empty to hide.

## [0.2.3] - 2026-07-01

### Added
- Hero eyebrow label above the homepage headline is now editable via the Customizer (Appearance > Customize > MultiRent Home) instead of being hardcoded.

## [0.2.2] - 2026-06-08

### Fixed
- Pages & Buttons admin: each Contact/Apartment page slot now keeps its selected WordPress page (a duplicate dropdown had reset slots 2 and 3 on save).
- Contact pages 2 and 3 are fully independent; empty slot fields are no longer inherited from the legacy global Contact settings. Contact page 1 keeps the legacy fallback.

### Changed
- Footer tagline replaced with a right-aligned "Made with MultiRent" credit; property name stays left-aligned.

## [0.2.1] - 2026-05

### Added
- Bundled local font files and a global site font selector, plus optional hero title-size control.

### Changed
- Hero Buttons admin uses page-dropdown destinations instead of literal URL fields.
- Removed the redundant homepage contact CTA band; tightened mobile hero wrapping and button layout.

## [0.2.0] - 2026-05

### Added
- Multiple apartment and contact page slots (up to three each) with migration from the earlier single Apartments/Contact settings.

## [0.1.38] and earlier

- Initial public MultiRent theme and companion plugin releases: GUI-first rental website setup, rental units, amenities, homepage sections, contact and local pages, top menu builder, color schemes, and starter content.

[Unreleased]: https://github.com/marinfrankovic/WP_MultiRent/compare/v0.3.0...HEAD
[0.3.0]: https://github.com/marinfrankovic/WP_MultiRent/releases/tag/v0.3.0
[0.2.6]: https://github.com/marinfrankovic/WP_MultiRent/releases/tag/v0.2.6
[0.2.5]: https://github.com/marinfrankovic/WP_MultiRent/releases/tag/v0.2.5
[0.2.4]: https://github.com/marinfrankovic/WP_MultiRent/releases/tag/v0.2.4
[0.2.3]: https://github.com/marinfrankovic/WP_MultiRent/releases/tag/v0.2.3
[0.2.2]: https://github.com/marinfrankovic/WP_MultiRent/releases/tag/v0.2.2
[0.2.1]: https://github.com/marinfrankovic/WP_MultiRent/releases/tag/v0.2.1
[0.2.0]: https://github.com/marinfrankovic/WP_MultiRent/releases/tag/v0.2.0
