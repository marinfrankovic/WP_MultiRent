# Release Policy

This repository publishes packaged WordPress upload ZIPs through GitHub Releases.

## Current Release Assets

Each release should include:

- `multirent-theme-upload-x.y.z.zip`: upload this in **Appearance > Themes > Add New > Upload Theme**.
- `multirent-companion-plugin-upload-x.y.z.zip`: upload this in **Plugins > Add New > Upload Plugin**.
- `multirent-complete-package-extract-first-x.y.z.zip`: extract this first; it contains the separate theme and plugin upload ZIPs.

## Retention

Keep the latest 10 releases available on GitHub.

For future releases:

1. Build and validate the theme ZIP, plugin ZIP, and extract-first complete package ZIP with `scripts/New-MultiRentReleasePackage.ps1 -Version x.y.z -CleanOldLocalPackages`.
2. Create a new GitHub release with those three ZIP files attached.
3. Mark the newest release as latest.
4. List releases newest-first.
5. Delete releases older than the newest 10.
6. Delete old local ZIP files from `release-assets/` after the new release is published so only the newest theme, companion plugin, and combined template packages remain locally.

## Local Source Safety

The release packaging script must verify these files before and after packaging:

- `MultiRent/style.css`
- `MultiRent/functions.php`
- `multirent-companion/multirent-companion.php`

If any of these files are missing, stop immediately and restore the source folders before publishing or uploading release assets. Do not manually delete, move, or clean `MultiRent/` or `multirent-companion/` during release packaging. Only remove old local ZIP files from `release-assets/`.

## Privacy

Before every release, scan source files and packaged ZIPs for credentials, local admin details, API keys, tokens, private backups, database dumps, and account-specific production settings.
