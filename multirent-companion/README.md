# MultiRent Companion

MultiRent Companion is the setup and content-management plugin for the **Multi Apartment Rental** WordPress theme.

The plugin is designed for rental-property owners who want to manage apartments, images, menus, colors, review sections, and starter pages from the WordPress dashboard without editing theme files.

## Install Order

1. Install and activate [multirent-theme-upload-0.1.38.zip](https://github.com/marinfrankovic/WP_MultiRent/releases/download/v0.1.38/multirent-theme-upload-0.1.38.zip) in **Appearance > Themes > Add New > Upload Theme**.
2. Install and activate [multirent-companion-plugin-upload-0.1.38.zip](https://github.com/marinfrankovic/WP_MultiRent/releases/download/v0.1.38/multirent-companion-plugin-upload-0.1.38.zip) in **Plugins > Add New > Upload Plugin**.
3. Open **MultiRent Setup** in the left WordPress admin menu.
4. Click **Create Starter Pages, Menu, Amenities, and Rental Units** if this is a fresh site. This creates the Apartments page and four starter rental units.
5. Rename, add, or edit each rental unit under **MultiRent Setup > Rental Units**.
6. Review **Settings > Permalinks** and click **Save Changes** once if rental links do not open correctly.

For convenience, [multirent-complete-package-extract-first-0.1.38.zip](https://github.com/marinfrankovic/WP_MultiRent/releases/download/v0.1.38/multirent-complete-package-extract-first-0.1.38.zip) contains both the theme ZIP and plugin ZIP. Extract it first, then upload the theme and plugin separately in the order above.

## What This Plugin Adds

### MultiRent Setup Admin Area

The plugin creates a dedicated **MultiRent Setup** menu in WordPress admin. Rental-related admin tasks are grouped there so users do not need to search through many WordPress screens.

The **MultiRent Setup** menu uses the bundled MultiRent SVG icon in the WordPress left admin menu.

Grouped items include:

- Website Setup
- Rental Units
- Apartments Page
- Contact Page
- Local Page
- Amenities
- Demo Content
- Help / README

The submenu order is intentionally kept simple: **Website Setup** first, **Rental Units** second, then separate page administration screens, rental tools such as **Amenities**, **Demo Content** above **Help / README**, and **Help / README** last.

The **Help / README** screen shows this README file inside WordPress admin so site owners can always find setup instructions from the left navigation pane.

### Website Setup

The **Website Setup** screen starts with **Starter Content** for fresh sites, then controls homepage and brand content:

- Property name
- Property tagline
- Optional page logo shown left of the property name
- Homepage section visibility checkboxes
- Hero title and hero text
- Hero button label and link
- Homepage intro eyebrow, title, and text
- Stats lines, such as unit count, distance, or service notes
- Homepage apartment-card number of apartments to show
- Reviews shortcode field
- Contact call-to-action title, text, button label, and button link

The landing page is the only mandatory page. Apartments, Contact, and Local pages are optional and have their own administration screens.

#### Website Setup Field Reference

- **Property name**: public property or rental-business name shown in the header and key theme areas. It takes precedence over the default WordPress Site Title in theme output.
- **Property tagline**: optional short tagline shown under the property name in the site header. It takes precedence over the default WordPress Tagline in the theme header.
- **Page logo**: optional image shown to the left of the property name in the site header. Leave empty to show the property name without any logo.
- **Homepage section visibility**: checkboxes that show or hide full homepage blocks: Hero, Intro, Stats, Apartment cards, Reviews, admin-only SEO reminder, admin-only backup reminder, and Contact call-to-action. Hidden blocks keep their saved text and image settings so they can be turned back on later.
- **Hero title**: main landing-page headline. Use a short phrase that explains the stay you offer.
- **Hero text**: short landing-page introduction below the headline.
- **Hero image**: large visual image used on the landing page hero.
- **Hero button text**: text shown on the main landing-page call-to-action button.
- **Hero button URL**: where the hero button opens. Use a page path such as `/apartments/` or a full URL.
- **Intro eyebrow**: small label shown above the landing-page intro heading.
- **Intro title**: heading for the landing-page intro section below the hero.
- **Intro text**: text for the landing-page intro section.
- **Stats lines**: landing-page facts, one per line, using `value | label`, for example `4 | Apartments`.
- **Apartment cards checkbox**: shows or hides rental-unit cards on the front page.
- **Number of apartments to show**: limits the homepage rental cards from 1 to 50. Use the Apartments page for the complete list.
- **Reviews shortcode**: shortcode from a reviews plugin. The Reviews section checkbox must also be enabled in Homepage section visibility.
- **SEO reminder checkbox**: shows a private admin-only reminder to configure SEO metadata.
- **Backup reminder checkbox**: shows a private admin-only reminder to create backups before major changes.
- **Contact title/text/button fields**: content for the landing-page contact call-to-action band, not the full Contact page. The Contact call-to-action checkbox controls whether this band appears.
- **Header menu links**: top menu links, one per line, using `Label | URL`.
- **Color scheme**: preset color palette used by the theme.
- **Use custom colors**: enables the custom color pickers.
- **Custom colors**: primary, dark, surface, and accent colors used across the theme.

### Separate Page Administration

Optional pages are managed from separate left-menu screens under **MultiRent Setup**:

- **Apartments Page**: choose which WordPress page is the Apartments page, show or hide it, choose an Apartments template, and open the assigned page preview from the template section.
- **Contact Page**: choose which WordPress page is the Contact page, show or hide it, choose a Contact template, open the assigned page preview from the template section, and control contact details, booking checklist, map, optional QR code tile, page content, form shortcode, and map note.
- **Local Page**: choose which WordPress page is the Local page, show or hide it, choose a Local template, open the assigned page preview from the template section, and control guide cards, highlights, activities, useful links, and page content.

When an optional page is hidden, MultiRent changes that page to draft and removes it from the generated top menu. The Home/Landing page remains mandatory.

### Page Templates Created By Starter Content

The starter-content button creates sample pages under the **MultiRent** WordPress author and assigns useful templates:

- **Apartments** uses the **Apartments - Grid** template.
- **Contact** uses the **Contact / Booking Inquiry** template.
- **Local** uses the **Local Information** template.

The theme also includes **Apartments - Featured Guide** and **Apartments - Compact List** templates. Users can switch the Apartments page template from **MultiRent Setup > Apartments Page**.
Contact pages can use **Contact / Booking Inquiry**, **Contact - Split Map**, or **Contact - Compact**. Local pages can use **Local Information**, **Local - Compact Guide**, or **Local - Featured Guide**.

### Adding And Editing Pages

Users can add normal WordPress pages for extra content, such as house rules, booking terms, gallery pages, nearby restaurants, or seasonal offers.

To add a new page:

1. Open **Pages > Add New**.
2. Enter the page title.
3. Add text, headings, lists, images, buttons, or other blocks in the editor.
4. For Apartments, Contact, and Local pages, choose the assigned WordPress page and template from the matching MultiRent Setup admin screen and use the preview button under the template dropdown when you want to open the assigned page.
5. Click **Publish**.

To edit text on any page:

1. Open **Pages > All Pages**.
2. Click the page title or **Edit**.
3. Click inside the text block you want to change.
4. Edit the text directly in the WordPress editor.
5. Use the **+** button to add Paragraph, Heading, List, Button, Image, or Columns blocks.
6. Click **Update**.

Some page sections are controlled from **MultiRent Setup** instead of the page editor. Homepage text is edited in **MultiRent Setup > Website Setup**. Contact page fields are edited in **MultiRent Setup > Contact Page**. Local page fields are edited in **MultiRent Setup > Local Page**.

### Adding Images To Pages

To add an image inside any page:

1. Open the page in **Pages > All Pages**.
2. Click where the image should appear.
3. Click the **+** button and choose **Image**.
4. Choose **Upload** for a new image or **Media Library** for an existing image.
5. Add useful alt text in the image settings for accessibility and SEO.
6. Adjust alignment, size, or caption if needed.
7. Click **Update**.

For apartment card images, use **MultiRent Setup > Rental Units**, open the apartment, and use **Apartment Images > Apartment Tile Image** below the editor. The duplicate WordPress **Featured image** sidebar panel is hidden for rental units to avoid confusion.

For the homepage hero image, use **MultiRent Setup > Website Setup > Hero image**.

### Linking An Image To A URL

To make an image clickable:

1. Open the page editor.
2. Click the image block.
3. Click the link icon in the block toolbar.
4. Paste the target URL, such as `/contact/`, `/apartments/`, or a full external URL.
5. Press **Enter** or click **Apply**.
6. For external links, optionally enable opening in a new tab from the link settings.
7. Click **Update**.

### Ordering Pages In The Menu

The easiest way to control the top navigation order is the **Top Menu Builder** in **MultiRent Setup > Website Setup**.

Add one menu item per line in the exact order it should appear:

```text
Home | /
Apartments | /apartments/
Local | /local/
Contact | /contact/
Booking Terms | /booking-terms/
```

Click **Save and Apply Top Menu** after editing the list.

Users who prefer the standard WordPress menu editor can use **Appearance > Menus** instead. Create or edit a menu, drag items into the preferred order, assign it to the **Primary menu** location, and click **Save Menu**.

### Hero Image Picker

The setup page includes a WordPress media picker for the homepage hero image. The user can choose or remove the hero image from the GUI without touching theme files.

### Plugin Placeholders

MultiRent does not automatically install third-party plugins. This avoids filesystem permission errors such as failed backup-folder creation and keeps the template portable.

The setup page provides checkboxes that control whether optional plugin-related areas appear in the theme:

- **Google Reviews**: shows the homepage reviews section only when this checkbox is enabled and a shortcode is entered.
- **SEO reminder**: shows an admin-only reminder on the frontend for logged-in site managers.
- **Backup reminder**: shows an admin-only reminder to create a backup before migrations or large edits.

Visitors do not see the SEO or backup reminders.

### Top Menu Builder

The top menu builder lets users create a header menu using one line per link:

```text
Home | /
Rentals | /rentals/
Contact | #contact
Book Now | https://example.com/book
```

Click **Save and Apply Top Menu** to create or update the **MultiRent Top Menu** and assign it to the theme header.

### Contact Page Builder

The Contact Page Builder is available from **MultiRent Setup > Contact Page**. It lets users choose which WordPress page is the Contact page, choose between Contact templates, and open the assigned page preview from the template section.

Editable fields include:

- Contact page title and intro
- Address
- Phone and mobile phone
- Email
- Contact form shortcode
- Map search query
- Map or arrival note
- Optional Contact QR code image
- Booking-help checklist lines

The Contact page visibility checkboxes are grouped at the top of the screen. The user can show or hide each contact page area with checkboxes:

- Contact details card
- Booking inquiry checklist
- Map iframe
- Page editor content
- Form shortcode area
- Map or arrival note

#### Contact Page Field Reference

- **Show Contact page**: publishes or drafts the Contact page and controls whether it is included in the generated top menu.
- **Contact page title**: main heading shown at the top of the Contact page.
- **Contact page intro**: intro text shown under the Contact page heading.
- **Contact address**: address block shown in the contact details card. Use one line per address line.
- **Contact phone**: primary phone number. Leave empty to hide it.
- **Contact mobile**: mobile phone number. Leave empty to hide it.
- **Contact email**: email address shown on the Contact page. Leave empty to hide it.
- **Contact form shortcode**: shortcode from a contact-form plugin, for example `[contact-form-7 id="123"]`. Leave empty if no form is used.
- **Contact map query**: search text used to build the embedded Google map. Enter a property name, street address, city, and country. It is not a special code.
- **Contact map note**: short note below the map, useful for parking, arrival instructions, or map corrections.
- **Contact QR code image**: optional image shown as a QR tile on Contact templates. Leave empty to hide the tile.
- **Booking help lines**: checklist shown to guests before they send an inquiry. Add one requested detail per line.
- **Show contact details card**: shows or hides the address/phone/email card.
- **Show booking inquiry checklist**: shows or hides the checklist of details guests should send.
- **Show map iframe**: shows or hides the embedded Google map.
- **Show page editor content**: shows or hides content written in the normal WordPress page editor.
- **Show form shortcode area**: shows or hides the contact-form shortcode output.
- **Show map or arrival note**: shows or hides the map note text.

### Local Page Builder

The Local Page Builder is available from **MultiRent Setup > Local Page**. It lets users choose which WordPress page is the Local page, choose between Local templates, and open the assigned page preview from the template section.

Editable fields include:

- Local page title and intro
- Guest guide lines
- Local highlight lines
- Activity lines
- Useful link lines

Use this format for guide, highlight, and activity rows:

```text
Beach | Five minutes on foot with shallow water nearby.
Restaurants | Add nearby places guests usually ask about.
```

Use this format for links:

```text
Nearest airport | https://example.com
Local tourism board | https://example.com
```

The Local page visibility checkboxes are grouped at the top of the screen. The user can show or hide each local page area with checkboxes:

- Guest guide cards
- Local highlights
- Trips and activities
- Useful links sidebar
- Page editor content

#### Local Page Field Reference

- **Show Local page**: publishes or drafts the Local page and controls whether it is included in the generated top menu.
- **Local page title**: main heading shown at the top of the Local page.
- **Local page intro**: intro text shown under the Local page heading.
- **Local guide lines**: guest guide cards, one per line, using `Title | text`.
- **Local highlight lines**: quick local highlights, one per line, using `Title | text`.
- **Local activity lines**: activity cards, one per line, using `Title | text`.
- **Local link lines**: useful links, one per line, using `Label | URL`.
- **Show guide cards**: shows or hides the main guest guide grid.
- **Show local highlights**: shows or hides local highlight cards.
- **Show trips and activities**: shows or hides activity cards.
- **Show useful links sidebar**: shows or hides the link sidebar.
- **Show page editor content**: shows or hides content written in the normal WordPress page editor.

### Color Scheme Controls

Users can choose a preset color scheme or enable custom colors.

Available presets:

- Coastal Blue
- Olive Garden
- Coral Sunset
- Graphite

When **Use custom colors** is enabled, the user can set custom primary, dark, surface, and accent colors from WordPress admin.

### Starter Content

The plugin can create starter pages, a starter menu, starter amenities, and four starter rental units in one action.

Use this on a new site to get a working structure quickly, then rename, add, or edit the starter rental units and replace the placeholder text and images with real property content.

### Optional Demo Preview

The plugin includes a **MultiRent Setup > Demo Content** screen with a link to the hosted public demo at [https://demo.multirent.online](https://demo.multirent.online). The demo is hosted separately so individual WordPress sites stay light and do not download sample media, generated QR images, demo pages, or demo apartments.

Use the public demo to preview apartment pages, galleries, maps, QR examples, contact details, local guide sections, and the finished theme layout before adding real property content. Use **Website Setup** on the local site to create starter pages, menu links, amenities, and rental units.

The theme and companion plugin preserve Croatian characters such as `č`, `ć`, `ž`, `š`, and `đ` in settings, titles, rental content, and menus on standard WordPress installations.

Starter and demo pages, generated demo apartments, generated demo images, and starter rental units are assigned to the **MultiRent** WordPress author so packaged example content is clearly separated from real site-owner content.

### Rental Units

Rental Units are stored as a custom post type. Keeping rental data in the plugin means apartment content remains available even if the theme is changed later.

Each rental unit supports:

- Title
- Main content
- Short excerpt for cards
- Apartment tile image / featured image
- Capacity
- Bedrooms
- Bathrooms
- Size
- Optional price note
- Booking URL
- YouTube video URL
- QR code image
- Apartment map address or coordinates
- Amenities

#### Add A New Apartment End To End

Use this workflow whenever you want to add one complete apartment from the WordPress dashboard.

1. Open **MultiRent Setup > Rental Units**.
2. Click **Add New**.
3. Enter the apartment name in the title field, for example `Apartment A1` or `Sea View Studio`.
4. Add the full apartment description in the main editor. Include room layout, balcony/terrace notes, kitchen details, accessibility notes, house rules, or anything guests should know before booking.
5. Add a short summary in the **Excerpt** field if it is visible. This text is used on apartment cards. If Excerpt is not visible, open **Screen Options** at the top of the editor and enable **Excerpt**.
6. In the auto-expanded **Apartment Images** box below the editor, use **Apartment Tile Image** to select the main apartment photo from the Media Library or upload a new one. This is the apartment tile image used on cards and apartment pages.
7. In the **Apartment Gallery Images** box below the editor, choose the detail-page gallery photos, reorder them with **Move up** and **Move down**, and remove any images you do not want.
8. Add optional extra apartment photos in the main editor with **Image** or **Gallery** blocks only when those photos should appear inside the written apartment description.
9. In the right sidebar, open **Apartment Details** and fill in **Guest capacity**, **Bedrooms**, **Bathrooms**, **Size**, optional **Price note**, **Booking or inquiry URL**, optional **YouTube video URL**, optional **Apartment map address/coordinates**, and optional **QR code image**. Guest capacity appears on apartment cards and detail pages; public summary badges display only the number or range. Leave price note empty to hide the price tile. If a YouTube URL is set, it appears as part of the apartment gallery and opens in the lightbox player. If QR or map fields are set, a compact QR/map tile appears under the details tile; when all QR/map fields are empty, that tile is hidden.
10. In the **Amenities** checkbox list, select the amenities for that apartment. The standard options are Parking, WiFi, Balcony, Bathroom, Air Condition, TV, Sat TV, BBQ, Terrace, No-Smoking, Kitchen, Separate entrance, Dishwasher, Coffee machine, Microwave, Washing machine, Iron, Hair dryer, Baby cot, EV charger, Pets allowed, and Pets not allowed.
11. Click **Publish** to make the apartment live, or **Save Draft** if it is not ready yet.
12. Open the public apartment page from the **View** link and check the image, gallery order, description, details, amenities, and booking button.
13. Open the Apartments page on the public site and confirm the new apartment card appears in the list.
14. If the apartment page returns a 404 error, open **Settings > Permalinks** and click **Save Changes** once, then test again.

Recommended content checklist for each apartment:

- One clear apartment title.
- One strong tile image in landscape orientation.
- Detail-page gallery photos selected in Apartment Gallery Images and ordered correctly.
- Optional extra photos added as Image or Gallery blocks in the main editor only when they belong inside the written description.
- A short card summary in the Excerpt field.
- A complete main description.
- Capacity, bedrooms, bathrooms, size, and optional price note.
- A booking or inquiry URL, usually `/contact/` or a direct booking link.
- Optional YouTube video URL when you want the apartment gallery to include a playable video.
- Optional QR code image and apartment-specific map address or coordinates when that apartment needs its own compact QR/map tile.
- Relevant amenities selected.
- Alt text on uploaded photos so the site is easier to use and better for SEO.

To edit an existing apartment later, open **MultiRent Setup > Rental Units**, click the apartment title, make changes, and click **Update**.

To hide an apartment without deleting it, open the apartment editor and change its status from **Published** to **Draft**. Draft apartments are not shown on the public listing.

#### Add Images To An Apartment Page

There are three image areas for each apartment:

- **Apartment Tile Image**: the main apartment image. It appears on apartment cards, previews, and the top of the single apartment page.
- **Apartment Gallery Images**: the managed gallery shown beside the Details tile on the apartment detail page. Choose multiple images, reorder them with **Move up** and **Move down**, and remove images without deleting them from the Media Library.
- **YouTube video URL**: optional video link stored in Apartment Details. When present, the video appears in the same gallery as the photos and opens in the lightbox player.
- **QR code image**: optional image shown in the apartment QR/map tile. Leave empty to hide the QR section.
- **Apartment map address/coordinates**: optional map fields shown in the apartment QR/map tile. Coordinates such as `43.2039, 17.1364` take priority over the address. If no QR image, address, or coordinates are set, the entire tile is hidden.
- **Image or Gallery blocks in the main editor**: optional photos shown inside the written apartment description only, such as a floor plan or a captioned detail.
- **Attached apartment images fallback**: older apartments still show attached images in the gallery if no Apartment Gallery Images have been selected.
- **Apartment Details**: the single apartment page organizes details as a booking-style panel with optional price first, guests/bedrooms/bathrooms as clean quick facts, then size, amenities, and the inquiry button. If the price note is empty, the price tile is hidden.

To add extra photos to one apartment page:

1. Open **MultiRent Setup > Rental Units**.
2. Click the apartment you want to edit.
3. Find the auto-expanded **Apartment Images** box below the main editor, then use its **Apartment Gallery Images** section.
4. Click **Choose gallery images**.
5. Choose **Upload** for new photos or **Media Library** for existing photos, select all gallery photos, and click **Use selected images**.
6. Use **Move up** and **Move down** on each thumbnail until the public gallery order is correct.
7. Use **Remove** on a thumbnail to remove it from this apartment gallery without deleting the file from the Media Library.
8. Add helpful alt text in the Media Library for each photo, for example `Apartment A1 balcony sea view`.
9. Click **Update**.
10. Open the public apartment page and confirm the photos appear in the right order.

Use **Apartment Tile Image** only for the main apartment/card image. Use **Apartment Gallery Images** for the detail-page gallery. Use **Image** or **Gallery** blocks only for photos that should appear inside the written apartment description.

### Apartment Details And Images

Each rental unit edit screen keeps image controls and text/detail controls separate. The auto-expanded **Apartment Images** box below the main editor is for the apartment tile image and ordered gallery photos only. Capacity, bedrooms, bathrooms, size, price note, booking URL, YouTube video URL, QR code image, and apartment map fields are edited in the right sidebar under **Apartment Details**.

Apartment Details fields are saved as rental-unit custom fields. Amenities are saved separately as taxonomy terms, so an amenity checkbox can save even when a custom-field problem affects details such as capacity, bedrooms, price note, video URL, QR image, or map address. After editing Apartment Details, click **Update** and refresh the public apartment page to confirm the details card and QR/map tile use the new values.

The apartment tile image is stored as the normal WordPress featured image internally, but site owners manage it from **Apartment Images > Apartment Tile Image** below the editor. It appears in rental cards and single rental pages. The detail-page gallery is controlled by the **Apartment Gallery Images** section in the same box.

#### Apartment Field Reference

- **Apartment Tile Image**: main image used on apartment cards, previews, and the top of the apartment page. It is stored as the WordPress featured image internally, but rental editors manage it from the **Apartment Images** box.
- **Apartment Gallery Images**: comma-separated image IDs are stored internally for the ordered detail-page gallery. Site owners manage this with the gallery picker and **Move up** / **Move down** controls, not by editing IDs manually.
- **Guest capacity**: guest count or range for apartment cards and detail pages, such as `2 guests`, `2-4 guests`, or `up to 6 guests`. Public summary badges display only the number or range.
- **Bedrooms**: number or description of bedrooms, such as `2` or `Studio`.
- **Bathrooms**: number or description of bathrooms.
- **Size**: apartment size, such as `45 m2`.
- **Price note**: optional short price message, such as `On request`, `From 90 EUR`, or `Seasonal rates`. Leave empty to hide the price tile on the apartment detail page.
- **Booking or inquiry URL**: link for the apartment booking/inquiry button. Use a full URL or a site page path.
- **YouTube video URL**: optional YouTube link. Supported formats include normal YouTube watch links, short `youtu.be` links, embed links, Shorts, and live links. If valid, the video is added to the public apartment gallery and plays in the lightbox.
- **QR code image**: optional attachment ID stored internally and selected from the right-sidebar **Apartment Details** UI. It appears in the compact QR/map tile only when present and is excluded from the apartment gallery.
- **Apartment map address**: optional address for the apartment-specific map.
- **Apartment map coordinates**: optional `latitude, longitude` pair for the apartment-specific map. Coordinates override the address when both are set.

### Amenities

Amenities are grouped under **MultiRent Setup > Amenities** and appear as checkboxes on each rental unit. The companion plugin creates the standard amenity choices automatically: Parking, WiFi, Balcony, Bathroom, Air Condition, TV, Sat TV, BBQ, Terrace, No-Smoking, Kitchen, Separate entrance, Dishwasher, Coffee machine, Microwave, Washing machine, Iron, Hair dryer, Baby cot, EV charger, Pets allowed, and Pets not allowed.

When an amenity checkbox is selected for an apartment, the theme displays that amenity as an icon badge on the single apartment detail page. Apartment listing cards stay compact and show the apartment image, title, excerpt, guest capacity, and details link. New default amenity choices are inserted only when missing, so existing rental-unit amenity selections and custom amenity terms are preserved. Older starter terms such as `Wi-Fi` and `Air conditioning` are migrated to the newer checkbox terms when possible, and unused old starter terms are cleaned up.

## Recommended Plugins

These plugins are optional. Install them manually from **Plugins > Add New** only if the site needs them. This recommendation list is based on the plugins validated during local rental-site testing.

- **CookieYes - Cookie Banner for Cookie Consent** (`cookie-law-info`): cookie consent banner and GDPR/CCPA cookie notice workflow.
- **Rank Math SEO** (`seo-by-rank-math`): SEO titles, metadata, sitemap, schema, and search optimization.
- **Rich Showcase for Google Reviews** (`widget-google-reviews`): Google Reviews widgets and shortcodes.

ManageWP is not included because it is account-specific.

Backup/migration plugins are not listed as live-site detected plugins because they usually do not expose public frontend assets. Add a backup plugin manually if the site owner needs export, import, or migration workflows.

## Reviews Setup

1. Install and configure a reviews plugin manually, such as **Rich Showcase for Google Reviews**.
2. Copy the reviews shortcode from that plugin.
3. Go to **MultiRent Setup > Website Setup**.
4. Paste the shortcode into **Reviews shortcode**.
5. Enable the **Google Reviews** checkbox under **Plugin Placeholders**.
6. Save settings.

If the checkbox is off, the theme will not show the reviews section even if a shortcode is saved.

## Backup and SEO Notes

The SEO and backup checkboxes do not install plugins and do not change plugin settings. They only show private admin reminders to logged-in managers so the setup workflow is easier to remember.

## Uninstall Notes

Deactivating the plugin does not delete rental units or settings. This protects user content during troubleshooting or theme changes.

## Privacy

The plugin ships with placeholder content only. It does not include real property photos, owner details, booking IDs, review IDs, private API keys, or account-specific plugin configuration.

## Disclaimer

Copyright 2026 MultiRent Project. Original theme and companion plugin by the MultiRent project.

This code is free to use and modify for private, non-commercial purposes, provided that the original author and the MultiRent project are clearly credited.

Commercial use, including client work, paid services, marketplace products, hosted services, SaaS products, agency projects, revenue-generating websites, or resale, requires prior written permission from the copyright holder.

This theme and companion plugin are provided on a best-effort basis. No guarantees are given for correctness, compatibility, security, availability, fitness for a particular purpose, or future maintenance. The end user is responsible for everything they install, configure, publish, modify, connect, or deploy with this theme and plugin.

Any permitted private-use copy, modified version, or redistributed non-commercial version must clearly state that it originated from the Multi Apartment Rental theme and MultiRent Companion plugin, and must credit the original author and the MultiRent project.

No free or paid support is provided. The copyright holder may remove this theme/plugin, stop distributing it, or stop developing it without notice.

