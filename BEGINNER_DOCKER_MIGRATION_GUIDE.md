# Beginner Guide: Local WordPress, MultiRent, Backup, And Live Restore

This guide is for a user with no Docker or WordPress development knowledge. It explains how to create a local WordPress test site on a Windows computer, install the **Multi Apartment Rental** theme and **MultiRent Companion** plugin, back up the finished work with **All-in-One WP Migration and Backup**, and restore it on a live website.

This guide does not explain how to use every MultiRent field after installation. For the theme setup screens, use the existing theme and companion documentation.

## What You Will Create

You will create a private WordPress site on your own computer:

- Local website: `http://localhost:8082`
- Local WordPress admin: `http://localhost:8082/wp-admin`
- Database: stored inside Docker on your computer
- Theme ZIP: `multirent-theme-upload-0.1.18.zip`
- Plugin ZIP: `multirent-companion-plugin-upload-0.1.18.zip`

At the end, you will export the local site to one backup file and import that file into the live WordPress site.

## Important Safety Notes

- The local Docker site is only for your computer. Visitors on the internet cannot see it.
- Restoring a backup on the live website can overwrite the live website content, media, users, settings, plugins, and theme.
- Always make a backup of the live website before importing anything into it.
- Do not commit or publish `.wpress` backup files, database files, passwords, or `wp-config.php` files.
- Do not delete Docker volumes unless you are sure you no longer need the local WordPress site.

## Part 1: Install Docker Desktop On Windows

1. Open this page in your browser: https://www.docker.com/products/docker-desktop/
2. Download **Docker Desktop for Windows**.
3. Run the installer.
4. Keep the default options enabled, including **Use WSL 2 instead of Hyper-V** if Docker offers it.
5. Restart the computer if the installer asks you to.
6. Open **Docker Desktop** from the Start menu.
7. Wait until Docker Desktop says Docker is running.

To check that Docker works:

1. Open **PowerShell**.
2. Run:

```powershell
docker --version
docker compose version
```

Both commands should show a version number. If they do not, close and reopen PowerShell, then try again.

## Part 2: Create A Local WordPress Folder

If you are using the full WP MultiRent repository, you can use the included `local-wordpress` folder instead of creating your own folder and files. Open PowerShell in the repository and run:

```powershell
Set-Location .\local-wordpress
docker compose up -d
```

Then continue with **Part 4: Install WordPress Locally**.

If you only received the theme/plugin ZIP files and do not have the repository, create a local WordPress folder manually:

1. Create a folder somewhere easy to find, for example:

```text
C:\Users\YourName\Documents\multirent-local-wordpress
```

2. Inside that folder, create a file named `compose.yaml`.
3. Put this content into `compose.yaml`:

```yaml
services:
  db:
    image: mysql:8.0
    command: --default-authentication-plugin=mysql_native_password
    environment:
      MYSQL_DATABASE: wordpress
      MYSQL_USER: wordpress
      MYSQL_PASSWORD: wordpress
      MYSQL_ROOT_PASSWORD: wordpress
    volumes:
      - db_data:/var/lib/mysql
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost", "-pwordpress"]
      interval: 10s
      timeout: 5s
      retries: 10

  wordpress:
    image: wordpress:php8.4-apache
    depends_on:
      db:
        condition: service_healthy
    ports:
      - "8082:80"
    environment:
      WORDPRESS_DB_HOST: db:3306
      WORDPRESS_DB_USER: wordpress
      WORDPRESS_DB_PASSWORD: wordpress
      WORDPRESS_DB_NAME: wordpress
    volumes:
      - wordpress_data:/var/www/html
      - ./uploads.ini:/usr/local/etc/php/conf.d/uploads.ini:ro

volumes:
  db_data:
  wordpress_data:
```

4. In the same folder, create a file named `uploads.ini`.
5. Put this content into `uploads.ini`:

```ini
upload_max_filesize=512M
post_max_size=512M
memory_limit=512M
max_execution_time=300
max_input_time=300
```

The `uploads.ini` file helps WordPress accept larger theme, plugin, image, and migration files on the local site.

## Part 3: Start WordPress In Docker

1. Open **PowerShell**.
2. Go to the folder you created. Replace the path with your real folder path:

```powershell
Set-Location "C:\Users\YourName\Documents\multirent-local-wordpress"
```

3. Start WordPress:

```powershell
docker compose up -d
```

