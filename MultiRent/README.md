# Multi Apartment Rental

Multi Apartment Rental is a clean, PII-free WordPress theme for apartment, room, villa, and multi-unit rental websites.

## Installation

1. In WordPress admin, go to **Appearance > Themes > Add New > Upload Theme**.
2. Upload `multirent-theme-upload-0.1.27.zip`.
3. Activate **Multi Apartment Rental**.
4. Go to **Plugins > Add New > Upload Plugin**.
5. Upload `multirent-companion-plugin-upload-0.1.27.zip`.
6. Activate **MultiRent Companion**.
7. Open **MultiRent Setup** from the left WordPress admin menu.

If you received `multirent-complete-package-extract-first-0.1.27.zip`, unzip it first. It contains the separate theme and plugin ZIP files that must be uploaded individually.

Do not upload `multirent-complete-package-extract-first-0.1.27.zip` or GitHub's automatic source-code ZIP directly in **Upload Theme**. WordPress expects the theme ZIP to contain the `MultiRent/style.css` stylesheet, which is present in `multirent-theme-upload-0.1.27.zip`.

## Intended Use

The theme is designed to work with the MultiRent Companion plugin so non-technical users can configure the site from WordPress admin.

Users can manage:

- Property name and homepage text
- Hero image
- GUI hero image picker through MultiRent Companion
- Rental units
- Capacity, bedrooms, bathrooms, size, price note, booking URL
- Amenities
- Reviews shortcode
- Contact call to action
- Ready-made Apartments, Contact, and Local page templates
- Configurable Contact page sections
- Configurable Local information page sections
- Preset and custom color schemes
- Homepage apartment-card visibility and count controls
- Apartment tile images managed from each apartment editor
- Starter pages, menu, and demo rental units
- Separate page administration for Apartments, Contact, and Local pages
- Show/hide controls for optional pages

## Main Theme Functions

### Homepage

The homepage is built for rental businesses and uses editable content from **MultiRent Setup**:

- Hero section
- Hero image
- Property intro eyebrow, heading, and text
- Stats strip
- Rental unit cards
- Homepage controls to hide rental cards or limit how many cards are shown
- Optional reviews section
- Contact call to action

### Rental Listings

Rental cards are generated from **Rental Units**, which are managed by the companion plugin. Each card can show the apartment image, title, summary, guest capacity, and details link. Bedrooms, bathrooms, size, and price note are shown on the single apartment detail page.

### Apartments Page Templates

The theme includes ready-made page templates for Apartments, Contact, and Local pages. Choose the assigned WordPress page and template from the matching MultiRent Setup admin screen, then use the preview button under the template dropdown to open the selected page.

### Apartments Page Templates

Choose the Apartments page template from **MultiRent Setup > Apartments Page**:

- **Apartments - Grid**: simple card grid similar to the apartment overview on the original local rental site.
- **Apartments - Featured Guide**: intro text plus the same apartment card grid, useful when the page should explain how to choose a unit.
- **Apartments - Compact List**: tighter horizontal cards for sites with many apartments or when quick comparison matters.

All Apartments templates pull published **Rental Units** automatically and use the rental image fields plus the right-sidebar Apartment Details fields.

### Contact Page Templates

Choose the Contact page and template from **MultiRent Setup > Contact Page**:

- **Contact / Booking Inquiry**: balanced details, inquiry, map, content, and form layout.
- **Contact - Split Map**: map-forward layout for arrival context before inquiry details.
- **Contact - Compact**: simpler single-column contact and inquiry flow.

### Local Page Templates

Choose the Local page and template from **MultiRent Setup > Local Page**:

- **Local Information**: full guide layout with a useful-links sidebar.
- **Local - Compact Guide**: stacked single-column guide for shorter local pages.
- **Local - Featured Guide**: guide-first layout with prominent plan-your-stay cards.

### Adding And Editing Pages

