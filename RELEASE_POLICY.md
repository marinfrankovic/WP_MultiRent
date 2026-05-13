# Release Policy

This repository publishes packaged WordPress upload ZIPs through GitHub Releases.

## Current Release Assets

Each release should include:

- `MultiRent-x.y.z.zip`: the WordPress theme upload package.
- `multirent-companion-x.y.z.zip`: the WordPress companion plugin upload package.
- `multirent-template-x.y.z.zip`: a convenience bundle containing the theme ZIP and plugin ZIP.

## Retention

Keep the latest 10 releases available on GitHub.

For future releases:

1. Build and validate the theme ZIP, plugin ZIP, and combined template ZIP.
2. Create a new GitHub release with those three ZIP files attached.
3. Mark the newest release as latest.
4. List releases newest-first.
5. Delete releases older than the newest 10.
6. Delete old local ZIP files from `release-assets/` after the new release is published so only the newest theme, companion plugin, and combined template packages remain locally.

## Privacy

Before every release, scan source files and packaged ZIPs for credentials, local admin details, API keys, tokens, private backups, database dumps, and account-specific production settings.
