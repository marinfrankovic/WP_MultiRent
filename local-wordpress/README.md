# Local WordPress Docker Setup

This folder contains the local Docker setup for testing the Multi Apartment Rental theme and MultiRent Companion plugin from this repository.

The local site runs at:

```text
http://localhost:8082
```

## Start The Site

```powershell
Set-Location .\local-wordpress
docker compose up -d
```

The compose file mounts the repository source folders directly into WordPress:

- `../MultiRent` becomes the active theme source folder.
- `../multirent-companion` becomes the companion plugin source folder.

## Seed A Test Site

The seed script does not store admin credentials in this repository. Pass local-only values when running it:

```powershell
docker compose run --rm -e WP_ADMIN_USER="localadmin" -e WP_ADMIN_PASSWORD="change-this-local-password" -e WP_ADMIN_EMAIL="local@example.test" wpcli sh /seed.sh
```

Use only local test credentials. Do not use production usernames, passwords, emails, tokens, backup files, or account-specific settings in this folder.

## Stop The Site

```powershell
docker compose stop
```

## Remove Containers Only

```powershell
docker compose down
```

## Remove Containers And Local Volumes

This deletes the local WordPress database and uploaded files for this Docker setup.

```powershell
docker compose down -v
```