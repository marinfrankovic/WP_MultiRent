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

GitHub Releases should contain the latest packaged theme ZIP, plugin ZIP, and combined template ZIP.

Keep only the latest 10 GitHub Releases available. See [Release Policy](../RELEASE_POLICY.md) for packaging and retention details.

## Warranty And Support

This theme and companion plugin are provided on a best-effort basis. No guarantees are given for correctness, compatibility, security, availability, fitness for a particular purpose, or future maintenance.

The end user is responsible for everything they install, configure, publish, modify, connect, or deploy with this theme and plugin. No free or paid support is provided.