4. Wait until Docker downloads the images and starts the containers.
5. Check that the containers are running:

```powershell
docker compose ps
```

You should see `wordpress` and `db` running.

## Part 4: Install WordPress Locally

1. Open this address in your browser:

```text
http://localhost:8082
```

2. Choose the WordPress language.
3. Fill in the WordPress installation form:

- Site Title: your rental website name, for example `MultiRent Test`
- Username: choose a username you will remember
- Password: use a strong password
- Email: use your email address
- Search engine visibility: this does not matter much for a local site

4. Click **Install WordPress**.
5. Log in.

After login, the admin area is here:

```text
http://localhost:8082/wp-admin
```

## Part 5: Install The MultiRent Theme And Companion Plugin

Use the packaged ZIP files from the release assets:

- `multirent-theme-upload-0.1.18.zip`
- `multirent-companion-plugin-upload-0.1.18.zip`

If you received `multirent-complete-package-extract-first-0.1.18.zip`, unzip it first. It contains the separate theme and plugin ZIP files.

Important: do not upload `multirent-complete-package-extract-first-0.1.18.zip` or GitHub's automatic source-code ZIP directly as a theme. If WordPress says the theme is missing `style.css`, you selected the wrong ZIP. Choose `multirent-theme-upload-0.1.18.zip` for the theme installer.

### Install The Theme

1. In WordPress admin, open **Appearance > Themes**.
2. Click **Add New**.
3. Click **Upload Theme**.
4. Choose `multirent-theme-upload-0.1.18.zip`.
5. Click **Install Now**.
6. Click **Activate**.

### Install The Companion Plugin

1. In WordPress admin, open **Plugins > Add New**.
2. Click **Upload Plugin**.
3. Choose `multirent-companion-plugin-upload-0.1.18.zip`.
4. Click **Install Now**.
5. Click **Activate Plugin**.

### Create Starter Content

1. In the left WordPress admin menu, open **MultiRent Setup**.
2. Click **Create Starter Pages, Menu, and Amenities** if this is a fresh site.
3. Create the rental units you need.
4. Open **Settings > Permalinks**.
5. Click **Save Changes** once. You do not need to change anything on the page.

Now open the local site:

```text
http://localhost:8082
```

Use the existing MultiRent theme and companion guides for the normal site editing work.

## Part 6: Stop And Start The Local Site Later

When you are done working for the day, you can stop the local site:

```powershell
Set-Location "C:\Users\YourName\Documents\multirent-local-wordpress"
docker compose stop
```

To start it again later:

```powershell
Set-Location "C:\Users\YourName\Documents\multirent-local-wordpress"
docker compose up -d
```

Then open:

```text
http://localhost:8082
```

## Part 7: Install All-in-One WP Migration And Backup Locally

1. In the local WordPress admin, open **Plugins > Add New**.
2. Search for **All-in-One WP Migration and Backup**.
3. Install the plugin by **ServMask**.
4. Activate it.

## Part 8: Export A Backup From The Local Site

1. In the local WordPress admin, open **All-in-One WP Migration > Export**.
2. Choose **Export To > File**.
3. Wait for the export to finish.
4. Download the `.wpress` file to your computer.
5. Store it somewhere private, for example:

```text
Documents\MultiRent Backups\local-multirent-before-live-restore.wpress
```

Do not store this file inside a public repository. It can contain website content, users, media, settings, and private data.

## Part 9: Prepare The Live WordPress Site

Before restoring the local backup, prepare the live site.

1. Log in to the live WordPress admin.
2. Create a full backup of the current live site with your hosting provider or an existing backup plugin.
3. Confirm you know how to restore that live backup if something goes wrong.
4. Update WordPress core if needed.
5. Make sure the live site uses a compatible PHP version. PHP 8.2, 8.3, or 8.4 is recommended.
6. Check the live site's upload limit. The backup import file must be smaller than the allowed upload size.
7. Install and activate **All-in-One WP Migration and Backup** on the live site.

If the live site upload limit is too small, use one of these options:

- Ask the hosting provider to increase `upload_max_filesize`, `post_max_size`, `memory_limit`, and execution time.
- Use the official All-in-One WP Migration extension if your backup size requires it.
- Import through a hosting file manager or hosting migration tool if your host supports that workflow.

## Part 10: Restore The Local Backup On The Live Site

Only continue when you have a backup of the current live site.