Users can add any extra page through normal WordPress pages.

To add a new page:

1. Go to **Pages > Add New**.
2. Enter a title.
3. Add text, headings, lists, images, buttons, columns, or other blocks.
4. Choose assigned Apartments, Contact, and Local pages plus their templates from the matching MultiRent Setup admin screens if needed.
5. Click **Publish**.

To edit text on an existing page:

1. Go to **Pages > All Pages**.
2. Open the page.
3. Click the text block and edit it directly.
4. Use the **+** button to add more blocks.
5. Click **Update**.

Homepage fields, including the intro eyebrow above the property intro heading, are edited from **MultiRent Setup > Website Setup**. That screen also has homepage section visibility checkboxes for Hero, Intro, Stats, Apartment cards, Reviews, admin-only reminders, and the Contact call-to-action, so sections can be hidden without deleting their saved text or images. Contact page fields are edited from **MultiRent Setup > Contact Page**. Local page fields are edited from **MultiRent Setup > Local Page**.

### Separate Page Administration

The landing page is the only mandatory page. Optional pages have their own admin screens under **MultiRent Setup**:

- **Apartments Page**: choose which WordPress page is the Apartments page, show/hide it, choose the visual template, and open the assigned page preview from the template section.
- **Contact Page**: choose which WordPress page is the Contact page, show/hide it, choose the visual template, and control contact details, booking checklist, map, content, form, and map note.
- **Local Page**: choose which WordPress page is the Local page, show/hide it, choose the visual template, and control guide cards, highlights, activities, useful links, and page content.

When an optional assigned page is hidden, the companion plugin changes that selected page to draft and removes matching default page paths from the generated top menu. The standard WordPress page editor is still available for custom page content, and pages can be deleted or recreated like normal WordPress pages.

### Adding Images And Linking Them

To add an image to any page:

1. Open the page editor.
2. Click where the image should go.
3. Click **+** and choose **Image**.
4. Upload a new image or choose one from **Media Library**.
5. Add alt text.
6. Adjust image size, alignment, or caption if needed.
7. Click **Update**.

To link an image to a URL:

1. Click the image block.
2. Click the link icon in the image toolbar.
3. Paste a URL such as `/apartments/`, `/contact/`, or a full external link.
4. Press **Enter** or click **Apply**.
5. Click **Update**.

Use **MultiRent Setup > Website Setup > Hero image** for the homepage hero image. For apartment card images, open the apartment under **MultiRent Setup > Rental Units** and use **Apartment Images > Apartment Tile Image** below the editor.

### Adding Pages To The Menu And Ordering Them

The simplest way is the **Top Menu Builder** in **MultiRent Setup > Website Setup**.

Add one item per line in the order users should see them:

```text
Home | /
Apartments | /apartments/
Local | /local/
Contact | /contact/
Booking Terms | /booking-terms/
```

Click **Save and Apply Top Menu**.

The standard WordPress method also works: open **Appearance > Menus**, add pages to the menu, drag them into the preferred order, assign the menu to **Primary menu**, and click **Save Menu**.

### Single Apartment Pages

Each rental unit has its own page. The theme displays the unit image, gallery, details, amenities, description, and booking link using the image fields from **Apartment Images** and the text fields from the right-sidebar **Apartment Details** panel.

Apartment images work in three places:

- **Apartment Tile Image** is the main apartment/card image and appears at the top of the apartment page. It is managed from the auto-expanded **Apartment Images** box below the editor.
- **Apartment Gallery Images** in the auto-expanded Apartment Images box is the managed photo gallery shown beside the Details tile on the apartment detail page. Choose multiple images, use **Move up** and **Move down** to reorder them, remove any image you do not want, and then click **Update**.
- **Image** or **Gallery** blocks in the main editor are optional extra photos that appear inside the written apartment description only.
- Existing attached apartment images are still used as a fallback gallery when no Apartment Gallery Images have been selected, so older apartments keep showing their photos.
- **Apartment Details** are organized as a booking-style panel: optional price first, then clean quick facts for guests/bedrooms/bathrooms, then size, amenities, and the inquiry button. If the price note is empty, the price tile is hidden.

