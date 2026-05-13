# MultiRent Companion

MultiRent Companion is the setup and content-management plugin for the **Multi Apartment Rental** WordPress theme.

The plugin is designed for rental-property owners who want to manage apartments, images, menus, colors, review sections, and starter pages from the WordPress dashboard without editing theme files.

## Install Order

1. Install and activate `MultiRent-0.1.14.zip` in **Appearance > Themes > Add New > Upload Theme**.
2. Install and activate `multirent-companion-0.1.14.zip` in **Plugins > Add New > Upload Plugin**.
3. Open **MultiRent Setup** in the left WordPress admin menu.
4. Click **Create Starter Pages, Menu, and Amenities** if this is a fresh site.
5. Create the required number of rental units from the **Starter Content** section.
6. Edit each rental unit under **MultiRent Setup > Rental Units**.
7. Review **Settings > Permalinks** and click **Save Changes** once if rental links do not open correctly.

For convenience, `multirent-template-0.1.14.zip` contains both the theme ZIP and plugin ZIP. Extract it first, then upload the theme and plugin separately in the order above.

## What This Plugin Adds

### MultiRent Setup Admin Area

The plugin creates a dedicated **MultiRent Setup** menu in WordPress admin. Rental-related admin tasks are grouped there so users do not need to search through many WordPress screens.

Grouped items include:

- Website Setup
- Rental Units
- Apartments Page
- Contact Page
- Local Page
- Help / README
- Amenities

The submenu order is intentionally kept simple: **Website Setup** first, **Rental Units** second, then separate page administration screens, **Help / README**, and the remaining rental tools.

The **Help / README** screen shows this README file inside WordPress admin so site owners can always find setup instructions from the left navigation pane.

### Website Setup

The **Website Setup** screen controls homepage and brand content:

- Property name
- Hero title and hero text
- Hero button label and link
- Homepage intro title and text
- Stats lines, such as unit count, distance, or service notes
- Homepage apartment-card visibility and number of apartments to show
- Reviews shortcode field
- Contact call-to-action title, text, button label, and button link

The landing page is the only mandatory page. Apartments, Contact, and Local pages are optional and have their own administration screens.

#### Website Setup Field Reference

- **Property name**: public property or rental-business name shown in the header and key theme areas.
- **Hero title**: main landing-page headline. Use a short phrase that explains the stay you offer.
- **Hero text**: short landing-page introduction below the headline.
- **Hero image**: large visual image used on the landing page hero.
- **Hero button text**: text shown on the main landing-page call-to-action button.
- **Hero button URL**: where the hero button opens. Use a page path such as `/apartments/` or a full URL.
- **Intro title**: heading for the landing-page intro section below the hero.
- **Intro text**: text for the landing-page intro section.
- **Stats lines**: landing-page facts, one per line, using `value | label`, for example `4 | Apartments`.
- **Homepage apartments checkbox**: shows or hides rental-unit cards on the front page.
- **Number of apartments to show**: limits the homepage rental cards from 1 to 50. Use the Apartments page for the complete list.
- **Reviews shortcode**: shortcode from a reviews plugin. The reviews section also needs the Google Reviews checkbox enabled.
- **Google Reviews checkbox**: shows or hides the reviews section.
- **SEO reminder checkbox**: shows a private admin-only reminder to configure SEO metadata.
- **Backup reminder checkbox**: shows a private admin-only reminder to create backups before major changes.
- **Contact title/text/button fields**: content for the landing-page contact call-to-action band, not the full Contact page.
- **Header menu links**: top menu links, one per line, using `Label | URL`.
- **Color scheme**: preset color palette used by the theme.
- **Use custom colors**: enables the custom color pickers.
- **Custom colors**: primary, dark, surface, and accent colors used across the theme.

### Separate Page Administration

Optional pages are managed from separate left-menu screens under **MultiRent Setup**:

- **Apartments Page**: show or hide the Apartments page and choose an Apartments template from the normal WordPress page editor.
- **Contact Page**: show or hide the Contact page and control contact details, booking checklist, map, page content, form shortcode, and map note.
- **Local Page**: show or hide the Local page and control guide cards, highlights, activities, useful links, and page content.

When an optional page is hidden, MultiRent changes that page to draft and removes it from the generated top menu. The Home/Landing page remains mandatory.

### Page Templates Created By Starter Content

The starter-content button creates sample pages and assigns useful templates:

- **Apartments** uses the **Apartments - Grid** template.
- **Contact** uses the **Contact / Booking Inquiry** template.
- **Local** uses the **Local Information** template.

The theme also includes **Apartments - Featured Guide** and **Apartments - Compact List** templates. Users can switch the Apartments page template from the normal WordPress page editor.

### Adding And Editing Pages

Users can add normal WordPress pages for extra content, such as house rules, booking terms, gallery pages, nearby restaurants, or seasonal offers.

To add a new page:

1. Open **Pages > Add New**.
2. Enter the page title.
3. Add text, headings, lists, images, buttons, or other blocks in the editor.
4. In the page settings sidebar, choose a template if the page should use one of the MultiRent templates.
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

For apartment card images, use **MultiRent Setup > Rental Units**, open the apartment, and click **Set featured image** in the right sidebar. In the classic editor, this may also appear as **Apartment Tile Image** in the **Apartment Page Editor** box.

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

The Contact Page Builder is now available from **MultiRent Setup > Contact Page** and controls the **Contact / Booking Inquiry** page template.

Editable fields include:

- Contact page title and intro
- Address
- Phone and mobile phone
- Email
- Contact form shortcode
- Map search query
- Map or arrival note
- Booking-help checklist lines