1. In the live WordPress admin, open **All-in-One WP Migration > Import**.
2. Choose **Import From > File**.
3. Select the `.wpress` file exported from the local Docker site.
4. Wait for the upload and restore process to finish.
5. Confirm the warning that the import will overwrite the live site.
6. When the plugin asks you to save permalinks, click the link to open **Settings > Permalinks**.
7. Click **Save Changes** twice.
8. Log in again if WordPress asks you to.

Important: after import, the live site's WordPress users may be replaced by the users from the local backup. Use the admin username and password from the local site if your old live login no longer works.

## Part 11: Live Site Checklist After Restore

After the restore, check these items before considering the live site finished.

### Website Address And SSL

1. Open the public domain in a private browser window.
2. Confirm the address uses `https://`.
3. In WordPress admin, open **Settings > General**.
4. Confirm **WordPress Address** and **Site Address** show the live domain, not `localhost:8082`.
5. If the site still redirects to localhost, run a search-and-replace tool or ask the host to replace `http://localhost:8082` with the live domain.

### Permalinks

1. Open **Settings > Permalinks**.
2. Choose the permalink style you want, usually **Post name**.
3. Click **Save Changes**.
4. Open several apartment pages and menu links to confirm they do not show 404 errors.

### Theme And Plugin

1. Open **Appearance > Themes**.
2. Confirm **Multi Apartment Rental** is active.
3. Open **Plugins**.
4. Confirm **MultiRent Companion** is active.
5. Open **MultiRent Setup** and confirm your property settings, rental units, images, colors, and menu links are present.

### Images And Media

1. Open the homepage.
2. Check the hero image.
3. Check every apartment image.
4. Open **Media > Library** and confirm images load correctly.
5. Replace any missing image from the live WordPress media library.

### Contact, Booking, Maps, And Reviews

1. Test all booking buttons and external booking links.
2. Test the contact page and contact form, if one is used.
3. Check that form notification emails arrive.
4. Check map embeds, Google reviews, review shortcodes, analytics scripts, and cookie banners.
5. Reconnect any plugin account that does not work after migration.

### Users And Passwords

1. Open **Users > All Users**.
2. Remove test users that should not exist on the live site.
3. Change weak local passwords.
4. Make sure the real site owner has an administrator account.
5. Update the admin email address if needed.

### SEO And Search Visibility

1. Open **Settings > Reading**.
2. Make sure **Discourage search engines from indexing this site** is not enabled on the live site.
3. Check SEO plugin titles and descriptions if an SEO plugin is installed.
4. Submit or refresh the sitemap in Google Search Console if the site uses it.

### Cache And Performance

1. Clear the WordPress cache plugin if one is installed.
2. Clear hosting cache or CDN cache if your host provides it.
3. Open the site in a private browser window and check the homepage again.
4. Test the site on a phone, not only on a desktop computer.

### Final Backup

1. When the live site looks correct, create a new backup of the finished live site.
2. Store the backup somewhere private.
3. Keep a note of the date, domain, theme version, plugin version, and backup file name.

## Common Problems

### Docker Says Port 8082 Is Already Used

Another local app is already using port `8082`. In `compose.yaml`, change this line:

```yaml
- "8082:80"
```

For example, use:

```yaml
- "8083:80"
```

Then restart Docker Compose:

```powershell
docker compose down
docker compose up -d
```

The local site will then be:

```text
http://localhost:8083
```

### WordPress Upload Is Too Small Locally

Check that `uploads.ini` exists in the same folder as `compose.yaml`, then restart:

```powershell
docker compose restart wordpress
```

### The Live Site Shows 404 Errors After Import

Open **Settings > Permalinks** and click **Save Changes** twice.

### The Live Site Still Shows Localhost Links

The migration did not fully replace local URLs. Replace:

```text
http://localhost:8082
```

with the real live domain, for example:

```text
https://example.com
```

Use a trusted WordPress search-and-replace plugin, WP-CLI, or your hosting provider's migration tool.

### You Cannot Log In After Restore

The import may have replaced the live users with the users from the local site. Try the local WordPress admin username and password. If that does not work, use the hosting provider's WordPress password reset or database user reset tool.

## Clean Up Local Docker Site Only When Finished

Stopping Docker keeps your local work:

```powershell
docker compose stop
```

This command removes the containers but keeps the database and WordPress files in Docker volumes:

```powershell
docker compose down
```

This command deletes the local database and WordPress files. Use it only when you are sure you have exported and saved everything you need:

```powershell
docker compose down -v
```