### Adding A New Apartment End To End

1. Open **MultiRent Setup > Rental Units**.
2. Click **Add New**.
3. Enter the apartment name as the title.
4. Add the full apartment description in the main editor.
5. Add a short card summary in the **Excerpt** field. If Excerpt is hidden, enable it from **Screen Options**.
6. In the auto-expanded **Apartment Images** box below the editor, use **Apartment Tile Image** to choose the apartment tile image.
7. In the auto-expanded **Apartment Images** box below the editor, use **Apartment Gallery Images** to choose the gallery photos for the apartment detail page, reorder them with **Move up** and **Move down**, and remove any images you do not want.
8. Add optional extra photos in the main editor with **Image** or **Gallery** blocks only when they should appear inside the written description.
9. In the right sidebar, open **Apartment Details** and fill in guest capacity, bedrooms, bathrooms, size, optional price note, and booking or inquiry URL. Guest capacity appears on apartment cards and detail pages; public summary badges display only the number or range. Leave price note empty to hide the price tile.
10. Select amenities in the **Amenities** checkbox list. The standard options are Parking, WiFi, Balcony, Bathroom, Air Condition, TV, Sat TV, BBQ, Terrace, No-Smoking, Kitchen, Pets allowed, and Pets not allowed.
11. Click **Publish**.
12. View the apartment page and the Apartments listing page to confirm the card, image, gallery order, details, amenities, and booking link are correct.

To add or reorder gallery photos later, open the apartment, use **Apartment Gallery Images**, adjust the order with **Move up** and **Move down**, add alt text in the Media Library where needed, and click **Update**.

If the new apartment page shows a 404 error, open **Settings > Permalinks** and click **Save Changes** once.

### Amenity Icons

The **Amenities** box on each apartment is a checkbox list. Select only the amenities that apply to that apartment. Selected amenities are displayed as icon badges on the single apartment detail page.

Default amenity options are created by MultiRent Companion: Parking, WiFi, Balcony, Bathroom, Air Condition, TV, Sat TV, BBQ, Terrace, No-Smoking, Kitchen, Pets allowed, and Pets not allowed. If older starter terms such as `Wi-Fi` or `Air conditioning` exist, the companion plugin migrates them to the newer checkbox terms when possible.

### Optional Demo Content

Open **MultiRent Setup > Website Setup**, find the **Demo Content** section, and click **Create Demo Content** to preview a complete example site. This admin option is included in public releases. The demo creates four apartments, generated demo hero/featured/gallery images, demo Home/Apartments/Contact/Local pages, menu links, selected amenity checkboxes, and sample contact/local-guide settings. Starter and demo content created by the companion plugin is assigned to the **MultiRent** WordPress author.

Click **Remove Demo Content** to disable the demo and delete the generated demo pages, apartments, and images. Demo content is marked internally with `_multirent_demo_content=multirent-demo-content-v1`, and the plugin stores previous homepage and MultiRent settings so they can be restored.

### Contact Page Template

The Contact templates can show or hide these sections from MultiRent Setup:

- Contact details card
- Booking inquiry checklist
- Google map iframe
- Page editor content
- Contact form shortcode area
- Map or arrival note

This lets a site owner decide whether the page should be a simple contact card, a booking-inquiry page, a map page, or a fuller contact/form page.

Contact field meanings:

- **Contact map query** means the address/search phrase used by Google Maps, such as `Example Apartments, Split, Croatia`. It is not a special code.
- **Contact form shortcode** is optional and comes from a separate form plugin.
- **Booking help lines** are the checklist items guests should include when asking about availability.

### Local Information Template

The Local templates can show or hide these sections from MultiRent Setup:

- Guest guide cards
- Local highlights
- Trips and activities
- Useful links sidebar
- Page editor content

Users can add local page items with simple text lines such as `Beach | Five minutes on foot` or `Nearest airport | https://example.com`.

Local field meanings:

- **Guide, highlight, and activity lines** use `Title | text`.
- **Useful link lines** use `Label | URL`.
- Each show/hide checkbox controls whether that section appears on the Local page.

### Header Menu

The theme supports a primary header menu. The top header row stays fixed while visitors scroll, so the property name and menu remain visible. Users can manage the menu through standard WordPress menus or through the simpler **Top Menu Builder** in MultiRent Companion.

### Colors

The theme uses color tokens generated from the companion plugin settings. Users can choose a preset palette or enable custom colors without editing CSS.

### Optional Plugin Areas

The theme includes safe placeholders for optional plugin workflows:

- Reviews section, shown only when enabled in MultiRent Setup and a reviews shortcode is entered.
- Admin-only SEO reminder, shown only to logged-in managers when enabled.
- Admin-only backup reminder, shown only to logged-in managers when enabled.

The theme does not require those plugins to run.

## Recommended Plugins

Install these manually only if the site needs them. This list is based on the plugins validated during local rental-site testing:

- **MultiRent Companion**: required for GUI setup, rental units, amenities, menu builder, colors, and image controls.
- **CookieYes - Cookie Banner for Cookie Consent** (`cookie-law-info`): optional cookie consent banner and GDPR/CCPA cookie notice workflow.
- **Rank Math SEO** (`seo-by-rank-math`): optional SEO metadata, sitemap, and schema workflow.
- **Rich Showcase for Google Reviews** (`widget-google-reviews`): optional Google Reviews shortcode workflow.

The theme and companion plugin do not automatically install third-party plugins. This avoids server permission problems and keeps setup under the WordPress administrator's control.

## Responsive Layout

The header switches to a compact menu at tablet widths, rental cards move from three columns to two columns and then one column, and phone layouts tighten hero type, button widths, card padding, stats, contact, local guide, and apartment detail sections so pages remain readable on small screens.

Backup/migration plugins are not listed as live-site detected plugins because they usually do not expose public frontend assets. Add a backup plugin manually if the site owner needs export, import, or migration workflows.

## First Setup Checklist

1. Activate the theme.
2. Activate MultiRent Companion.
3. Open **MultiRent Setup**.
4. Create starter pages, menu, and amenities.
5. Add the correct number of rental units.
6. Upload a hero image.
7. Edit property name, homepage text, stats, and contact call to action.
8. Edit each rental unit and add its apartment tile image/details.
9. Choose a color scheme or enable custom colors.
10. Configure the top menu.
11. Create or review the Apartments, Contact, and Local pages.
12. Add optional plugins manually only when needed.

## Privacy

This theme ships with no real property photos, no owner contact data, no booking IDs, no review IDs, and no real location coordinates.

## Disclaimer

Copyright 2026 MultiRent Project. Original theme and companion plugin by the MultiRent project.

This code is free to use and modify for private, non-commercial purposes, provided that the original author and the MultiRent project are clearly credited.

Commercial use, including client work, paid services, marketplace products, hosted services, SaaS products, agency projects, revenue-generating websites, or resale, requires prior written permission from the copyright holder.

This theme and companion plugin are provided on a best-effort basis. No guarantees are given for correctness, compatibility, security, availability, fitness for a particular purpose, or future maintenance. The end user is responsible for everything they install, configure, publish, modify, connect, or deploy with this theme and plugin.

Any permitted private-use copy, modified version, or redistributed non-commercial version must clearly state that it originated from the Multi Apartment Rental theme and MultiRent Companion plugin, and must credit the original author and the MultiRent project.

No free or paid support is provided. The copyright holder may remove this theme/plugin, stop distributing it, or stop developing it without notice.