The user can show or hide each contact page area with checkboxes:

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
- **Booking help lines**: checklist shown to guests before they send an inquiry. Add one requested detail per line.
- **Show contact details card**: shows or hides the address/phone/email card.
- **Show booking inquiry checklist**: shows or hides the checklist of details guests should send.
- **Show map iframe**: shows or hides the embedded Google map.
- **Show page editor content**: shows or hides content written in the normal WordPress page editor.
- **Show form shortcode area**: shows or hides the contact-form shortcode output.
- **Show map or arrival note**: shows or hides the map note text.

### Local Page Builder

The Local Page Builder is now available from **MultiRent Setup > Local Page** and controls the **Local Information** page template.

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

The user can show or hide each local page area with checkboxes:

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

The plugin can create starter pages, a starter menu, starter amenities, and a chosen number of rental units.

Use this on a new site to get a working structure quickly, then replace the placeholder text and images with real property content.

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
- Distance or location note
- Price note
- Booking URL
- Amenities

#### Add A New Apartment End To End

Use this workflow whenever you want to add one complete apartment from the WordPress dashboard.

1. Open **MultiRent Setup > Rental Units**.
2. Click **Add New**.
3. Enter the apartment name in the title field, for example `Apartment A1` or `Sea View Studio`.
4. Add the full apartment description in the main editor. Include room layout, balcony/terrace notes, kitchen details, accessibility notes, house rules, or anything guests should know before booking.
5. Add a short summary in the **Excerpt** field if it is visible. This text is used on apartment cards. If Excerpt is not visible, open **Screen Options** at the top of the editor and enable **Excerpt**.
6. In the right sidebar, click **Set featured image** and select the main apartment photo from the Media Library or upload a new one. This is the apartment tile image used on cards and apartment pages.
7. Add extra apartment photos in the main editor with **Image** or **Gallery** blocks. These photos appear inside that apartment page only.
8. In the right sidebar, open **Apartment Details** and fill in **Guest capacity**, **Bedrooms**, **Bathrooms**, **Size**, **Location note**, **Price note**, and **Booking or inquiry URL**.
9. In the **Amenities** box, select existing amenities such as parking, Wi-Fi, air conditioning, sea view, or pet friendly. If you need a new amenity, open **MultiRent Setup > Amenities** in another tab, add it, then return to the apartment and select it.
10. Click **Publish** to make the apartment live, or **Save Draft** if it is not ready yet.
11. Open the public apartment page from the **View** link and check the image, description, details, amenities, and booking button.
12. Open the Apartments page on the public site and confirm the new apartment card appears in the list.
13. If the apartment page returns a 404 error, open **Settings > Permalinks** and click **Save Changes** once, then test again.

Recommended content checklist for each apartment:

- One clear apartment title.
- One strong tile image in landscape orientation.
- Extra apartment photos added as Image or Gallery blocks in the main editor.
- A short card summary in the Excerpt field.
- A complete main description.
- Capacity, bedrooms, bathrooms, size, location note, and price note.
- A booking or inquiry URL, usually `/contact/` or a direct booking link.
- Relevant amenities selected.
- Alt text on uploaded photos so the site is easier to use and better for SEO.

To edit an existing apartment later, open **MultiRent Setup > Rental Units**, click the apartment title, make changes, and click **Update**.

To hide an apartment without deleting it, open the apartment editor and change its status from **Published** to **Draft**. Draft apartments are not shown on the public listing.

#### Add Images To An Apartment Page

There are two image areas for each apartment:

- **Set featured image**: the main apartment image. It appears on apartment cards, previews, and the top of the single apartment page.
- **Image or Gallery blocks in the main editor**: extra photos shown inside that apartment page only, such as bedroom, bathroom, kitchen, balcony, view, parking, or floor-plan photos.

To add extra photos to one apartment page:

1. Open **MultiRent Setup > Rental Units**.
2. Click the apartment you want to edit.
3. Click in the main editor area where the photos should appear.
4. Click the blue **+** button and choose **Image** for one photo or **Gallery** for several photos.
5. Choose **Upload** for new photos or **Media Library** for existing photos.
6. Add helpful alt text for each photo, for example `Apartment A1 balcony sea view`.
7. Add captions if they help guests understand the photo.
8. Click **Update**.
9. Open the public apartment page and confirm the photos appear in the right place.

Use **Set featured image** only for the main apartment/card image. Use **Image** or **Gallery** blocks for all additional apartment-page photos.

### Apartment Details And Images

Each rental unit edit screen includes apartment detail controls. In the modern WordPress block editor, these fields appear in the right sidebar under **Apartment Details**. In the classic editor, they appear in an **Apartment Page Editor** box below the main editor.

The apartment tile image is the normal WordPress **Featured image**. Click **Set featured image** in the right sidebar. It appears in rental cards and single rental pages.

#### Apartment Field Reference

- **Featured image / apartment tile image**: main image used on apartment cards, previews, and the top of the apartment page. In the block editor, set it with **Set featured image** in the right sidebar.
- **Guest capacity**: guest count shown on cards and detail pages, such as `2-4 guests`.
- **Bedrooms**: number or description of bedrooms, such as `2` or `Studio`.
- **Bathrooms**: number or description of bathrooms.
- **Size**: apartment size, such as `45 m2`.
- **Location note**: short location note, such as `100 m from beach` or `City center`.
- **Price note**: short price message, such as `On request`, `From 90 EUR`, or `Seasonal rates`.
- **Booking or inquiry URL**: link for the apartment booking/inquiry button. Use a full URL or a site page path.

### Amenities

Amenities are grouped under **MultiRent Setup > Amenities**. They can be assigned to rental units and displayed on rental pages.

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
