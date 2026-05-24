# MultiRent Project Notes

These notes keep supporting project information out of the front README while preserving important guidance for maintainers and careful site owners.

## Demo Preview

MultiRent Companion includes a **MultiRent Setup > Demo Content** screen, placed just above **Help / README** in the admin menu. The screen links to the hosted public demo at [https://demo.multirent.online](https://demo.multirent.online) instead of creating local demo content.

The public demo is hosted separately so released plugin installs stay light and do not download sample media, generate QR placeholders, or create demo pages and apartments on customer sites.

The theme uses WordPress UTF-8 output and has been checked with Croatian characters (`ČĆŽŠĐ čćžšđ`) against the local Docker `utf8mb4` database.

Do not edit demo content into real production content. Remove the demo set first, then create or import real apartments and pages.

## Update Safety

Updating MultiRent or MultiRent Companion does not delete existing WordPress content. Always create a backup before updating.

Starter content actions are intended for new sites and should not be used to reset a live site unless you understand the result. Demo content is optional and can be removed from the same setup screen.

## Privacy Boundary

This repository intentionally excludes local WordPress containers, databases, credentials, auth data, private API keys, and account-specific plugin configuration.

Do not commit:

- `.env` files or database dumps.
- WordPress `wp-config.php` files containing secrets.
- Local Docker volumes, uploaded media, databases, and runtime files.
- Admin usernames, passwords, tokens, cookies, private keys, or backup archives from a live site.
- Account-specific plugin configuration from production websites.

## Release Notes

GitHub Releases should contain the latest packaged theme ZIP, plugin ZIP, and combined template ZIP. Public install documentation should link to the latest release asset downloads for those packages.

Version 0.2.1 bundles local font files, exposes a global site font dropdown plus optional homepage hero title-size controls in Website Setup / Customizer, changes the Hero Buttons admin UI to use page dropdown destinations instead of literal URL fields, removes the redundant homepage contact CTA band, and tightens mobile hero wrapping/button layout.

Version 0.2.0 adds flexible Apartment Page 1-3 and Contact Page 1-3 slots, per-slot **Show in top menu** controls, homepage hero-button controls, explicit migration from the old single Apartments/Contact pages into Page 1 slots, and a rental-unit editor right-sidebar **Apartment Page Assignment** panel.

Version 0.1.38 marks the theme and companion plugin as tested through WordPress 7.0, refreshes public package links, and removes deprecated HTML5 `style` / `script` theme support while keeping normal asset enqueues unchanged.

Version 0.1.37 adds expanded default rental-unit amenities, including private-entry, kitchen-appliance, laundry, family, and EV-charging options, while preserving existing rental-unit amenity selections and custom terms.

Keep only the latest 10 GitHub Releases available. See [Release Policy](../RELEASE_POLICY.md) for packaging and retention details.

## Warranty And Support

This theme and companion plugin are provided on a best-effort basis. No guarantees are given for correctness, compatibility, security, availability, fitness for a particular purpose, or future maintenance.

The end user is responsible for everything they install, configure, publish, modify, connect, or deploy with this theme and plugin. No free or paid support is provided.

