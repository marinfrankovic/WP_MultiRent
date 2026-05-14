<?php
/**
 * Plugin Name: MultiRent Companion
 * Description: End-to-end setup tools, rental unit management, amenities, and GUI settings for the Multi Apartment Rental theme.
 * Version: 0.1.31
 * Requires at least: 6.5
 * Requires PHP: 8.4
 * Author: MultiRent Project
 * Text Domain: multirent-companion
 * Copyright: 2026 MultiRent Project. Free for private, non-commercial use and modification with original author credit. Commercial use requires prior written permission from the copyright holder.
 * License: Proprietary private-use license
 *
 * @package MultiRentCompanion
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MULTIRENT_COMPANION_VERSION', '0.1.31' );

function multirent_companion_default_amenities() {
	return array(
		'parking'          => 'Parking',
		'wifi'             => 'WiFi',
		'balcony'          => 'Balcony',
		'bathroom'         => 'Bathroom',
		'air-condition'    => 'Air Condition',
		'tv'               => 'TV',
		'sat-tv'           => 'Sat TV',
		'bbq'              => 'BBQ',
		'terrace'          => 'Terrace',
		'no-smoking'       => 'No-Smoking',
		'kitchen'          => 'Kitchen',
		'pets-allowed'     => 'Pets allowed',
		'pets-not-allowed' => 'Pets not allowed',
	);
}

function multirent_companion_ensure_default_amenities() {
	if ( ! taxonomy_exists( 'rental_amenity' ) ) {
		return;
	}

	foreach ( multirent_companion_default_amenities() as $slug => $name ) {
		if ( ! term_exists( $slug, 'rental_amenity' ) ) {
			wp_insert_term( $name, 'rental_amenity', array( 'slug' => $slug ) );
		}
	}
}

function multirent_companion_migrate_legacy_amenities() {
	if ( ! taxonomy_exists( 'rental_amenity' ) ) {
		return;
	}

	$legacy_map = array(
		'wi-fi'            => 'wifi',
		'air-conditioning' => 'air-condition',
	);

	foreach ( $legacy_map as $old_slug => $new_slug ) {
		$old_term = get_term_by( 'slug', $old_slug, 'rental_amenity' );
		$new_term = get_term_by( 'slug', $new_slug, 'rental_amenity' );

		if ( ! $old_term || ! $new_term ) {
			continue;
		}

		$object_ids = get_objects_in_term( $old_term->term_id, 'rental_amenity' );
		foreach ( $object_ids as $object_id ) {
			wp_set_object_terms( $object_id, array( (int) $new_term->term_id ), 'rental_amenity', true );
			wp_remove_object_terms( $object_id, (int) $old_term->term_id, 'rental_amenity' );
		}

		$old_term = get_term( $old_term->term_id, 'rental_amenity' );
		if ( $old_term && ! is_wp_error( $old_term ) && 0 === (int) $old_term->count ) {
			wp_delete_term( $old_term->term_id, 'rental_amenity' );
		}
	}

	foreach ( array( 'family-friendly', 'sea-view' ) as $old_slug ) {
		$old_term = get_term_by( 'slug', $old_slug, 'rental_amenity' );
		if ( $old_term && 0 === (int) $old_term->count ) {
			wp_delete_term( $old_term->term_id, 'rental_amenity' );
		}
	}
}

function multirent_companion_activate() {
	multirent_companion_register_content_types();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'multirent_companion_activate' );

function multirent_companion_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'multirent_companion_deactivate' );

function multirent_companion_register_content_types() {
	register_post_type(
		'rental_unit',
		array(
			'labels'       => array(
				'name'          => esc_html__( 'Rental Units', 'multirent-companion' ),
				'singular_name' => esc_html__( 'Rental Unit', 'multirent-companion' ),
				'add_new_item'  => esc_html__( 'Add New Rental Unit', 'multirent-companion' ),
				'edit_item'     => esc_html__( 'Edit Rental Unit', 'multirent-companion' ),
				'all_items'     => esc_html__( 'Rental Units', 'multirent-companion' ),
			),
			'public'       => true,
			'show_in_menu' => 'multirent-setup',
			'supports'     => array( 'title', 'editor', 'excerpt', 'page-attributes' ),
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'rentals' ),
			'show_in_rest' => true,
		)
	);

	register_taxonomy(
		'rental_amenity',
		'rental_unit',
		array(
			'labels'       => array(
				'name'          => esc_html__( 'Amenities', 'multirent-companion' ),
				'singular_name' => esc_html__( 'Amenity', 'multirent-companion' ),
			),
			'public'       => true,
			'hierarchical' => true,
			'rewrite'      => array( 'slug' => 'amenity' ),
			'show_in_rest' => true,
			'show_in_menu' => 'multirent-setup',
			'show_admin_column' => true,
		)
	);
}
add_action( 'init', 'multirent_companion_register_content_types' );
add_action( 'init', 'multirent_companion_ensure_default_amenities', 20 );
add_action( 'init', 'multirent_companion_migrate_legacy_amenities', 30 );

function multirent_companion_unit_fields() {
	return array(
		'capacity'   => esc_html__( 'Guest capacity', 'multirent-companion' ),
		'bedrooms'   => esc_html__( 'Bedrooms', 'multirent-companion' ),
		'bathrooms'  => esc_html__( 'Bathrooms', 'multirent-companion' ),
		'size'       => esc_html__( 'Size', 'multirent-companion' ),
		'price_note' => esc_html__( 'Price note', 'multirent-companion' ),
		'booking_url'=> esc_html__( 'Booking or inquiry URL', 'multirent-companion' ),
		'video_url'  => esc_html__( 'YouTube video URL', 'multirent-companion' ),
		'map_address' => esc_html__( 'Apartment map address', 'multirent-companion' ),
		'map_coordinates' => esc_html__( 'Apartment map coordinates', 'multirent-companion' ),
	);
}

function multirent_companion_register_unit_meta() {
	foreach ( multirent_companion_unit_fields() as $key => $label ) {
		register_post_meta(
			'rental_unit',
			'_' . $key,
			array(
				'single'            => true,
				'type'              => 'string',
				'show_in_rest'      => true,
				'sanitize_callback' => in_array( $key, array( 'booking_url', 'video_url' ), true ) ? 'esc_url_raw' : 'sanitize_text_field',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	register_post_meta(
		'rental_unit',
		'_gallery_image_ids',
		array(
			'single'            => true,
			'type'              => 'string',
			'show_in_rest'      => true,
			'sanitize_callback' => 'multirent_companion_sanitize_gallery_image_ids',
			'auth_callback'     => function() {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'rental_unit',
		'_qr_code_image_id',
		array(
			'single'            => true,
			'type'              => 'integer',
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
			'auth_callback'     => function() {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'multirent_companion_register_unit_meta' );

function multirent_companion_sanitize_gallery_image_ids( $value ) {
	$ids = is_array( $value ) ? $value : explode( ',', (string) $value );
	$ids = array_filter( array_map( 'absint', $ids ) );
	$ids = array_values( array_unique( $ids ) );

	return implode( ',', $ids );
}

function multirent_companion_gallery_image_ids_for_editor( $post_id ) {
	$stored_ids = multirent_companion_sanitize_gallery_image_ids( get_post_meta( $post_id, '_gallery_image_ids', true ) );
	if ( $stored_ids ) {
		return $stored_ids;
	}

	$thumbnail_id = get_post_thumbnail_id( $post_id );
	$attachments  = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'post_parent'    => $post_id,
			'post_status'    => 'inherit',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order ID',
			'order'          => 'ASC',
			'fields'         => 'ids',
		)
	);

	$image_ids = array();
	foreach ( $attachments as $attachment_id ) {
		if ( (int) $attachment_id === (int) $thumbnail_id ) {
			continue;
		}

		$image_ids[] = (int) $attachment_id;
	}

	return implode( ',', $image_ids );
}

function multirent_companion_unit_sidebar_fields() {
	$fields = array();
	foreach ( multirent_companion_unit_fields() as $key => $label ) {
		$fields[] = array(
			'key'         => '_' . $key,
			'label'       => $label,
			'type'        => in_array( $key, array( 'booking_url', 'video_url' ), true ) ? 'url' : 'text',
			'description' => multirent_companion_field_description( $key ),
		);
	}
	return $fields;
}

function multirent_companion_field_descriptions() {
	return array(
		'property_name'       => esc_html__( 'Public property or rental-business name shown in the header and key theme areas.', 'multirent-companion' ),
		'page_logo'           => esc_html__( 'Optional logo image shown to the left of the property name in the site header. Leave empty to show the property name without any logo.', 'multirent-companion' ),
		'hero_title'          => esc_html__( 'Main landing-page headline. Use a short phrase that explains the stay you offer.', 'multirent-companion' ),
		'hero_text'           => esc_html__( 'Short landing-page introduction below the headline.', 'multirent-companion' ),
		'hero_button_text'    => esc_html__( 'Text shown on the main landing-page call-to-action button.', 'multirent-companion' ),
		'hero_button_url'     => esc_html__( 'Where the hero button opens. Use a page path such as /apartments/ or a full URL.', 'multirent-companion' ),
		'intro_eyebrow'       => esc_html__( 'Small label shown above the landing-page intro heading.', 'multirent-companion' ),
		'intro_title'         => esc_html__( 'Heading for the landing-page intro section below the hero.', 'multirent-companion' ),
		'intro_text'          => esc_html__( 'Text for the landing-page intro section. Explain what guests can manage or discover.', 'multirent-companion' ),
		'stats_lines'         => esc_html__( 'Landing-page facts, one per line, using value | label. Example: 4 | Apartments.', 'multirent-companion' ),
		'show_front_page_rentals' => esc_html__( 'When enabled, rental-unit cards appear on the homepage below the intro and stats.', 'multirent-companion' ),
		'front_page_rental_count' => esc_html__( 'Maximum number of rental units shown on the homepage. Use the Apartments page for the full list.', 'multirent-companion' ),
		'reviews_shortcode'   => esc_html__( 'Shortcode from a reviews plugin. The Reviews section checkbox must also be enabled in Homepage section visibility.', 'multirent-companion' ),
		'contact_title'       => esc_html__( 'Heading for the landing-page contact call-to-action band.', 'multirent-companion' ),
		'contact_text'        => esc_html__( 'Short text in the landing-page contact call-to-action band.', 'multirent-companion' ),
		'contact_button_text' => esc_html__( 'Button text for the landing-page contact call to action.', 'multirent-companion' ),
		'contact_button_url'  => esc_html__( 'Where the landing-page contact button opens, such as /contact/.', 'multirent-companion' ),
		'menu_items'          => esc_html__( 'Header menu links, one per line, using Label | URL. The order here is the order shown in the menu.', 'multirent-companion' ),
		'color_scheme'        => esc_html__( 'Preset color palette used by the theme unless custom colors are enabled.', 'multirent-companion' ),
		'use_custom_colors'   => esc_html__( 'Enable this when you want the custom color pickers to override the selected preset.', 'multirent-companion' ),
		'color_primary'       => esc_html__( 'Main action color used for buttons, links, and highlights.', 'multirent-companion' ),
		'color_dark'          => esc_html__( 'Dark brand color used for headers, footers, and dark sections.', 'multirent-companion' ),
		'color_surface'       => esc_html__( 'Light page background color.', 'multirent-companion' ),
		'color_accent'        => esc_html__( 'Soft accent background color used for highlighted navigation and panels.', 'multirent-companion' ),
		'show_hero_section'   => esc_html__( 'Show or hide the large homepage hero section.', 'multirent-companion' ),
		'show_intro_section'  => esc_html__( 'Show or hide the homepage introduction section.', 'multirent-companion' ),
		'show_stats_section'  => esc_html__( 'Show or hide the homepage facts strip.', 'multirent-companion' ),
		'show_apartments_page' => esc_html__( 'When enabled, the Apartments page is published and kept in the generated top menu.', 'multirent-companion' ),
		'show_contact_page'   => esc_html__( 'When enabled, the Contact page is published and kept in the generated top menu.', 'multirent-companion' ),
		'show_local_page'     => esc_html__( 'When enabled, the Local page is published and kept in the generated top menu.', 'multirent-companion' ),
		'show_contact_cta'    => esc_html__( 'Show or hide the homepage contact call-to-action band.', 'multirent-companion' ),
		'contact_page_title'  => esc_html__( 'Main heading shown at the top of the Contact page.', 'multirent-companion' ),
		'contact_page_intro'  => esc_html__( 'Intro text shown under the Contact page heading.', 'multirent-companion' ),
		'contact_address'     => esc_html__( 'Address block shown in the contact details card. Use one line per address line.', 'multirent-companion' ),
		'contact_phone'       => esc_html__( 'Primary phone number shown on the Contact page. Leave empty to hide it.', 'multirent-companion' ),
		'contact_mobile'      => esc_html__( 'Mobile phone number shown on the Contact page. Leave empty to hide it.', 'multirent-companion' ),
		'contact_email'       => esc_html__( 'Email address shown on the Contact page. Leave empty to hide it.', 'multirent-companion' ),
		'contact_form_shortcode' => esc_html__( 'Shortcode from a contact-form plugin, for example [contact-form-7 id="123"]. Leave empty if no form is used.', 'multirent-companion' ),
		'contact_map_query'   => esc_html__( 'Search text used to build the embedded Google map, such as a property name, street address, city, and country. It is not a special code.', 'multirent-companion' ),
		'contact_map_note'    => esc_html__( 'Short note below the map, useful for parking, arrival instructions, or map corrections.', 'multirent-companion' ),
		'contact_qr_code_image_id' => esc_html__( 'Optional QR code image for the Contact page. When empty, the QR tile is hidden.', 'multirent-companion' ),
		'booking_help_lines'  => esc_html__( 'Checklist shown to guests before they send an inquiry. Add one requested detail per line.', 'multirent-companion' ),
		'local_page_title'    => esc_html__( 'Main heading shown at the top of the Local page.', 'multirent-companion' ),
		'local_page_intro'    => esc_html__( 'Intro text shown under the Local page heading.', 'multirent-companion' ),
		'local_guide_lines'   => esc_html__( 'Guest guide cards, one per line, using Title | text.', 'multirent-companion' ),
		'local_highlight_lines' => esc_html__( 'Quick local highlights, one per line, using Title | text.', 'multirent-companion' ),
		'local_activity_lines' => esc_html__( 'Activity cards, one per line, using Title | text.', 'multirent-companion' ),
		'local_link_lines'    => esc_html__( 'Useful links, one per line, using Label | URL.', 'multirent-companion' ),
		'capacity'            => esc_html__( 'Guest capacity shown on apartment cards and detail pages, such as 2-4 guests.', 'multirent-companion' ),
		'bedrooms'            => esc_html__( 'Number or description of bedrooms, such as 2 or Studio.', 'multirent-companion' ),
		'bathrooms'           => esc_html__( 'Number or description of bathrooms.', 'multirent-companion' ),
		'size'                => esc_html__( 'Apartment size, such as 45 m2.', 'multirent-companion' ),
		'price_note'          => esc_html__( 'Optional short price message, such as On request, From 90 EUR, or Seasonal rates. Leave empty to hide the price tile on the apartment detail page.', 'multirent-companion' ),
		'booking_url'         => esc_html__( 'Link for booking or inquiry button for this apartment. Use a full URL or a site page path.', 'multirent-companion' ),
		'video_url'           => esc_html__( 'Optional YouTube video link. When set, the video appears as part of the apartment gallery and opens in the gallery lightbox.', 'multirent-companion' ),
		'map_address'         => esc_html__( 'Optional address for a map specific to this apartment. Leave empty when the property-wide contact map is enough.', 'multirent-companion' ),
		'map_coordinates'     => esc_html__( 'Optional latitude and longitude for this apartment map, such as 43.2039, 17.1364. Coordinates override the address when both are set.', 'multirent-companion' ),
	);
}

function multirent_companion_field_description( $key ) {
	$descriptions = multirent_companion_field_descriptions();
	return isset( $descriptions[ $key ] ) ? $descriptions[ $key ] : '';
}

function multirent_companion_description( $key ) {
	$description = multirent_companion_field_description( $key );
	if ( $description ) {
		echo '<p class="description">' . esc_html( $description ) . '</p>';
	}
}

function multirent_companion_add_unit_meta_box() {
	add_meta_box( 'multirent_unit_details', esc_html__( 'Apartment Images', 'multirent-companion' ), 'multirent_companion_render_unit_meta_box', 'rental_unit', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'multirent_companion_add_unit_meta_box' );

function multirent_companion_render_unit_meta_box( $post ) {
	wp_nonce_field( 'multirent_save_unit_details', 'multirent_unit_nonce' );
	$tile_image_id = get_post_thumbnail_id( $post );
	?>
	<div class="multirent-editor-panel">
		<h3><?php esc_html_e( 'Apartment Tile Image', 'multirent-companion' ); ?></h3>
		<p><?php esc_html_e( 'This image is used on apartment cards, previews, and the top of the apartment page.', 'multirent-companion' ); ?></p>
		<?php multirent_companion_media_field( 'multirent_featured_image_id', $tile_image_id, __( 'Choose apartment image', 'multirent-companion' ), __( 'Remove image', 'multirent-companion' ) ); ?>
	</div>
	<div class="multirent-editor-panel">
		<h3><?php esc_html_e( 'Apartment Gallery Images', 'multirent-companion' ); ?></h3>
		<p><?php esc_html_e( 'Choose extra apartment photos for the gallery on the rental detail page. Use Move up and Move down to control the public gallery order.', 'multirent-companion' ); ?></p>
		<?php multirent_companion_gallery_media_field( 'multirent_gallery_image_ids', multirent_companion_gallery_image_ids_for_editor( $post->ID ) ); ?>
	</div>
	<?php
}

function multirent_companion_save_unit_details( $post_id ) {
	if ( ! isset( $_POST['multirent_unit_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['multirent_unit_nonce'] ) ), 'multirent_save_unit_details' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['multirent_unit'] ) && is_array( $_POST['multirent_unit'] ) ) {
		$values = wp_unslash( $_POST['multirent_unit'] );
		foreach ( multirent_companion_unit_fields() as $key => $label ) {
			$value = isset( $values[ $key ] ) ? sanitize_text_field( $values[ $key ] ) : '';
			if ( in_array( $key, array( 'booking_url', 'video_url' ), true ) ) {
				$value = esc_url_raw( $value );
			}
			update_post_meta( $post_id, '_' . $key, $value );
		}
	}

	if ( isset( $_POST['multirent_featured_image_id'] ) ) {
		$featured_image_id = absint( $_POST['multirent_featured_image_id'] );
		if ( $featured_image_id ) {
			set_post_thumbnail( $post_id, $featured_image_id );
		} else {
			delete_post_thumbnail( $post_id );
		}
	}

	if ( isset( $_POST['multirent_gallery_image_ids'] ) ) {
		update_post_meta( $post_id, '_gallery_image_ids', multirent_companion_sanitize_gallery_image_ids( wp_unslash( $_POST['multirent_gallery_image_ids'] ) ) );
	}

}
add_action( 'save_post_rental_unit', 'multirent_companion_save_unit_details' );

function multirent_companion_default_settings() {
	return array(
		'property_name'       => 'Your Rental Property',
		'page_logo'           => '',
		'hero_title'          => 'Flexible stays for every guest',
		'hero_text'           => 'Showcase apartments, rooms, villas, or holiday homes with clear details and easy inquiry paths.',
		'hero_image'          => '',
		'show_hero_section'   => '1',
		'hero_button_text'    => 'View rentals',
		'hero_button_url'     => '#rentals',
		'show_intro_section'  => '1',
		'intro_eyebrow'       => 'About the property',
		'intro_title'         => 'Manage every unit from WordPress',
		'intro_text'          => 'Add rental units, amenities, photos, capacity, booking links, and guest-facing details from the WordPress dashboard.',
		'show_stats_section'  => '1',
		'stats_lines'         => "4 | Rental units\n100 m | Example distance\n24/7 | Self-managed content",
		'show_front_page_rentals' => '1',
		'front_page_rental_count' => '12',
		'reviews_shortcode'   => '',
		'show_reviews'        => '0',
		'show_seo_note'       => '0',
		'show_migration_note' => '0',
		'contact_title'       => 'Ready to receive inquiries?',
		'contact_text'        => 'Connect this section to your contact page, booking form, or external reservation system.',
		'contact_button_text' => 'Contact us',
		'contact_button_url'  => '#contact',
		'show_contact_cta'    => '1',
		'show_apartments_page' => '1',
		'apartments_page_id'  => '',
		'show_contact_page'   => '1',
		'contact_page_id'     => '',
		'show_local_page'     => '1',
		'local_page_id'       => '',
		'contact_page_title'  => 'Contact and booking inquiry',
		'contact_page_intro'  => 'Send your dates, guest count, and preferred rental unit so the owner can reply with availability.',
		'contact_address'     => "Your property name\nStreet and house number\nCity, country",
		'contact_phone'       => '',
		'contact_mobile'      => '',
		'contact_email'       => '',
		'contact_form_shortcode' => '',
		'contact_map_query'   => '',
		'contact_map_note'    => 'Add arrival notes, parking instructions, or map corrections here.',
		'contact_qr_code_image_id' => '',
		'booking_help_lines'  => "Preferred arrival and departure dates\nNumber of adults and children\nPreferred apartment or flexible choice\nParking, arrival, or mobility questions",
		'show_contact_details' => '1',
		'show_booking_help'   => '1',
		'show_contact_map'    => '1',
		'show_contact_content' => '1',
		'show_contact_form'   => '1',
		'show_contact_map_note' => '1',
		'local_page_title'    => 'Local information',
		'local_page_intro'    => 'Help guests plan arrival, beaches, restaurants, activities, and useful services around your property.',
		'local_guide_lines'   => "Beaches nearby | Describe the closest beach, walking time, shade, and family suitability.\nHow to arrive | Add airport, road, bus, ferry, parking, or check-in route notes.\nBest time to visit | Explain seasons, weather, crowds, and local events.",
		'local_highlight_lines' => "Beach | Add distance and practical details.\nMarket | Add grocery or everyday shopping notes.\nRestaurants | Add nearby dining suggestions.\nHealth services | Add nearest pharmacy, doctor, or hospital note.",
		'local_activity_lines' => "Day trips | Add nearby towns, islands, parks, or scenic routes.\nOutdoor activities | Add hiking, cycling, swimming, courts, rentals, or tours.\nFamily ideas | Add child-friendly places, playgrounds, or simple rainy-day options.",
		'local_link_lines'    => "Local tourism board | https://example.com\nNearest airport | https://example.com\nFerry or bus information | https://example.com",
		'show_local_guides'   => '1',
		'show_local_highlights' => '1',
		'show_local_activities' => '1',
		'show_local_links'    => '1',
		'show_local_content'  => '1',
		'menu_items'          => "Home | /\nApartments | /apartments/\nLocal | /local/\nContact | /contact/",
		'color_scheme'        => 'coastal',
		'use_custom_colors'   => '0',
		'color_primary'       => '',
		'color_dark'          => '',
		'color_surface'       => '',
		'color_accent'        => '',
	);
}

function multirent_companion_settings() {
	$settings = get_option( 'multirent_settings', array() );
	return wp_parse_args( is_array( $settings ) ? $settings : array(), multirent_companion_default_settings() );
}

function multirent_companion_admin_menu() {
	add_menu_page( esc_html__( 'MultiRent Setup', 'multirent-companion' ), esc_html__( 'MultiRent Setup', 'multirent-companion' ), 'manage_options', 'multirent-setup', 'multirent_companion_render_setup_page', plugins_url( 'assets/images/multirent-admin-icon.svg', __FILE__ ), 58 );
	add_submenu_page( 'multirent-setup', esc_html__( 'Website Setup', 'multirent-companion' ), esc_html__( 'Website Setup', 'multirent-companion' ), 'manage_options', 'multirent-setup', 'multirent_companion_render_setup_page' );
	add_submenu_page( 'multirent-setup', esc_html__( 'Apartments Page', 'multirent-companion' ), esc_html__( 'Apartments Page', 'multirent-companion' ), 'manage_options', 'multirent-apartments-page', 'multirent_companion_render_apartments_page' );
	add_submenu_page( 'multirent-setup', esc_html__( 'Contact Page', 'multirent-companion' ), esc_html__( 'Contact Page', 'multirent-companion' ), 'manage_options', 'multirent-contact-page', 'multirent_companion_render_contact_page' );
	add_submenu_page( 'multirent-setup', esc_html__( 'Local Page', 'multirent-companion' ), esc_html__( 'Local Page', 'multirent-companion' ), 'manage_options', 'multirent-local-page', 'multirent_companion_render_local_page' );
	add_submenu_page( 'multirent-setup', esc_html__( 'Amenities', 'multirent-companion' ), esc_html__( 'Amenities', 'multirent-companion' ), 'manage_categories', 'edit-tags.php?taxonomy=rental_amenity&post_type=rental_unit' );
	add_submenu_page( 'multirent-setup', esc_html__( 'Help / README', 'multirent-companion' ), esc_html__( 'Help / README', 'multirent-companion' ), 'manage_options', 'multirent-readme', 'multirent_companion_render_readme_page' );
}
add_action( 'admin_menu', 'multirent_companion_admin_menu' );

function multirent_companion_order_admin_submenus() {
	global $submenu;

	if ( empty( $submenu['multirent-setup'] ) || ! is_array( $submenu['multirent-setup'] ) ) {
		return;
	}

	$website_items = array();
	$rental_items  = array();
	$page_items    = array();
	$help_items    = array();
	$other_items   = array();

	foreach ( $submenu['multirent-setup'] as $submenu_item ) {
		$submenu_slug = isset( $submenu_item[2] ) ? $submenu_item[2] : '';

		if ( 'multirent-setup' === $submenu_slug ) {
			$submenu_item[0] = esc_html__( 'Website Setup', 'multirent-companion' );
			$website_items[] = $submenu_item;
		} elseif ( 'edit.php?post_type=rental_unit' === $submenu_slug ) {
			$submenu_item[0] = esc_html__( 'Rental Units', 'multirent-companion' );
			$rental_items[] = $submenu_item;
		} elseif ( in_array( $submenu_slug, array( 'multirent-apartments-page', 'multirent-contact-page', 'multirent-local-page' ), true ) ) {
			$page_items[] = $submenu_item;
		} elseif ( 'multirent-readme' === $submenu_slug ) {
			$help_items[] = $submenu_item;
		} else {
			$other_items[] = $submenu_item;
		}
	}

	$submenu['multirent-setup'] = array_values( array_merge( $website_items, $rental_items, $page_items, $other_items, $help_items ) );
}
add_action( 'admin_menu', 'multirent_companion_order_admin_submenus', 99 );

function multirent_companion_admin_parent_file( $parent_file ) {
	global $pagenow;

	$taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_key( wp_unslash( $_GET['taxonomy'] ) ) : '';
	if ( 'edit-tags.php' === $pagenow && 'rental_amenity' === $taxonomy ) {
		return 'multirent-setup';
	}

	return $parent_file;
}
add_filter( 'parent_file', 'multirent_companion_admin_parent_file' );

function multirent_companion_admin_submenu_file( $submenu_file ) {
	global $pagenow;

	$taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_key( wp_unslash( $_GET['taxonomy'] ) ) : '';
	if ( 'edit-tags.php' === $pagenow && 'rental_amenity' === $taxonomy ) {
		return 'edit-tags.php?taxonomy=rental_amenity&post_type=rental_unit';
	}

	return $submenu_file;
}
add_filter( 'submenu_file', 'multirent_companion_admin_submenu_file' );

function multirent_companion_readme_line_html( $text ) {
	$html = esc_html( $text );
	$html = preg_replace( '/`([^`]+)`/', '<code>$1</code>', $html );
	$html = preg_replace( '/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $html );
	return $html;
}

function multirent_companion_readme_html() {
	$readme_path = plugin_dir_path( __FILE__ ) . 'README.md';
	if ( ! is_readable( $readme_path ) ) {
		return '<p>' . esc_html__( 'README file is not available in this plugin package.', 'multirent-companion' ) . '</p>';
	}

	$lines    = preg_split( '/\r\n|\r|\n/', (string) file_get_contents( $readme_path ) );
	$html     = '';
	$in_list  = false;
	$in_olist = false;
	$in_code  = false;

	foreach ( $lines as $line ) {
		$trimmed = trim( $line );

		if ( str_starts_with( $trimmed, '```' ) ) {
			if ( $in_list ) {
				$html   .= '</ul>';
				$in_list = false;
			}
			if ( $in_olist ) {
				$html    .= '</ol>';
				$in_olist = false;
			}
			$html    .= $in_code ? '</code></pre>' : '<pre><code>';
			$in_code = ! $in_code;
			continue;
		}

		if ( $in_code ) {
			$html .= esc_html( $line ) . "\n";
			continue;
		}

		if ( '' === $trimmed ) {
			if ( $in_list ) {
				$html   .= '</ul>';
				$in_list = false;
			}
			if ( $in_olist ) {
				$html    .= '</ol>';
				$in_olist = false;
			}
			continue;
		}

		if ( preg_match( '/^(#{1,4})\s+(.+)$/', $trimmed, $matches ) ) {
			if ( $in_list ) {
				$html   .= '</ul>';
				$in_list = false;
			}
			if ( $in_olist ) {
				$html    .= '</ol>';
				$in_olist = false;
			}
			$level = min( 4, strlen( $matches[1] ) + 1 );
			$html .= '<h' . $level . '>' . multirent_companion_readme_line_html( $matches[2] ) . '</h' . $level . '>';
			continue;
		}

		if ( preg_match( '/^-\s+(.+)$/', $trimmed, $matches ) ) {
			if ( $in_olist ) {
				$html    .= '</ol>';
				$in_olist = false;
			}
			if ( ! $in_list ) {
				$html   .= '<ul>';
				$in_list = true;
			}
			$html .= '<li>' . multirent_companion_readme_line_html( $matches[1] ) . '</li>';
			continue;
		}

		if ( preg_match( '/^\d+\.\s+(.+)$/', $trimmed, $matches ) ) {
			if ( $in_list ) {
				$html   .= '</ul>';
				$in_list = false;
			}
			if ( ! $in_olist ) {
				$html    .= '<ol>';
				$in_olist = true;
			}
			$html .= '<li>' . multirent_companion_readme_line_html( $matches[1] ) . '</li>';
			continue;
		}

		if ( $in_list ) {
			$html   .= '</ul>';
			$in_list = false;
		}
		if ( $in_olist ) {
			$html    .= '</ol>';
			$in_olist = false;
		}
		$html .= '<p>' . multirent_companion_readme_line_html( $trimmed ) . '</p>';
	}

	if ( $in_list ) {
		$html .= '</ul>';
	}
	if ( $in_olist ) {
		$html .= '</ol>';
	}
	if ( $in_code ) {
		$html .= '</code></pre>';
	}

	return $html;
}

function multirent_companion_admin_assets( $hook_suffix ) {
	$screen = get_current_screen();
	if ( ! $screen ) {
		return;
	}

	$is_multirent_screen = str_contains( $hook_suffix, 'multirent' ) || 'rental_unit' === $screen->post_type || 'edit-rental_amenity' === $screen->id;
	if ( ! $is_multirent_screen ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script( 'multirent-companion-admin', plugins_url( 'assets/js/admin.js', __FILE__ ), array(), MULTIRENT_COMPANION_VERSION, true );
	wp_enqueue_style( 'multirent-companion-admin', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), MULTIRENT_COMPANION_VERSION );

	if ( 'rental_unit' === $screen->post_type && function_exists( 'use_block_editor_for_post_type' ) && use_block_editor_for_post_type( 'rental_unit' ) ) {
		wp_enqueue_script(
			'multirent-companion-unit-sidebar',
			plugins_url( 'assets/js/unit-sidebar.js', __FILE__ ),
			array( 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data', 'wp-i18n', 'wp-block-editor' ),
			MULTIRENT_COMPANION_VERSION,
			true
		);
		wp_localize_script(
			'multirent-companion-unit-sidebar',
			'MultiRentUnitSidebar',
			array(
				'fields'       => multirent_companion_unit_sidebar_fields(),
				'qrImageKey'   => '_qr_code_image_id',
				'qrImageLabel' => esc_html__( 'QR code image', 'multirent-companion' ),
				'qrImageHelp'  => esc_html__( 'Optional QR code image shown in an extra tile on the apartment detail page.', 'multirent-companion' ),
				'qrImageButton'=> esc_html__( 'Choose QR code image', 'multirent-companion' ),
				'qrImageRemove'=> esc_html__( 'Remove QR code image', 'multirent-companion' ),
				'panelTitle'   => esc_html__( 'Apartment Details', 'multirent-companion' ),
				'imageHelp'    => esc_html__( 'Use the Apartment Images box below the editor for the main apartment photo and extra gallery photos. The QR code image, video link, and apartment map fields are in Apartment Details. Gallery images can be reordered before saving.', 'multirent-companion' ),
				'publishHelp'  => esc_html__( 'After filling these fields, click Publish or Update.', 'multirent-companion' ),
			)
		);
	}

	wp_localize_script(
		'multirent-companion-admin',
		'MultiRentAdmin',
		array(
			'chooseImage'   => esc_html__( 'Choose image', 'multirent-companion' ),
			'useImage'      => esc_html__( 'Use this image', 'multirent-companion' ),
			'chooseGallery' => esc_html__( 'Choose apartment gallery images', 'multirent-companion' ),
			'useGallery'    => esc_html__( 'Use selected images', 'multirent-companion' ),
			'noImages'      => esc_html__( 'No gallery images selected', 'multirent-companion' ),
			'moveUp'        => esc_html__( 'Move up', 'multirent-companion' ),
			'moveDown'      => esc_html__( 'Move down', 'multirent-companion' ),
			'removeImage'   => esc_html__( 'Remove image', 'multirent-companion' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'multirent_companion_admin_assets' );

function multirent_companion_media_field( $field_name, $attachment_id, $button_label, $remove_label ) {
	$image_url = $attachment_id ? wp_get_attachment_image_url( $attachment_id, 'medium' ) : '';
	?>
	<div class="multirent-media-control" data-multirent-media-control>
		<input type="hidden" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $attachment_id ); ?>" data-multirent-media-id>
		<div class="multirent-media-preview" data-multirent-media-preview>
			<?php if ( $image_url ) : ?>
				<img src="<?php echo esc_url( $image_url ); ?>" alt="">
			<?php else : ?>
				<span><?php esc_html_e( 'No image selected', 'multirent-companion' ); ?></span>
			<?php endif; ?>
		</div>
		<p>
			<button type="button" class="button" data-multirent-media-select><?php echo esc_html( $button_label ); ?></button>
			<button type="button" class="button button-link-delete" data-multirent-media-remove><?php echo esc_html( $remove_label ); ?></button>
		</p>
	</div>
	<?php
}

function multirent_companion_gallery_media_field( $field_name, $attachment_ids ) {
	$ids = multirent_companion_sanitize_gallery_image_ids( $attachment_ids );
	$ids = $ids ? array_map( 'absint', explode( ',', $ids ) ) : array();
	?>
	<div class="multirent-gallery-control" data-multirent-gallery-control>
		<input type="hidden" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>" data-multirent-gallery-ids>
		<div class="multirent-gallery-preview" data-multirent-gallery-preview>
			<?php if ( $ids ) : ?>
				<?php foreach ( $ids as $attachment_id ) : ?>
					<?php $image_url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' ); ?>
					<?php if ( $image_url ) : ?>
						<div class="multirent-gallery-preview-item" data-multirent-gallery-item data-attachment-id="<?php echo esc_attr( $attachment_id ); ?>">
							<img src="<?php echo esc_url( $image_url ); ?>" alt="">
							<div class="multirent-gallery-preview-actions">
								<button type="button" class="button button-small" data-multirent-gallery-move="up"><?php esc_html_e( 'Move up', 'multirent-companion' ); ?></button>
								<button type="button" class="button button-small" data-multirent-gallery-move="down"><?php esc_html_e( 'Move down', 'multirent-companion' ); ?></button>
								<button type="button" class="button button-small button-link-delete" data-multirent-gallery-remove-item><?php esc_html_e( 'Remove', 'multirent-companion' ); ?></button>
							</div>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php else : ?>
				<span><?php esc_html_e( 'No gallery images selected', 'multirent-companion' ); ?></span>
			<?php endif; ?>
		</div>
		<p>
			<button type="button" class="button" data-multirent-gallery-select><?php esc_html_e( 'Choose gallery images', 'multirent-companion' ); ?></button>
			<button type="button" class="button button-link-delete" data-multirent-gallery-remove><?php esc_html_e( 'Remove gallery images', 'multirent-companion' ); ?></button>
		</p>
	</div>
	<?php
}

function multirent_companion_page_select_field( $role, $field_name, $selected_page_id ) {
	$roles = multirent_companion_page_roles();
	if ( empty( $roles[ $role ] ) ) {
		return;
	}
	if ( ! multirent_companion_page_exists( $selected_page_id ) ) {
		$fallback_page    = multirent_companion_role_page( $role );
		$selected_page_id = $fallback_page ? $fallback_page->ID : 0;
	}

	$pages = get_pages(
		array(
			'post_status' => 'publish,draft,pending,private',
			'sort_column' => 'post_title',
		)
	);
	?>
	<select name="<?php echo esc_attr( $field_name ); ?>" id="multirent-<?php echo esc_attr( $role ); ?>-page-id">
		<?php foreach ( $pages as $page ) : ?>
			<option value="<?php echo esc_attr( $page->ID ); ?>" <?php selected( absint( $selected_page_id ), $page->ID ); ?>><?php echo esc_html( $page->post_title . ' (#' . $page->ID . ')' ); ?></option>
		<?php endforeach; ?>
	</select>
	<?php
}

function multirent_companion_recommended_plugins() {
	return array(
		'cookie-law-info'        => array(
			'name'        => esc_html__( 'CookieYes - Cookie Banner for Cookie Consent', 'multirent-companion' ),
			'plugin_file' => 'cookie-law-info/cookie-law-info.php',
			'purpose'     => esc_html__( 'Cookie consent banner and GDPR/CCPA cookie notice workflow.', 'multirent-companion' ),
		),
		'seo-by-rank-math'       => array(
			'name'        => esc_html__( 'Rank Math SEO', 'multirent-companion' ),
			'plugin_file' => 'seo-by-rank-math/rank-math.php',
			'purpose'     => esc_html__( 'SEO titles, schema, sitemap, redirects, and search optimization.', 'multirent-companion' ),
		),
		'widget-google-reviews'  => array(
			'name'        => esc_html__( 'Rich Showcase for Google Reviews', 'multirent-companion' ),
			'plugin_file' => 'widget-google-reviews/widget-google-reviews.php',
			'purpose'     => esc_html__( 'Display Google Reviews through shortcodes and widgets.', 'multirent-companion' ),
		),
	);
}

function multirent_companion_plugin_state( $plugin_file ) {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	if ( is_plugin_active( $plugin_file ) ) {
		return 'active';
	}

	return file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) ? 'installed' : 'missing';
}

function multirent_companion_sanitize_settings( $input, $scope = null ) {
	$defaults = multirent_companion_default_settings();
	$current  = multirent_companion_settings();
	$output   = array();
	$scope    = is_array( $scope ) ? array_map( 'sanitize_key', $scope ) : null;
	foreach ( $defaults as $key => $default ) {
		if ( is_array( $scope ) && ! in_array( $key, $scope, true ) ) {
			$output[ $key ] = isset( $current[ $key ] ) ? $current[ $key ] : $default;
			continue;
		}

		$value = isset( $input[ $key ] ) ? wp_unslash( $input[ $key ] ) : $default;
		if ( in_array( $key, array( 'show_hero_section', 'show_intro_section', 'show_stats_section', 'show_front_page_rentals', 'show_reviews', 'show_seo_note', 'show_migration_note', 'show_contact_cta', 'show_apartments_page', 'show_contact_page', 'show_local_page', 'show_contact_details', 'show_booking_help', 'show_contact_map', 'show_contact_content', 'show_contact_form', 'show_contact_map_note', 'show_local_guides', 'show_local_highlights', 'show_local_activities', 'show_local_links', 'show_local_content', 'use_custom_colors' ), true ) ) {
			$output[ $key ] = ! empty( $input[ $key ] ) ? '1' : '0';
		} elseif ( 'front_page_rental_count' === $key ) {
			$output[ $key ] = (string) min( 50, max( 1, absint( $value ) ) );
		} elseif ( in_array( $key, array( 'apartments_page_id', 'contact_page_id', 'local_page_id', 'contact_qr_code_image_id' ), true ) ) {
			$output[ $key ] = (string) absint( $value );
		} elseif ( in_array( $key, array( 'hero_image', 'page_logo' ), true ) ) {
			$output[ $key ] = absint( $value );
		} elseif ( 'contact_email' === $key ) {
			$output[ $key ] = sanitize_email( $value );
		} elseif ( str_starts_with( $key, 'color_' ) ) {
			$output[ $key ] = sanitize_hex_color( $value );
		} elseif ( str_ends_with( $key, '_url' ) ) {
			$output[ $key ] = esc_url_raw( $value );
		} else {
			$output[ $key ] = sanitize_textarea_field( $value );
		}
	}
	return $output;
}

function multirent_companion_handle_setup_actions() {
	if ( ! current_user_can( 'manage_options' ) || empty( $_POST['multirent_action'] ) ) {
		return;
	}

	check_admin_referer( 'multirent_setup_action', 'multirent_setup_nonce' );
	$action = sanitize_key( wp_unslash( $_POST['multirent_action'] ) );
	$scope  = isset( $_POST['multirent_settings_scope'] ) ? array_filter( array_map( 'trim', explode( ',', sanitize_text_field( wp_unslash( $_POST['multirent_settings_scope'] ) ) ) ) ) : null;

	if ( in_array( $action, array( 'save_settings', 'save_apartments_page', 'save_contact_page', 'save_local_page' ), true ) ) {
		$settings = isset( $_POST['multirent_settings'] ) && is_array( $_POST['multirent_settings'] ) ? multirent_companion_sanitize_settings( $_POST['multirent_settings'], $scope ) : multirent_companion_settings();
		update_option( 'multirent_settings', $settings );
		multirent_companion_sync_optional_page_visibility( $settings );

		if ( 'save_apartments_page' === $action ) {
			$template_result = isset( $_POST['multirent_apartments_template'] ) ? multirent_companion_save_apartments_page_template( $_POST['multirent_apartments_template'] ) : null;
			if ( is_wp_error( $template_result ) ) {
				add_settings_error( 'multirent_messages', 'apartments_template_error', $template_result->get_error_message(), 'error' );
			} else {
				add_settings_error( 'multirent_messages', 'apartments_page_saved', esc_html__( 'Apartments page settings saved.', 'multirent-companion' ), 'updated' );
			}
		} elseif ( 'save_contact_page' === $action ) {
			$template_result = isset( $_POST['multirent_contact_template'] ) ? multirent_companion_save_role_page_template( 'contact', $_POST['multirent_contact_template'] ) : null;
			if ( is_wp_error( $template_result ) ) {
				add_settings_error( 'multirent_messages', 'contact_template_error', $template_result->get_error_message(), 'error' );
			} else {
				add_settings_error( 'multirent_messages', 'contact_page_saved', esc_html__( 'Contact page settings saved.', 'multirent-companion' ), 'updated' );
			}
		} elseif ( 'save_local_page' === $action ) {
			$template_result = isset( $_POST['multirent_local_template'] ) ? multirent_companion_save_role_page_template( 'local', $_POST['multirent_local_template'] ) : null;
			if ( is_wp_error( $template_result ) ) {
				add_settings_error( 'multirent_messages', 'local_template_error', $template_result->get_error_message(), 'error' );
			} else {
				add_settings_error( 'multirent_messages', 'local_page_saved', esc_html__( 'Local page settings saved.', 'multirent-companion' ), 'updated' );
			}
		} else {
			add_settings_error( 'multirent_messages', 'settings_saved', esc_html__( 'MultiRent settings saved.', 'multirent-companion' ), 'updated' );
		}
	}

	if ( 'apply_top_menu' === $action ) {
		$settings = isset( $_POST['multirent_settings'] ) && is_array( $_POST['multirent_settings'] ) ? multirent_companion_sanitize_settings( $_POST['multirent_settings'], $scope ) : multirent_companion_settings();
		update_option( 'multirent_settings', $settings );
		multirent_companion_sync_optional_page_visibility( $settings );
		multirent_companion_apply_top_menu( $settings['menu_items'] );
		add_settings_error( 'multirent_messages', 'menu_applied', esc_html__( 'Top menu updated and assigned to the theme header.', 'multirent-companion' ), 'updated' );
	}

	if ( 'create_units' === $action ) {
		$count = isset( $_POST['unit_count'] ) ? max( 1, min( 50, absint( $_POST['unit_count'] ) ) ) : 1;
		multirent_companion_create_units( $count );
		add_settings_error( 'multirent_messages', 'units_created', sprintf( esc_html__( 'Created %d starter rental units.', 'multirent-companion' ), $count ), 'updated' );
	}

	if ( 'create_starter_site' === $action ) {
		multirent_companion_create_starter_site();
		add_settings_error( 'multirent_messages', 'site_created', esc_html__( 'Starter pages, menu, and amenities created.', 'multirent-companion' ), 'updated' );
	}

	if ( 'create_demo_content' === $action ) {
		$result = multirent_companion_create_demo_content();
		if ( is_wp_error( $result ) ) {
			add_settings_error( 'multirent_messages', 'demo_failed', $result->get_error_message(), 'error' );
		} else {
			add_settings_error( 'multirent_messages', 'demo_created', esc_html__( 'Demo pages, apartments, amenities, menu, and settings created. You can remove them later with Remove Demo Content.', 'multirent-companion' ), 'updated' );
		}
	}

	if ( 'remove_demo_content' === $action ) {
		$deleted = multirent_companion_remove_demo_content();
		add_settings_error( 'multirent_messages', 'demo_removed', sprintf( esc_html__( 'Removed %d demo pages/apartments and restored the previous MultiRent settings when available.', 'multirent-companion' ), $deleted ), 'updated' );
	}

}
add_action( 'admin_init', 'multirent_companion_handle_setup_actions' );

function multirent_companion_optional_pages() {
	return array(
		'Apartments' => 'show_apartments_page',
		'Contact'    => 'show_contact_page',
		'Local'      => 'show_local_page',
	);
}

function multirent_companion_page_roles() {
	return array(
		'apartments' => array(
			'title'        => 'Apartments',
			'setting_key'  => 'apartments_page_id',
			'show_key'     => 'show_apartments_page',
			'template'     => 'template-apartments-grid.php',
			'content'      => 'This page uses a ready-made MultiRent apartment listing template. Edit this text if you use the Featured Guide template.',
		),
		'contact'    => array(
			'title'        => 'Contact',
			'setting_key'  => 'contact_page_id',
			'show_key'     => 'show_contact_page',
			'template'     => 'template-contact.php',
			'content'      => 'Add extra contact notes, house rules, payment instructions, or a contact-form shortcode here if you want page-editor content to appear.',
		),
		'local'      => array(
			'title'        => 'Local',
			'setting_key'  => 'local_page_id',
			'show_key'     => 'show_local_page',
			'template'     => 'template-local.php',
			'content'      => 'Add extra local recommendations, seasonal notes, or guest instructions here if you want page-editor content to appear.',
		),
	);
}

function multirent_companion_get_page_by_title( $page_title ) {
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'any',
			'title'          => $page_title,
			'posts_per_page' => 1,
			'orderby'        => 'ID',
			'order'          => 'ASC',
		)
	);

	return $pages ? $pages[0] : null;
}

function multirent_companion_page_exists( $page_id ) {
	$page = $page_id ? get_post( absint( $page_id ) ) : null;
	return $page && 'page' === $page->post_type && 'trash' !== $page->post_status ? $page : null;
}

function multirent_companion_role_page( $role, $settings = null ) {
	$roles = multirent_companion_page_roles();
	if ( empty( $roles[ $role ] ) ) {
		return null;
	}

	$settings = is_array( $settings ) ? $settings : multirent_companion_settings();
	$page     = multirent_companion_page_exists( isset( $settings[ $roles[ $role ]['setting_key'] ] ) ? $settings[ $roles[ $role ]['setting_key'] ] : 0 );
	return $page ? $page : multirent_companion_get_page_by_title( $roles[ $role ]['title'] );
}

function multirent_companion_sync_optional_page_visibility( $settings = null ) {
	$settings = is_array( $settings ) ? $settings : multirent_companion_settings();

	foreach ( multirent_companion_page_roles() as $role => $role_config ) {
		$page = multirent_companion_role_page( $role, $settings );
		if ( ! $page ) {
			continue;
		}

		wp_update_post(
			array(
				'ID'          => $page->ID,
				'post_status' => '1' === (string) $settings[ $role_config['show_key'] ] ? 'publish' : 'draft',
			)
		);
	}
}

function multirent_companion_apartments_page_templates() {
	return array(
		'template-apartments-grid.php'     => array(
			'label'       => esc_html__( 'Apartments - Grid', 'multirent-companion' ),
			'description' => esc_html__( 'A simple apartment card grid for the clearest overview of all rental units.', 'multirent-companion' ),
			'layout'      => esc_html__( 'Hero title, page intro, then a responsive grid of apartment cards.', 'multirent-companion' ),
		),
		'template-apartments-featured.php' => array(
			'label'       => esc_html__( 'Apartments - Featured Guide', 'multirent-companion' ),
			'description' => esc_html__( 'A richer guide-style listing for pages that need intro copy before the rental cards.', 'multirent-companion' ),
			'layout'      => esc_html__( 'Page heading, editor content, featured intro, then apartment cards.', 'multirent-companion' ),
		),
		'template-apartments-compact.php'  => array(
			'label'       => esc_html__( 'Apartments - Compact List', 'multirent-companion' ),
			'description' => esc_html__( 'A tighter comparison list for scanning many apartments quickly.', 'multirent-companion' ),
			'layout'      => esc_html__( 'Compact rows with apartment image, summary, guest capacity, and details link.', 'multirent-companion' ),
		),
	);
}

function multirent_companion_contact_page_templates() {
	return array(
		'template-contact.php'         => array(
			'label'       => esc_html__( 'Contact / Booking Inquiry', 'multirent-companion' ),
			'description' => esc_html__( 'A balanced contact page with details beside booking guidance, map, content, and form sections.', 'multirent-companion' ),
			'layout'      => esc_html__( 'Side contact card plus main inquiry, map, page content, and form area.', 'multirent-companion' ),
		),
		'template-contact-split.php'   => array(
			'label'       => esc_html__( 'Contact - Split Map', 'multirent-companion' ),
			'description' => esc_html__( 'A map-forward layout for guests who need arrival context before sending an inquiry.', 'multirent-companion' ),
			'layout'      => esc_html__( 'Large map and arrival note first, followed by contact details and inquiry sections.', 'multirent-companion' ),
		),
		'template-contact-compact.php' => array(
			'label'       => esc_html__( 'Contact - Compact', 'multirent-companion' ),
			'description' => esc_html__( 'A compact stacked layout for simpler sites or shorter contact pages.', 'multirent-companion' ),
			'layout'      => esc_html__( 'Single-column contact details, booking checklist, editor content, form, and optional map.', 'multirent-companion' ),
		),
	);
}

function multirent_companion_local_page_templates() {
	return array(
		'template-local.php'         => array(
			'label'       => esc_html__( 'Local Information', 'multirent-companion' ),
			'description' => esc_html__( 'A full local guide with main content and useful links in a sidebar.', 'multirent-companion' ),
			'layout'      => esc_html__( 'Guide cards, highlights, activities, editor content, and travel links sidebar.', 'multirent-companion' ),
		),
		'template-local-compact.php' => array(
			'label'       => esc_html__( 'Local - Compact Guide', 'multirent-companion' ),
			'description' => esc_html__( 'A simpler stacked guide for shorter local information pages.', 'multirent-companion' ),
			'layout'      => esc_html__( 'Single-column guide sections, highlights, activities, links, and editor content.', 'multirent-companion' ),
		),
		'template-local-featured.php' => array(
			'label'       => esc_html__( 'Local - Featured Guide', 'multirent-companion' ),
			'description' => esc_html__( 'A guide-first layout that emphasizes the plan-your-stay cards before other information.', 'multirent-companion' ),
			'layout'      => esc_html__( 'Featured guide cards first, then highlights, activities, travel links, and editor content.', 'multirent-companion' ),
		),
	);
}

function multirent_companion_role_page_templates( $role ) {
	if ( 'apartments' === $role ) {
		return multirent_companion_apartments_page_templates();
	}

	if ( 'contact' === $role ) {
		return multirent_companion_contact_page_templates();
	}

	if ( 'local' === $role ) {
		return multirent_companion_local_page_templates();
	}

	return array();
}

function multirent_companion_role_page_template( $role ) {
	$roles     = multirent_companion_page_roles();
	$page      = multirent_companion_role_page( $role );
	$template  = $page ? get_post_meta( $page->ID, '_wp_page_template', true ) : '';
	$templates = multirent_companion_role_page_templates( $role );
	$default   = isset( $roles[ $role ]['template'] ) ? $roles[ $role ]['template'] : '';

	return isset( $templates[ $template ] ) ? $template : $default;
}

function multirent_companion_save_role_page_template( $role, $template ) {
	$templates = multirent_companion_role_page_templates( $role );
	$template  = sanitize_text_field( wp_unslash( $template ) );

	if ( ! isset( $templates[ $template ] ) ) {
		return new WP_Error( 'invalid_page_template', esc_html__( 'Choose a valid page template.', 'multirent-companion' ) );
	}

	$page = multirent_companion_role_page( $role );
	if ( ! $page ) {
		return new WP_Error( 'missing_role_page', esc_html__( 'Choose a WordPress page first.', 'multirent-companion' ) );
	}

	update_post_meta( $page->ID, '_wp_page_template', $template );
	return true;
}

function multirent_companion_apartments_page_template() {
	return multirent_companion_role_page_template( 'apartments' );
}

function multirent_companion_save_apartments_page_template( $template ) {
	return multirent_companion_save_role_page_template( 'apartments', $template );
}

function multirent_companion_apply_role_page_templates() {
	foreach ( multirent_companion_page_roles() as $role => $role_config ) {
		if ( 'apartments' === $role || empty( $role_config['template'] ) ) {
			continue;
		}

		$page = multirent_companion_role_page( $role );
		if ( $page ) {
			update_post_meta( $page->ID, '_wp_page_template', $role_config['template'] );
		}
	}
}

function multirent_companion_content_author_id() {
	$user = get_user_by( 'login', 'multirent' );
	if ( ! $user ) {
		$user = get_user_by( 'email', 'multirent-content-author@example.invalid' );
	}

	if ( $user ) {
		wp_update_user(
			array(
				'ID'           => $user->ID,
				'display_name' => 'MultiRent',
				'nickname'     => 'MultiRent',
			)
		);
		return (int) $user->ID;
	}

	$user_id = wp_insert_user(
		array(
			'user_login'   => 'multirent',
			'user_pass'    => wp_generate_password( 32, true, true ),
			'user_email'   => 'multirent-content-author@example.invalid',
			'user_nicename' => 'multirent',
			'display_name' => 'MultiRent',
			'nickname'     => 'MultiRent',
			'role'         => 'author',
		)
	);

	if ( is_wp_error( $user_id ) ) {
		return get_current_user_id();
	}

	update_user_meta( $user_id, 'show_admin_bar_front', 'false' );

	return (int) $user_id;
}

function multirent_companion_create_units( $count ) {
	$author_id = multirent_companion_content_author_id();

	for ( $index = 1; $index <= $count; $index++ ) {
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'rental_unit',
				'post_status'  => 'publish',
				'post_author'  => $author_id,
				'post_title'   => sprintf( 'Rental Unit %d', $index ),
				'post_excerpt' => 'A configurable rental unit. Replace this text with your own guest-facing summary.',
				'post_content' => 'Describe the sleeping layout, kitchen, outdoor space, nearby attractions, house rules, and anything guests should know before booking.',
				'menu_order'   => $index,
			)
		);

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, '_capacity', '2-4' );
			update_post_meta( $post_id, '_bedrooms', '1' );
			update_post_meta( $post_id, '_bathrooms', '1' );
			update_post_meta( $post_id, '_size', '45 m2' );
		}
	}
}

function multirent_companion_create_starter_site() {
	$author_id = multirent_companion_content_author_id();
	$pages = array(
		'Home'       => array( 'content' => '', 'template' => '' ),
		'Apartments' => array( 'content' => 'This page uses a ready-made MultiRent apartment listing template. Edit this text if you use the Featured Guide template.', 'template' => 'template-apartments-grid.php' ),
		'Contact'    => array( 'content' => 'Add extra contact notes, house rules, payment instructions, or a contact-form shortcode here if you want page-editor content to appear.', 'template' => 'template-contact.php' ),
		'Local'      => array( 'content' => 'Add extra local recommendations, seasonal notes, or guest instructions here if you want page-editor content to appear.', 'template' => 'template-local.php' ),
	);

	$page_ids = array();
	foreach ( $pages as $title => $page_data ) {
		$page = multirent_companion_get_page_by_title( $title );
		if ( ! $page ) {
			$page_id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_author' => $author_id, 'post_title' => $title, 'post_content' => $page_data['content'] ) );
		} else {
			$page_id = $page->ID;
		}

		if ( $page_id && ! is_wp_error( $page_id ) && $page_data['template'] ) {
			update_post_meta( $page_id, '_wp_page_template', $page_data['template'] );
		}
		$page_ids[ $title ] = $page_id;
	}

	if ( ! empty( $page_ids['Home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $page_ids['Home'] );
	}

	multirent_companion_ensure_default_amenities();

	$settings = multirent_companion_settings();
	if ( ! empty( $page_ids['Apartments'] ) ) {
		$settings['apartments_page_id'] = (string) absint( $page_ids['Apartments'] );
	}
	if ( ! empty( $page_ids['Contact'] ) ) {
		$settings['contact_page_id'] = (string) absint( $page_ids['Contact'] );
	}
	if ( ! empty( $page_ids['Local'] ) ) {
		$settings['local_page_id'] = (string) absint( $page_ids['Local'] );
	}
	update_option( 'multirent_settings', $settings );

	multirent_companion_sync_optional_page_visibility( $settings );
	multirent_companion_apply_top_menu( multirent_companion_settings()['menu_items'] );

	flush_rewrite_rules();
}

function multirent_companion_demo_marker() {
	return 'multirent-demo-content-v1';
}

function multirent_companion_demo_previous_option_key() {
	return 'multirent_demo_previous_options_v1';
}

function multirent_companion_demo_image_specs() {
	return array(
		'hero' => array(
			'title'    => 'MultiRent Demo Hero Image',
			'filename' => 'multirent-demo-hero.jpg',
			'label'    => 'Demo Seaside House',
			'subtitle' => 'Preview hero image',
			'colors'   => array( array( 6, 54, 77 ), array( 8, 126, 164 ), array( 223, 243, 251 ) ),
		),
		'demo-contact-qr' => array(
			'title'    => 'MultiRent Demo Contact QR Code',
			'filename' => 'multirent-demo-contact-qr.jpg',
			'label'    => 'Contact QR',
			'subtitle' => 'Demo booking code',
			'colors'   => array( array( 16, 32, 51 ), array( 8, 126, 164 ), array( 255, 255, 255 ) ),
		),
		'demo-sea-view-studio-qr' => array(
			'title'    => 'MultiRent Demo Sea View Studio QR Code',
			'filename' => 'multirent-demo-sea-view-studio-qr.jpg',
			'label'    => 'Studio QR',
			'subtitle' => 'Demo unit code',
			'colors'   => array( array( 16, 32, 51 ), array( 32, 158, 181 ), array( 255, 255, 255 ) ),
		),
		'demo-family-apartment-qr' => array(
			'title'    => 'MultiRent Demo Family Apartment QR Code',
			'filename' => 'multirent-demo-family-apartment-qr.jpg',
			'label'    => 'Family QR',
			'subtitle' => 'Demo unit code',
			'colors'   => array( array( 16, 32, 51 ), array( 76, 143, 105 ), array( 255, 255, 255 ) ),
		),
		'demo-sea-view-studio' => array(
			'title'    => 'MultiRent Demo Sea View Studio Image',
			'filename' => 'multirent-demo-sea-view-studio.jpg',
			'label'    => 'Sea View Studio',
			'subtitle' => 'Balcony and kitchen',
			'colors'   => array( array( 7, 78, 110 ), array( 32, 158, 181 ), array( 236, 248, 252 ) ),
		),
		'demo-sea-view-studio-gallery-1' => array(
			'title'    => 'MultiRent Demo Sea View Studio Gallery 1',
			'filename' => 'multirent-demo-sea-view-studio-gallery-1.jpg',
			'label'    => 'Studio Balcony',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 9, 68, 95 ), array( 88, 180, 197 ), array( 232, 247, 250 ) ),
		),
		'demo-sea-view-studio-gallery-2' => array(
			'title'    => 'MultiRent Demo Sea View Studio Gallery 2',
			'filename' => 'multirent-demo-sea-view-studio-gallery-2.jpg',
			'label'    => 'Studio Kitchen',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 28, 74, 94 ), array( 117, 166, 179 ), array( 245, 251, 253 ) ),
		),
		'demo-sea-view-studio-gallery-3' => array(
			'title'    => 'MultiRent Demo Sea View Studio Gallery 3',
			'filename' => 'multirent-demo-sea-view-studio-gallery-3.jpg',
			'label'    => 'Studio Bathroom',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 18, 83, 109 ), array( 91, 159, 179 ), array( 239, 249, 251 ) ),
		),
		'demo-sea-view-studio-gallery-4' => array(
			'title'    => 'MultiRent Demo Sea View Studio Gallery 4',
			'filename' => 'multirent-demo-sea-view-studio-gallery-4.jpg',
			'label'    => 'Studio Sea View',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 5, 77, 111 ), array( 38, 141, 178 ), array( 226, 244, 250 ) ),
		),
		'demo-sea-view-studio-gallery-5' => array(
			'title'    => 'MultiRent Demo Sea View Studio Gallery 5',
			'filename' => 'multirent-demo-sea-view-studio-gallery-5.jpg',
			'label'    => 'Studio Dining Nook',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 21, 87, 112 ), array( 83, 151, 172 ), array( 241, 249, 251 ) ),
		),
		'demo-sea-view-studio-gallery-6' => array(
			'title'    => 'MultiRent Demo Sea View Studio Gallery 6',
			'filename' => 'multirent-demo-sea-view-studio-gallery-6.jpg',
			'label'    => 'Studio Entrance',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 12, 70, 98 ), array( 71, 138, 166 ), array( 232, 246, 250 ) ),
		),
		'demo-family-apartment' => array(
			'title'    => 'MultiRent Demo Family Apartment Image',
			'filename' => 'multirent-demo-family-apartment.jpg',
			'label'    => 'Family Apartment',
			'subtitle' => 'Two bedrooms and parking',
			'colors'   => array( array( 18, 83, 64 ), array( 76, 143, 105 ), array( 241, 248, 237 ) ),
		),
		'demo-family-apartment-gallery-1' => array(
			'title'    => 'MultiRent Demo Family Apartment Gallery 1',
			'filename' => 'multirent-demo-family-apartment-gallery-1.jpg',
			'label'    => 'Family Living Room',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 31, 92, 74 ), array( 99, 157, 111 ), array( 238, 247, 236 ) ),
		),
		'demo-family-apartment-gallery-2' => array(
			'title'    => 'MultiRent Demo Family Apartment Gallery 2',
			'filename' => 'multirent-demo-family-apartment-gallery-2.jpg',
			'label'    => 'Family Bedroom',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 46, 96, 68 ), array( 138, 180, 122 ), array( 249, 252, 244 ) ),
		),
		'demo-family-apartment-gallery-3' => array(
			'title'    => 'MultiRent Demo Family Apartment Gallery 3',
			'filename' => 'multirent-demo-family-apartment-gallery-3.jpg',
			'label'    => 'Family Kitchen',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 41, 91, 70 ), array( 119, 166, 98 ), array( 244, 250, 239 ) ),
		),
		'demo-family-apartment-gallery-4' => array(
			'title'    => 'MultiRent Demo Family Apartment Gallery 4',
			'filename' => 'multirent-demo-family-apartment-gallery-4.jpg',
			'label'    => 'Family Balcony',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 20, 101, 77 ), array( 91, 150, 126 ), array( 239, 248, 241 ) ),
		),
		'demo-family-apartment-gallery-5' => array(
			'title'    => 'MultiRent Demo Family Apartment Gallery 5',
			'filename' => 'multirent-demo-family-apartment-gallery-5.jpg',
			'label'    => 'Family Bathroom',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 38, 87, 69 ), array( 112, 156, 116 ), array( 246, 251, 241 ) ),
		),
		'demo-family-apartment-gallery-6' => array(
			'title'    => 'MultiRent Demo Family Apartment Gallery 6',
			'filename' => 'multirent-demo-family-apartment-gallery-6.jpg',
			'label'    => 'Family Second Bedroom',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 27, 86, 75 ), array( 97, 147, 135 ), array( 241, 250, 244 ) ),
		),
		'demo-garden-terrace-suite' => array(
			'title'    => 'MultiRent Demo Garden Terrace Suite Image',
			'filename' => 'multirent-demo-garden-terrace-suite.jpg',
			'label'    => 'Garden Terrace Suite',
			'subtitle' => 'Terrace and BBQ',
			'colors'   => array( array( 78, 68, 42 ), array( 174, 128, 61 ), array( 252, 246, 230 ) ),
		),
		'demo-garden-terrace-suite-gallery-1' => array(
			'title'    => 'MultiRent Demo Garden Terrace Suite Gallery 1',
			'filename' => 'multirent-demo-garden-terrace-suite-gallery-1.jpg',
			'label'    => 'Garden Terrace',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 92, 76, 42 ), array( 188, 139, 68 ), array( 253, 247, 230 ) ),
		),
		'demo-garden-terrace-suite-gallery-2' => array(
			'title'    => 'MultiRent Demo Garden Terrace Suite Gallery 2',
			'filename' => 'multirent-demo-garden-terrace-suite-gallery-2.jpg',
			'label'    => 'Outdoor Dining',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 86, 87, 53 ), array( 170, 150, 83 ), array( 252, 249, 235 ) ),
		),
		'demo-garden-terrace-suite-gallery-3' => array(
			'title'    => 'MultiRent Demo Garden Terrace Suite Gallery 3',
			'filename' => 'multirent-demo-garden-terrace-suite-gallery-3.jpg',
			'label'    => 'Terrace Kitchen',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 102, 82, 46 ), array( 178, 130, 73 ), array( 251, 245, 228 ) ),
		),
		'demo-garden-terrace-suite-gallery-4' => array(
			'title'    => 'MultiRent Demo Garden Terrace Suite Gallery 4',
			'filename' => 'multirent-demo-garden-terrace-suite-gallery-4.jpg',
			'label'    => 'Garden Bedroom',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 78, 87, 48 ), array( 146, 153, 88 ), array( 249, 247, 232 ) ),
		),
		'demo-garden-terrace-suite-gallery-5' => array(
			'title'    => 'MultiRent Demo Garden Terrace Suite Gallery 5',
			'filename' => 'multirent-demo-garden-terrace-suite-gallery-5.jpg',
			'label'    => 'Garden Bathroom',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 92, 83, 49 ), array( 158, 142, 78 ), array( 250, 248, 235 ) ),
		),
		'demo-garden-terrace-suite-gallery-6' => array(
			'title'    => 'MultiRent Demo Garden Terrace Suite Gallery 6',
			'filename' => 'multirent-demo-garden-terrace-suite-gallery-6.jpg',
			'label'    => 'Garden Walkway',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 83, 94, 52 ), array( 134, 160, 90 ), array( 246, 249, 232 ) ),
		),
		'demo-pet-friendly-loft' => array(
			'title'    => 'MultiRent Demo Pet Friendly Loft Image',
			'filename' => 'multirent-demo-pet-friendly-loft.jpg',
			'label'    => 'Pet Friendly Loft',
			'subtitle' => 'Flexible demo stay',
			'colors'   => array( array( 72, 52, 105 ), array( 134, 111, 180 ), array( 245, 239, 252 ) ),
		),
		'demo-pet-friendly-loft-gallery-1' => array(
			'title'    => 'MultiRent Demo Pet Friendly Loft Gallery 1',
			'filename' => 'multirent-demo-pet-friendly-loft-gallery-1.jpg',
			'label'    => 'Loft Seating',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 88, 63, 116 ), array( 152, 123, 190 ), array( 248, 242, 253 ) ),
		),
		'demo-pet-friendly-loft-gallery-2' => array(
			'title'    => 'MultiRent Demo Pet Friendly Loft Gallery 2',
			'filename' => 'multirent-demo-pet-friendly-loft-gallery-2.jpg',
			'label'    => 'Loft Kitchen',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 72, 67, 118 ), array( 126, 138, 198 ), array( 244, 244, 253 ) ),
		),
		'demo-pet-friendly-loft-gallery-3' => array(
			'title'    => 'MultiRent Demo Pet Friendly Loft Gallery 3',
			'filename' => 'multirent-demo-pet-friendly-loft-gallery-3.jpg',
			'label'    => 'Loft Bathroom',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 78, 57, 112 ), array( 139, 119, 184 ), array( 246, 241, 252 ) ),
		),
		'demo-pet-friendly-loft-gallery-4' => array(
			'title'    => 'MultiRent Demo Pet Friendly Loft Gallery 4',
			'filename' => 'multirent-demo-pet-friendly-loft-gallery-4.jpg',
			'label'    => 'Pet Friendly Corner',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 92, 70, 122 ), array( 148, 129, 190 ), array( 249, 244, 253 ) ),
		),
		'demo-pet-friendly-loft-gallery-5' => array(
			'title'    => 'MultiRent Demo Pet Friendly Loft Gallery 5',
			'filename' => 'multirent-demo-pet-friendly-loft-gallery-5.jpg',
			'label'    => 'Loft Sleeping Area',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 82, 62, 118 ), array( 132, 117, 181 ), array( 246, 242, 252 ) ),
		),
		'demo-pet-friendly-loft-gallery-6' => array(
			'title'    => 'MultiRent Demo Pet Friendly Loft Gallery 6',
			'filename' => 'multirent-demo-pet-friendly-loft-gallery-6.jpg',
			'label'    => 'Loft Balcony View',
			'subtitle' => 'Extra demo gallery image',
			'colors'   => array( array( 68, 70, 124 ), array( 122, 132, 190 ), array( 243, 244, 253 ) ),
		),
	);
}

function multirent_companion_demo_gallery_image_keys( $rental_slug ) {
	return array( $rental_slug . '-gallery-1', $rental_slug . '-gallery-2', $rental_slug . '-gallery-3', $rental_slug . '-gallery-4', $rental_slug . '-gallery-5', $rental_slug . '-gallery-6' );
}

function multirent_companion_demo_attachment_by_filename( $filename ) {
	$attachments = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_multirent_demo_image_filename',
			'meta_value'     => $filename,
		)
	);

	return $attachments ? (int) $attachments[0] : 0;
}

function multirent_companion_create_demo_image_attachment( $key, $parent_id = 0 ) {
	$specs = multirent_companion_demo_image_specs();
	if ( empty( $specs[ $key ] ) || ! function_exists( 'imagecreatetruecolor' ) || ! function_exists( 'imagejpeg' ) ) {
		return 0;
	}

	$spec = $specs[ $key ];
	$existing_id = multirent_companion_demo_attachment_by_filename( $spec['filename'] );
	if ( $existing_id ) {
		wp_update_post(
			array(
				'ID'          => $existing_id,
				'post_author' => multirent_companion_content_author_id(),
				'post_parent' => $parent_id,
			)
		);
		return $existing_id;
	}

	$upload_dir = wp_upload_dir();
	if ( ! empty( $upload_dir['error'] ) ) {
		return 0;
	}

	$demo_dir = trailingslashit( $upload_dir['basedir'] ) . 'multirent-demo';
	if ( ! wp_mkdir_p( $demo_dir ) ) {
		return 0;
	}

	$path = trailingslashit( $demo_dir ) . $spec['filename'];
	$image = imagecreatetruecolor( 1600, 1000 );
	if ( ! $image ) {
		return 0;
	}

	$dark = imagecolorallocate( $image, $spec['colors'][0][0], $spec['colors'][0][1], $spec['colors'][0][2] );
	$mid = imagecolorallocate( $image, $spec['colors'][1][0], $spec['colors'][1][1], $spec['colors'][1][2] );
	$light = imagecolorallocate( $image, $spec['colors'][2][0], $spec['colors'][2][1], $spec['colors'][2][2] );
	$white = imagecolorallocate( $image, 255, 255, 255 );
	$ink = imagecolorallocate( $image, 16, 32, 51 );

	imagefilledrectangle( $image, 0, 0, 1600, 1000, $light );
	imagefilledrectangle( $image, 0, 0, 1600, 560, $mid );
	imagefilledrectangle( $image, 0, 560, 1600, 1000, $dark );
	imagefilledrectangle( $image, 160, 210, 1440, 790, $white );
	imagefilledrectangle( $image, 230, 290, 1370, 720, $light );
	imagefilledrectangle( $image, 320, 360, 600, 720, $mid );
	imagefilledrectangle( $image, 675, 360, 955, 720, $mid );
	imagefilledrectangle( $image, 1030, 360, 1310, 720, $mid );
	imagefilledrectangle( $image, 260, 765, 1340, 815, $dark );
	imagefilledellipse( $image, 1280, 170, 140, 140, $light );
	imagestring( $image, 5, 235, 250, strtoupper( $spec['label'] ), $ink );
	imagestring( $image, 4, 235, 275, $spec['subtitle'], $ink );
	imagestring( $image, 3, 235, 835, 'Generated MultiRent demo image - replace with real property photography', $white );

	imagejpeg( $image, $path, 88 );
	imagedestroy( $image );

	$filetype = wp_check_filetype( $path );
	$author_id = multirent_companion_content_author_id();
	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => $spec['title'],
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_author'    => $author_id,
			'post_parent'    => $parent_id,
		),
		$path
	);

	if ( is_wp_error( $attachment_id ) ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	$metadata = wp_generate_attachment_metadata( $attachment_id, $path );
	wp_update_attachment_metadata( $attachment_id, $metadata );
	update_post_meta( $attachment_id, '_multirent_demo_content', multirent_companion_demo_marker() );
	update_post_meta( $attachment_id, '_multirent_demo_image_filename', $spec['filename'] );
	update_post_meta( $attachment_id, '_multirent_demo_image_key', $key );
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', $spec['label'] . ' demo image' );

	return (int) $attachment_id;
}

function multirent_companion_upsert_demo_post( $args, $meta = array(), $amenity_slugs = array() ) {
	$args['post_author'] = multirent_companion_content_author_id();

	$existing = get_page_by_path( $args['post_name'], OBJECT, $args['post_type'] );
	if ( $existing && multirent_companion_demo_marker() === get_post_meta( $existing->ID, '_multirent_demo_content', true ) ) {
		$args['ID'] = $existing->ID;
		$post_id = wp_update_post( $args, true );
	} else {
		if ( $existing ) {
			$args['post_name'] = $args['post_name'] . '-' . wp_generate_password( 6, false, false );
		}

		$post_id = wp_insert_post( $args, true );
	}

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	foreach ( $meta as $key => $value ) {
		update_post_meta( $post_id, $key, $value );
	}

	update_post_meta( $post_id, '_multirent_demo_content', multirent_companion_demo_marker() );

	if ( $amenity_slugs ) {
		wp_set_object_terms( $post_id, $amenity_slugs, 'rental_amenity', false );
	}

	return $post_id;
}

function multirent_companion_demo_settings() {
	return array(
		'property_name'            => 'MultiRent Demo Seaside House',
		'hero_title'               => 'Demo apartments ready to explore',
		'hero_text'                => 'Four example apartments with realistic details, selected amenities, contact information, and local guide content.',
		'show_hero_section'        => '1',
		'hero_button_text'         => 'View demo apartments',
		'hero_button_url'          => '/demo-apartments/',
		'show_intro_section'       => '1',
		'intro_eyebrow'            => 'Demo content',
		'intro_title'              => 'Preview every apartment display state',
		'intro_text'               => 'Use this optional demo dataset to understand how MultiRent looks before adding real property content.',
		'show_stats_section'       => '1',
		'stats_lines'              => "4 | Demo apartments\n12 | Amenity options\n1 | Complete test site",
		'show_front_page_rentals'  => '1',
		'front_page_rental_count'  => '4',
		'contact_title'            => 'Demo inquiry call to action',
		'contact_text'             => 'Use the demo contact page to test booking links, phone, email, map, and editor content.',
		'contact_button_text'      => 'Open demo contact',
		'contact_button_url'       => '/demo-contact/',
		'show_contact_cta'         => '1',
		'menu_items'               => "Home | /demo-home/\nApartments | /demo-apartments/\nLocal | /demo-local-guide/\nContact | /demo-contact/",
		'show_apartments_page'     => '1',
		'show_contact_page'        => '1',
		'show_local_page'          => '1',
		'contact_page_title'       => 'Demo contact and booking inquiry',
		'contact_page_intro'       => 'Send demo dates, guest count, and the preferred apartment so the layout can be tested end to end.',
		'contact_address'          => "MultiRent Demo Seaside House\nDemo Street 12\n21329 Demo Coast, Croatia",
		'contact_phone'            => '',
		'contact_mobile'           => '',
		'contact_email'            => 'demo-booking@example.test',
		'contact_form_shortcode'   => '',
		'contact_map_query'        => 'Split Croatia waterfront',
		'contact_map_note'         => 'Demo map note: parking is available behind the house after check-in.',
		'booking_help_lines'       => "Check-in | Demo check-in starts after 15:00.\nPayment | Demo payment can be tested with text-only instructions.\nResponse time | Demo owner replies within one working day.",
		'local_page_title'         => 'Demo local guide',
		'local_page_intro'         => 'Use this page to test guide cards, highlights, activity sections, useful links, and extra editor content.',
		'local_guide_lines'        => "Closest beach | Five-minute demo walk with shallow entry and afternoon shade.\nArrival notes | Use the demo parking bay behind the blue gate, then ring the demo bell.\nBest season | May, June, September, and October are quieter in this demo guide.",
		'local_highlight_lines'    => "Bakery | Two-minute walk for breakfast pastries.\nMarket | Small grocery shop near the demo bus stop.\nRestaurant | Waterfront tavern with family-friendly seating.\nPharmacy | Located beside the demo main square.",
		'local_activity_lines'     => "Morning swim | Test short activity text and two-column cards.\nBoat trip | Demo island excursion with pickup at the pier.\nFamily walk | Easy sunset path suitable for strollers.",
		'local_link_lines'         => "Local tourism board | https://example.test/tourism\nBus timetable | https://example.test/bus\nFerry information | https://example.test/ferry",
		'show_local_guides'        => '1',
		'show_local_highlights'    => '1',
		'show_local_activities'    => '1',
		'show_local_links'         => '1',
		'show_local_content'       => '1',
	);
}

function multirent_companion_demo_rentals() {
	return array(
		array(
			'title'     => 'Demo Sea View Studio',
			'slug'      => 'demo-sea-view-studio',
			'excerpt'   => 'Compact studio for two with balcony, WiFi, TV, and sea-view breakfast seating.',
			'content'   => '',
			'meta'      => array( '_capacity' => '2 guests', '_bedrooms' => 'Studio', '_bathrooms' => '1 bathroom', '_size' => '28 m2', '_price_note' => 'From 75 EUR / night', '_booking_url' => '/demo-contact/?unit=demo-sea-view-studio', '_video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', '_map_coordinates' => '43.5081, 16.4402', '_map_address' => 'Split Croatia waterfront' ),
			'qr_image_key' => 'demo-sea-view-studio-qr',
			'amenities' => array( 'wifi', 'balcony', 'bathroom', 'air-condition', 'tv', 'kitchen', 'no-smoking' ),
		),
		array(
			'title'     => 'Demo Family Apartment',
			'slug'      => 'demo-family-apartment',
			'excerpt'   => 'Two-bedroom family apartment with parking, kitchen, balcony, and no-smoking setup.',
			'content'   => '',
			'meta'      => array( '_capacity' => '2-5 guests', '_bedrooms' => '2 bedrooms', '_bathrooms' => '1 bathroom', '_size' => '62 m2', '_price_note' => 'From 130 EUR / night', '_booking_url' => '/demo-contact/?unit=demo-family-apartment', '_map_address' => 'Bacvice Beach Split Croatia' ),
			'qr_image_key' => 'demo-family-apartment-qr',
			'amenities' => array( 'parking', 'wifi', 'balcony', 'bathroom', 'air-condition', 'tv', 'kitchen', 'no-smoking' ),
		),
		array(
			'title'     => 'Demo Garden Terrace Suite',
			'slug'      => 'demo-garden-terrace-suite',
			'excerpt'   => 'Ground-floor suite with terrace, BBQ area, parking, and outdoor dining space.',
			'content'   => '',
			'meta'      => array( '_capacity' => '2-4 guests', '_bedrooms' => '1 bedroom', '_bathrooms' => '1 bathroom', '_size' => '48 m2', '_price_note' => 'From 110 EUR / night', '_booking_url' => '/demo-contact/?unit=demo-garden-terrace-suite', '_video_url' => 'https://youtu.be/dQw4w9WgXcQ' ),
			'amenities' => array( 'parking', 'wifi', 'bathroom', 'air-condition', 'bbq', 'terrace', 'kitchen', 'pets-not-allowed' ),
		),
		array(
			'title'     => 'Demo Pet Friendly Loft',
			'slug'      => 'demo-pet-friendly-loft',
			'excerpt'   => 'Pet-friendly loft with WiFi, TV, private bathroom, kitchen, and flexible stay notes.',
			'content'   => '',
			'meta'      => array( '_capacity' => '2-3 guests', '_bedrooms' => 'Loft layout', '_bathrooms' => '1 bathroom', '_size' => '40 m2', '_price_note' => 'From 95 EUR / night', '_booking_url' => '/demo-contact/?unit=demo-pet-friendly-loft' ),
			'amenities' => array( 'wifi', 'bathroom', 'air-condition', 'tv', 'kitchen', 'pets-allowed', 'no-smoking' ),
		),
	);
}

function multirent_companion_create_demo_content() {
	multirent_companion_ensure_default_amenities();
	multirent_companion_migrate_legacy_amenities();

	$previous_option_key = multirent_companion_demo_previous_option_key();
	if ( ! get_option( $previous_option_key ) ) {
		update_option(
			$previous_option_key,
			array(
				'show_on_front'      => get_option( 'show_on_front' ),
				'page_on_front'      => get_option( 'page_on_front' ),
				'page_for_posts'     => get_option( 'page_for_posts' ),
				'multirent_settings' => get_option( 'multirent_settings', array() ),
			)
		);
	}

	$pages = array(
		'home'       => multirent_companion_upsert_demo_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'MultiRent Demo Home', 'post_name' => 'demo-home', 'post_content' => '<p>Demo homepage for testing the MultiRent layout, apartment cards, amenity icons, and inquiry flow.</p>' ), array( '_wp_page_template' => 'default' ) ),
		'apartments' => multirent_companion_upsert_demo_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'MultiRent Demo Apartments', 'post_name' => 'demo-apartments', 'post_content' => '<p>Demo apartment listing page. Use this to compare cards, details, selected amenities, and responsive layouts.</p>' ), array( '_wp_page_template' => 'template-apartments-grid.php' ) ),
		'contact'    => multirent_companion_upsert_demo_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'MultiRent Demo Contact', 'post_name' => 'demo-contact', 'post_content' => '<p>This is demo contact-page body content for testing optional editor text below the contact cards.</p>' ), array( '_wp_page_template' => 'template-contact.php' ) ),
		'local'      => multirent_companion_upsert_demo_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'MultiRent Demo Local Guide', 'post_name' => 'demo-local-guide', 'post_content' => '<p>Demo local guide editor content. Add notes here while testing how page content appears with generated guide cards.</p>' ), array( '_wp_page_template' => 'template-local.php' ) ),
	);

	foreach ( $pages as $page_id ) {
		if ( is_wp_error( $page_id ) ) {
			return $page_id;
		}
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $pages['home'] );

	$hero_image_id = multirent_companion_create_demo_image_attachment( 'hero' );
	$contact_qr_image_id = multirent_companion_create_demo_image_attachment( 'demo-contact-qr', $pages['contact'] );
	$settings = array_merge( multirent_companion_settings(), multirent_companion_demo_settings() );
	if ( $hero_image_id ) {
		$settings['hero_image'] = $hero_image_id;
	}
	if ( $contact_qr_image_id ) {
		$settings['contact_qr_code_image_id'] = (string) $contact_qr_image_id;
	}
	update_option( 'multirent_settings', $settings );

	foreach ( multirent_companion_demo_rentals() as $index => $rental ) {
		$post_id = multirent_companion_upsert_demo_post(
			array(
				'post_type'    => 'rental_unit',
				'post_status'  => 'publish',
				'post_title'   => $rental['title'],
				'post_name'    => $rental['slug'],
				'post_excerpt' => $rental['excerpt'],
				'post_content' => $rental['content'],
				'menu_order'   => $index + 1,
			),
			$rental['meta'],
			$rental['amenities']
		);

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		delete_post_meta( $post_id, '_distance' );

		$demo_image_id = multirent_companion_create_demo_image_attachment( $rental['slug'], $post_id );
		if ( $demo_image_id ) {
			set_post_thumbnail( $post_id, $demo_image_id );
		}

		foreach ( multirent_companion_demo_gallery_image_keys( $rental['slug'] ) as $gallery_image_key ) {
			multirent_companion_create_demo_image_attachment( $gallery_image_key, $post_id );
		}

		if ( ! empty( $rental['qr_image_key'] ) ) {
			$qr_image_id = multirent_companion_create_demo_image_attachment( $rental['qr_image_key'], $post_id );
			if ( $qr_image_id ) {
				update_post_meta( $post_id, '_qr_code_image_id', $qr_image_id );
			}
		}
	}

	multirent_companion_apply_top_menu( $settings['menu_items'] );
	flush_rewrite_rules();

	return true;
}

function multirent_companion_remove_demo_content() {
	$demo_posts = get_posts(
		array(
			'post_type'      => array( 'page', 'rental_unit', 'attachment' ),
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => '_multirent_demo_content',
			'meta_value'     => multirent_companion_demo_marker(),
		)
	);

	foreach ( $demo_posts as $post_id ) {
		wp_delete_post( $post_id, true );
	}

	$previous = get_option( multirent_companion_demo_previous_option_key() );
	if ( is_array( $previous ) ) {
		foreach ( array( 'show_on_front', 'page_on_front', 'page_for_posts' ) as $option_key ) {
			if ( array_key_exists( $option_key, $previous ) ) {
				update_option( $option_key, $previous[ $option_key ] );
			}
		}

		if ( array_key_exists( 'multirent_settings', $previous ) ) {
			update_option( 'multirent_settings', $previous['multirent_settings'] );
			$settings = multirent_companion_settings();
			multirent_companion_apply_top_menu( $settings['menu_items'] );
		}
	}

	delete_option( multirent_companion_demo_previous_option_key() );
	flush_rewrite_rules();

	return count( $demo_posts );
}

function multirent_companion_menu_url( $url ) {
	$url = trim( $url );
	if ( '' === $url ) {
		return home_url( '/' );
	}

	if ( str_starts_with( $url, '#' ) || str_starts_with( $url, 'http://' ) || str_starts_with( $url, 'https://' ) || str_starts_with( $url, 'mailto:' ) || str_starts_with( $url, 'tel:' ) ) {
		return $url;
	}

	return home_url( '/' . ltrim( $url, '/' ) );
}

function multirent_companion_parse_menu_items( $menu_items_text ) {
	$items = array();
	$lines = preg_split( '/\r\n|\r|\n/', (string) $menu_items_text );
	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( '' === $line || ! str_contains( $line, '|' ) ) {
			continue;
		}

		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		if ( '' === $parts[0] ) {
			continue;
		}

		$items[] = array(
			'label' => sanitize_text_field( $parts[0] ),
			'url'   => multirent_companion_menu_url( $parts[1] ),
		);
	}

	return $items;
}

function multirent_companion_apply_top_menu( $menu_items_text ) {
	$items = multirent_companion_parse_menu_items( $menu_items_text );
	if ( ! $items ) {
		return;
	}
	$items = multirent_companion_filter_hidden_menu_items( $items, multirent_companion_settings() );
	if ( ! $items ) {
		return;
	}

	$menu_name = 'MultiRent Top Menu';
	$menu      = wp_get_nav_menu_object( $menu_name );
	$menu_id   = $menu ? $menu->term_id : wp_create_nav_menu( $menu_name );
	if ( is_wp_error( $menu_id ) ) {
		return;
	}

	$existing_items = wp_get_nav_menu_items( $menu_id );
	if ( $existing_items ) {
		foreach ( $existing_items as $existing_item ) {
			wp_delete_post( $existing_item->ID, true );
		}
	}

	foreach ( $items as $item ) {
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => $item['label'],
				'menu-item-url'    => esc_url_raw( $item['url'] ),
				'menu-item-type'   => 'custom',
				'menu-item-status' => 'publish',
			)
		);
	}

	$locations            = get_theme_mod( 'nav_menu_locations', array() );
	$locations['primary'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

function multirent_companion_filter_hidden_menu_items( $items, $settings ) {
	$hidden_paths = array();
	foreach ( multirent_companion_page_roles() as $role => $role_config ) {
		if ( '1' === (string) $settings[ $role_config['show_key'] ] ) {
			continue;
		}

		$hidden_paths[] = '/' . sanitize_title( $role_config['title'] ) . '/';
		$page = multirent_companion_role_page( $role, $settings );
		if ( $page ) {
			$path = wp_parse_url( get_permalink( $page ), PHP_URL_PATH );
			if ( $path ) {
				$hidden_paths[] = '/' . trim( (string) $path, '/' ) . '/';
			}
		}
	}
	$hidden_paths = array_values( array_unique( $hidden_paths ) );

	if ( ! $hidden_paths ) {
		return $items;
	}

	return array_values(
		array_filter(
			$items,
			function ( $item ) use ( $hidden_paths ) {
				$path = wp_parse_url( $item['url'], PHP_URL_PATH );
				$path = '/' . trim( (string) $path, '/' ) . '/';
				return ! in_array( $path, $hidden_paths, true );
			}
		)
	);
}

function multirent_companion_render_setup_page() {
	$settings = multirent_companion_settings();
	$schemes  = array(
		'coastal'  => 'Coastal Blue',
		'olive'    => 'Olive Garden',
		'coral'    => 'Coral Sunset',
		'graphite' => 'Graphite',
	);
	settings_errors( 'multirent_messages' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'MultiRent Setup', 'multirent-companion' ); ?></h1>
		<p><?php esc_html_e( 'Configure the rental website from one place. No code editing is required.', 'multirent-companion' ); ?></p>

		<form method="post" action="">
			<?php wp_nonce_field( 'multirent_setup_action', 'multirent_setup_nonce' ); ?>
			<input type="hidden" name="multirent_action" value="save_settings">
			<input type="hidden" name="multirent_settings_scope" value="property_name,page_logo,show_hero_section,hero_title,hero_text,hero_image,hero_button_text,hero_button_url,show_intro_section,intro_eyebrow,intro_title,intro_text,show_stats_section,stats_lines,show_front_page_rentals,front_page_rental_count,reviews_shortcode,show_reviews,show_seo_note,show_migration_note,show_contact_cta,contact_title,contact_text,contact_button_text,contact_button_url,menu_items,color_scheme,use_custom_colors,color_primary,color_dark,color_surface,color_accent">
			<h2><?php esc_html_e( 'Homepage and Brand', 'multirent-companion' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Homepage section visibility', 'multirent-companion' ); ?></th>
					<td>
						<p><label><input name="multirent_settings[show_hero_section]" type="checkbox" value="1" <?php checked( $settings['show_hero_section'], '1' ); ?>> <?php esc_html_e( 'Show Hero section.', 'multirent-companion' ); ?></label></p>
						<p><label><input name="multirent_settings[show_intro_section]" type="checkbox" value="1" <?php checked( $settings['show_intro_section'], '1' ); ?>> <?php esc_html_e( 'Show Intro section.', 'multirent-companion' ); ?></label></p>
						<p><label><input name="multirent_settings[show_stats_section]" type="checkbox" value="1" <?php checked( $settings['show_stats_section'], '1' ); ?>> <?php esc_html_e( 'Show Stats strip.', 'multirent-companion' ); ?></label></p>
						<p><label><input name="multirent_settings[show_front_page_rentals]" type="checkbox" value="1" <?php checked( $settings['show_front_page_rentals'], '1' ); ?>> <?php esc_html_e( 'Show Apartment cards section.', 'multirent-companion' ); ?></label></p>
						<p><label><input name="multirent_settings[show_reviews]" type="checkbox" value="1" <?php checked( $settings['show_reviews'], '1' ); ?>> <?php esc_html_e( 'Show Reviews section when a reviews shortcode is entered.', 'multirent-companion' ); ?></label></p>
						<p><label><input name="multirent_settings[show_seo_note]" type="checkbox" value="1" <?php checked( $settings['show_seo_note'], '1' ); ?>> <?php esc_html_e( 'Show admin-only SEO reminder.', 'multirent-companion' ); ?></label></p>
						<p><label><input name="multirent_settings[show_migration_note]" type="checkbox" value="1" <?php checked( $settings['show_migration_note'], '1' ); ?>> <?php esc_html_e( 'Show admin-only backup reminder.', 'multirent-companion' ); ?></label></p>
						<p><label><input name="multirent_settings[show_contact_cta]" type="checkbox" value="1" <?php checked( $settings['show_contact_cta'], '1' ); ?>> <?php esc_html_e( 'Show Contact call-to-action section.', 'multirent-companion' ); ?></label></p>
						<p class="description"><?php esc_html_e( 'These checkboxes hide or show full homepage blocks. The text and image fields below are kept even when their block is hidden.', 'multirent-companion' ); ?></p>
					</td>
				</tr>
			</table>
			<table class="form-table" role="presentation">
				<?php foreach ( array( 'property_name', 'hero_title', 'hero_text', 'hero_button_text', 'hero_button_url', 'intro_eyebrow', 'intro_title', 'intro_text', 'stats_lines', 'reviews_shortcode', 'contact_title', 'contact_text', 'contact_button_text', 'contact_button_url' ) as $key ) : ?>
					<tr>
						<th scope="row"><label for="multirent-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( ucwords( str_replace( '_', ' ', $key ) ) ); ?></label></th>
						<td>
							<?php if ( in_array( $key, array( 'hero_text', 'intro_text', 'stats_lines', 'contact_text' ), true ) ) : ?>
								<textarea class="large-text" rows="4" id="multirent-<?php echo esc_attr( $key ); ?>" name="multirent_settings[<?php echo esc_attr( $key ); ?>]"><?php echo esc_textarea( $settings[ $key ] ); ?></textarea>
							<?php else : ?>
								<input class="regular-text" id="multirent-<?php echo esc_attr( $key ); ?>" name="multirent_settings[<?php echo esc_attr( $key ); ?>]" type="text" value="<?php echo esc_attr( $settings[ $key ] ); ?>">
							<?php endif; ?>
							<?php multirent_companion_description( $key ); ?>
						</td>
					</tr>
				<?php endforeach; ?>
				<tr>
					<th scope="row"><?php esc_html_e( 'Page logo', 'multirent-companion' ); ?></th>
					<td>
						<?php multirent_companion_media_field( 'multirent_settings[page_logo]', absint( $settings['page_logo'] ), __( 'Choose page logo', 'multirent-companion' ), __( 'Remove page logo', 'multirent-companion' ) ); ?>
						<?php multirent_companion_description( 'page_logo' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Hero image', 'multirent-companion' ); ?></th>
					<td>
						<?php multirent_companion_media_field( 'multirent_settings[hero_image]', absint( $settings['hero_image'] ), __( 'Choose hero image', 'multirent-companion' ), __( 'Remove hero image', 'multirent-companion' ) ); ?>
						<p class="description"><?php esc_html_e( 'This controls the large homepage image. Users can change it without opening theme files.', 'multirent-companion' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Homepage apartments', 'multirent-companion' ); ?></th>
					<td>
						<label for="multirent-front-page-rental-count"><strong><?php esc_html_e( 'Number of apartments to show', 'multirent-companion' ); ?></strong></label><br>
						<input id="multirent-front-page-rental-count" name="multirent_settings[front_page_rental_count]" type="number" min="1" max="50" step="1" value="<?php echo esc_attr( $settings['front_page_rental_count'] ); ?>">
						<?php multirent_companion_description( 'front_page_rental_count' ); ?>
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Plugin Placeholders', 'multirent-companion' ); ?></h2>
			<p><?php esc_html_e( 'Choose which optional plugin areas should appear. Install and configure recommended plugins separately from Plugins > Add New.', 'multirent-companion' ); ?></p>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Google Reviews', 'multirent-companion' ); ?></th>
					<td><p class="description"><?php esc_html_e( 'Paste the reviews shortcode above, then enable Reviews in Homepage section visibility.', 'multirent-companion' ); ?></p></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'SEO reminder', 'multirent-companion' ); ?></th>
					<td><p class="description"><?php esc_html_e( 'Enable SEO reminder in Homepage section visibility to show this private admin-only homepage reminder.', 'multirent-companion' ); ?></p></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Backup reminder', 'multirent-companion' ); ?></th>
					<td><p class="description"><?php esc_html_e( 'Enable Backup reminder in Homepage section visibility to show this private admin-only homepage reminder.', 'multirent-companion' ); ?></p></td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Page Administration', 'multirent-companion' ); ?></h2>
			<p><?php esc_html_e( 'Landing page settings stay here because the landing page is mandatory. Optional pages have their own admin screens in the MultiRent Setup menu: Apartments Page, Contact Page, and Local Page.', 'multirent-companion' ); ?></p>

			<h2><?php esc_html_e( 'Top Menu Builder', 'multirent-companion' ); ?></h2>
			<p><?php esc_html_e( 'Add one menu link per line using this simple format: Label | URL. Use / for home, /rentals/ for the rental archive, #contact for homepage sections, or a full external URL.', 'multirent-companion' ); ?></p>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="multirent-menu-items"><?php esc_html_e( 'Header menu links', 'multirent-companion' ); ?></label></th>
					<td>
						<textarea class="large-text code" rows="6" id="multirent-menu-items" name="multirent_settings[menu_items]"><?php echo esc_textarea( $settings['menu_items'] ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Example: Home | /', 'multirent-companion' ); ?></p>
						<?php multirent_companion_description( 'menu_items' ); ?>
					</td>
				</tr>
			</table>
			<?php submit_button( esc_html__( 'Save Settings Only', 'multirent-companion' ), 'secondary', 'submit', false ); ?>
			<button class="button button-primary" type="submit" name="multirent_action" value="apply_top_menu"><?php esc_html_e( 'Save and Apply Top Menu', 'multirent-companion' ); ?></button>

			<h2><?php esc_html_e( 'Color Scheme', 'multirent-companion' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="multirent-color-scheme"><?php esc_html_e( 'Preset', 'multirent-companion' ); ?></label></th>
					<td>
						<select id="multirent-color-scheme" name="multirent_settings[color_scheme]">
							<?php foreach ( $schemes as $key => $label ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $settings['color_scheme'], $key ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php multirent_companion_description( 'color_scheme' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Custom palette', 'multirent-companion' ); ?></th>
					<td><label><input name="multirent_settings[use_custom_colors]" type="checkbox" value="1" <?php checked( $settings['use_custom_colors'], '1' ); ?>> <?php esc_html_e( 'Use the custom colors below instead of only the preset.', 'multirent-companion' ); ?></label><?php multirent_companion_description( 'use_custom_colors' ); ?></td>
				</tr>
				<?php foreach ( array( 'color_primary', 'color_dark', 'color_surface', 'color_accent' ) as $key ) : ?>
					<tr>
						<th scope="row"><label for="multirent-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( ucwords( str_replace( '_', ' ', $key ) ) ); ?></label></th>
						<td><input id="multirent-<?php echo esc_attr( $key ); ?>" name="multirent_settings[<?php echo esc_attr( $key ); ?>]" type="color" value="<?php echo esc_attr( $settings[ $key ] ? $settings[ $key ] : '#087ea4' ); ?>"><?php multirent_companion_description( $key ); ?></td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button( esc_html__( 'Save Website Settings', 'multirent-companion' ) ); ?>
		</form>

		<hr>
		<h2><?php esc_html_e( 'Starter Content', 'multirent-companion' ); ?></h2>
		<form method="post" action="" style="margin-bottom:24px;">
			<?php wp_nonce_field( 'multirent_setup_action', 'multirent_setup_nonce' ); ?>
			<input type="hidden" name="multirent_action" value="create_starter_site">
			<?php submit_button( esc_html__( 'Create Starter Pages, Menu, and Amenities', 'multirent-companion' ), 'secondary' ); ?>
		</form>
		<form method="post" action="">
			<?php wp_nonce_field( 'multirent_setup_action', 'multirent_setup_nonce' ); ?>
			<input type="hidden" name="multirent_action" value="create_units">
			<label for="unit-count"><strong><?php esc_html_e( 'How many rental units do you have?', 'multirent-companion' ); ?></strong></label>
			<input id="unit-count" name="unit_count" type="number" min="1" max="50" value="4">
			<?php submit_button( esc_html__( 'Create Rental Units', 'multirent-companion' ), 'secondary', 'submit', false ); ?>
		</form>

		<hr>
		<h2><?php esc_html_e( 'Demo Content', 'multirent-companion' ); ?></h2>
		<p><?php esc_html_e( 'Create a complete example site with four demo apartments, selected amenity checkboxes, demo Contact and Local pages, menu links, and sample settings. Demo content is marked so it can be removed later without deleting your real content.', 'multirent-companion' ); ?></p>
		<form method="post" action="" style="display:inline-block;margin-right:12px;margin-bottom:12px;">
			<?php wp_nonce_field( 'multirent_setup_action', 'multirent_setup_nonce' ); ?>
			<input type="hidden" name="multirent_action" value="create_demo_content">
			<?php submit_button( esc_html__( 'Create Demo Content', 'multirent-companion' ), 'secondary', 'submit', false ); ?>
		</form>
		<form method="post" action="" style="display:inline-block;margin-bottom:12px;">
			<?php wp_nonce_field( 'multirent_setup_action', 'multirent_setup_nonce' ); ?>
			<input type="hidden" name="multirent_action" value="remove_demo_content">
			<?php submit_button( esc_html__( 'Remove Demo Content', 'multirent-companion' ), 'delete', 'submit', false ); ?>
		</form>
		<p class="description"><?php esc_html_e( 'The demo uses pages and apartments whose slugs begin with demo-. It also stores the previous homepage and MultiRent settings so Remove Demo Content can restore them.', 'multirent-companion' ); ?></p>

		<hr>
		<h2><?php esc_html_e( 'Recommended Plugins', 'multirent-companion' ); ?></h2>
		<p><?php esc_html_e( 'These optional plugins match the tested localhost rental-site setup. MultiRent lists them for convenience but does not install them automatically, avoiding filesystem permission problems and keeping the template clean. ManageWP is not included because it is account-specific.', 'multirent-companion' ); ?></p>
		<table class="widefat striped" style="max-width:960px;">
			<thead><tr><th><?php esc_html_e( 'Plugin', 'multirent-companion' ); ?></th><th><?php esc_html_e( 'Purpose', 'multirent-companion' ); ?></th><th><?php esc_html_e( 'Status', 'multirent-companion' ); ?></th></tr></thead>
			<tbody>
				<?php foreach ( multirent_companion_recommended_plugins() as $plugin ) : ?>
					<?php $state = multirent_companion_plugin_state( $plugin['plugin_file'] ); ?>
					<tr>
						<td><strong><?php echo esc_html( $plugin['name'] ); ?></strong></td>
						<td><?php echo esc_html( $plugin['purpose'] ); ?></td>
						<td><?php echo esc_html( ucfirst( $state ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}

function multirent_companion_render_apartments_page() {
	$settings          = multirent_companion_settings();
	$apartments_page   = multirent_companion_role_page( 'apartments', $settings );
	$current_template  = multirent_companion_apartments_page_template();
	$templates         = multirent_companion_apartments_page_templates();
	settings_errors( 'multirent_messages' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Apartments Page', 'multirent-companion' ); ?></h1>
		<p><?php esc_html_e( 'Manage whether the Apartments page is visible and choose the apartment listing template from this screen.', 'multirent-companion' ); ?></p>
		<form method="post" action="">
			<?php wp_nonce_field( 'multirent_setup_action', 'multirent_setup_nonce' ); ?>
			<input type="hidden" name="multirent_action" value="save_apartments_page">
			<input type="hidden" name="multirent_settings_scope" value="show_apartments_page,apartments_page_id">
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="multirent-apartments-page-id"><?php esc_html_e( 'Assigned Apartments page', 'multirent-companion' ); ?></label></th>
					<td>
						<?php multirent_companion_page_select_field( 'apartments', 'multirent_settings[apartments_page_id]', $settings['apartments_page_id'] ); ?>
						<p class="description"><?php esc_html_e( 'Choose which WordPress page should behave as the Apartments page. You can create alternate apartment pages and switch between them here.', 'multirent-companion' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Show Apartments page', 'multirent-companion' ); ?></th>
					<td><label><input name="multirent_settings[show_apartments_page]" type="checkbox" value="1" <?php checked( $settings['show_apartments_page'], '1' ); ?>> <?php esc_html_e( 'Publish the Apartments page and allow it in the generated top menu.', 'multirent-companion' ); ?></label><?php multirent_companion_description( 'show_apartments_page' ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="multirent-apartments-template"><?php esc_html_e( 'Apartments template', 'multirent-companion' ); ?></label></th>
					<td>
						<select id="multirent-apartments-template" name="multirent_apartments_template">
							<?php foreach ( $templates as $template_file => $template_data ) : ?>
								<option value="<?php echo esc_attr( $template_file ); ?>" <?php selected( $current_template, $template_file ); ?>><?php echo esc_html( $template_data['label'] ); ?></option>
							<?php endforeach; ?>
						</select>
						<p class="description"><?php esc_html_e( 'Choose the template here instead of opening Pages > All Pages > Apartments.', 'multirent-companion' ); ?></p>
						<?php if ( $apartments_page ) : ?>
							<p><a class="button button-secondary" href="<?php echo esc_url( get_permalink( $apartments_page ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Open Apartments Page Preview', 'multirent-companion' ); ?></a></p>
						<?php else : ?>
							<p class="description"><?php esc_html_e( 'Create the starter pages first to enable the live Apartments page preview link.', 'multirent-companion' ); ?></p>
						<?php endif; ?>
					</td>
				</tr>
			</table>
			<?php submit_button( esc_html__( 'Save Apartments Page Settings', 'multirent-companion' ) ); ?>
		</form>
	</div>
	<?php
}

function multirent_companion_render_contact_page() {
	$settings              = multirent_companion_settings();
	$contact_page          = multirent_companion_role_page( 'contact', $settings );
	$current_template      = multirent_companion_role_page_template( 'contact' );
	$templates             = multirent_companion_contact_page_templates();
	settings_errors( 'multirent_messages' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Contact Page', 'multirent-companion' ); ?></h1>
		<p><?php esc_html_e( 'Manage the optional Contact / Booking Inquiry page and decide which sections should appear.', 'multirent-companion' ); ?></p>
		<form method="post" action="">
			<?php wp_nonce_field( 'multirent_setup_action', 'multirent_setup_nonce' ); ?>
			<input type="hidden" name="multirent_action" value="save_contact_page">
			<input type="hidden" name="multirent_settings_scope" value="show_contact_page,contact_page_id,contact_page_title,contact_page_intro,contact_address,contact_phone,contact_mobile,contact_email,contact_form_shortcode,contact_map_query,contact_map_note,contact_qr_code_image_id,booking_help_lines,show_contact_details,show_booking_help,show_contact_map,show_contact_content,show_contact_form,show_contact_map_note">
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="multirent-contact-page-id"><?php esc_html_e( 'Assigned Contact page', 'multirent-companion' ); ?></label></th>
					<td>
						<?php multirent_companion_page_select_field( 'contact', 'multirent_settings[contact_page_id]', $settings['contact_page_id'] ); ?>
						<p class="description"><?php esc_html_e( 'Choose which WordPress page should use the Contact / Booking Inquiry template. You can keep multiple contact-page versions and switch between them here.', 'multirent-companion' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="multirent-contact-template"><?php esc_html_e( 'Contact template', 'multirent-companion' ); ?></label></th>
					<td>
						<select id="multirent-contact-template" name="multirent_contact_template">
							<?php foreach ( $templates as $template_file => $template_data ) : ?>
								<option value="<?php echo esc_attr( $template_file ); ?>" <?php selected( $current_template, $template_file ); ?>><?php echo esc_html( $template_data['label'] ); ?></option>
							<?php endforeach; ?>
						</select>
						<p class="description"><?php esc_html_e( 'Choose the Contact page layout.', 'multirent-companion' ); ?></p>
						<?php if ( $contact_page ) : ?>
							<p><a class="button button-secondary" href="<?php echo esc_url( get_permalink( $contact_page ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Open Contact Page Preview', 'multirent-companion' ); ?></a></p>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Contact page visibility', 'multirent-companion' ); ?></th>
					<td>
						<p><label><input name="multirent_settings[show_contact_page]" type="checkbox" value="1" <?php checked( $settings['show_contact_page'], '1' ); ?>> <?php esc_html_e( 'Publish Contact page and include it in the generated top menu.', 'multirent-companion' ); ?></label></p>
						<p><label><input name="multirent_settings[show_contact_details]" type="checkbox" value="1" <?php checked( $settings['show_contact_details'], '1' ); ?>> <?php esc_html_e( 'Show contact details card.', 'multirent-companion' ); ?></label></p>
						<p><label><input name="multirent_settings[show_booking_help]" type="checkbox" value="1" <?php checked( $settings['show_booking_help'], '1' ); ?>> <?php esc_html_e( 'Show booking inquiry checklist.', 'multirent-companion' ); ?></label></p>
						<p><label><input name="multirent_settings[show_contact_map]" type="checkbox" value="1" <?php checked( $settings['show_contact_map'], '1' ); ?>> <?php esc_html_e( 'Show map iframe.', 'multirent-companion' ); ?></label></p>
						<p><label><input name="multirent_settings[show_contact_content]" type="checkbox" value="1" <?php checked( $settings['show_contact_content'], '1' ); ?>> <?php esc_html_e( 'Show page editor content.', 'multirent-companion' ); ?></label></p>
						<p><label><input name="multirent_settings[show_contact_form]" type="checkbox" value="1" <?php checked( $settings['show_contact_form'], '1' ); ?>> <?php esc_html_e( 'Show form shortcode area.', 'multirent-companion' ); ?></label></p>
						<p><label><input name="multirent_settings[show_contact_map_note]" type="checkbox" value="1" <?php checked( $settings['show_contact_map_note'], '1' ); ?>> <?php esc_html_e( 'Show map or arrival note.', 'multirent-companion' ); ?></label></p>
						<p class="description"><?php esc_html_e( 'Show or hide full Contact page areas while keeping saved field values available for later.', 'multirent-companion' ); ?></p>
					</td>
				</tr>
				<?php foreach ( array( 'contact_page_title', 'contact_page_intro', 'contact_address', 'contact_phone', 'contact_mobile', 'contact_email', 'contact_form_shortcode', 'contact_map_query', 'contact_map_note', 'booking_help_lines' ) as $key ) : ?>
					<tr>
						<th scope="row"><label for="multirent-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( ucwords( str_replace( '_', ' ', $key ) ) ); ?></label></th>
						<td>
							<?php if ( in_array( $key, array( 'contact_page_intro', 'contact_address', 'contact_map_note', 'booking_help_lines' ), true ) ) : ?>
								<textarea class="large-text" rows="4" id="multirent-<?php echo esc_attr( $key ); ?>" name="multirent_settings[<?php echo esc_attr( $key ); ?>]"><?php echo esc_textarea( $settings[ $key ] ); ?></textarea>
							<?php else : ?>
								<input class="regular-text" id="multirent-<?php echo esc_attr( $key ); ?>" name="multirent_settings[<?php echo esc_attr( $key ); ?>]" type="text" value="<?php echo esc_attr( $settings[ $key ] ); ?>">
							<?php endif; ?>
							<?php multirent_companion_description( $key ); ?>
						</td>
					</tr>
				<?php endforeach; ?>
				<tr>
					<th scope="row"><?php esc_html_e( 'Contact QR code image', 'multirent-companion' ); ?></th>
					<td>
						<?php multirent_companion_media_field( 'multirent_settings[contact_qr_code_image_id]', absint( $settings['contact_qr_code_image_id'] ), __( 'Choose Contact QR code image', 'multirent-companion' ), __( 'Remove Contact QR code image', 'multirent-companion' ) ); ?>
						<?php multirent_companion_description( 'contact_qr_code_image_id' ); ?>
					</td>
				</tr>
			</table>
			<?php submit_button( esc_html__( 'Save Contact Page Settings', 'multirent-companion' ) ); ?>
		</form>
	</div>
	<?php
}

function multirent_companion_render_local_page() {
	$settings              = multirent_companion_settings();
	$local_page            = multirent_companion_role_page( 'local', $settings );
	$current_template      = multirent_companion_role_page_template( 'local' );
	$templates             = multirent_companion_local_page_templates();
	settings_errors( 'multirent_messages' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Local Page', 'multirent-companion' ); ?></h1>
		<p><?php esc_html_e( 'Manage the optional Local Information page and decide which travel-guide sections should appear.', 'multirent-companion' ); ?></p>
		<form method="post" action="">
			<?php wp_nonce_field( 'multirent_setup_action', 'multirent_setup_nonce' ); ?>
			<input type="hidden" name="multirent_action" value="save_local_page">
			<input type="hidden" name="multirent_settings_scope" value="show_local_page,local_page_id,local_page_title,local_page_intro,local_guide_lines,local_highlight_lines,local_activity_lines,local_link_lines,show_local_guides,show_local_highlights,show_local_activities,show_local_links,show_local_content">
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="multirent-local-page-id"><?php esc_html_e( 'Assigned Local page', 'multirent-companion' ); ?></label></th>
					<td>
						<?php multirent_companion_page_select_field( 'local', 'multirent_settings[local_page_id]', $settings['local_page_id'] ); ?>
						<p class="description"><?php esc_html_e( 'Choose which WordPress page should use the Local Information template. You can keep multiple local-guide versions and switch between them here.', 'multirent-companion' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="multirent-local-template"><?php esc_html_e( 'Local template', 'multirent-companion' ); ?></label></th>
					<td>
						<select id="multirent-local-template" name="multirent_local_template">
							<?php foreach ( $templates as $template_file => $template_data ) : ?>
								<option value="<?php echo esc_attr( $template_file ); ?>" <?php selected( $current_template, $template_file ); ?>><?php echo esc_html( $template_data['label'] ); ?></option>
							<?php endforeach; ?>
						</select>
						<p class="description"><?php esc_html_e( 'Choose the Local page layout.', 'multirent-companion' ); ?></p>
						<?php if ( $local_page ) : ?>
							<p><a class="button button-secondary" href="<?php echo esc_url( get_permalink( $local_page ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Open Local Page Preview', 'multirent-companion' ); ?></a></p>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Local page visibility', 'multirent-companion' ); ?></th>
					<td>
						<p><label><input name="multirent_settings[show_local_page]" type="checkbox" value="1" <?php checked( $settings['show_local_page'], '1' ); ?>> <?php esc_html_e( 'Publish Local page and include it in the generated top menu.', 'multirent-companion' ); ?></label></p>
						<p><label><input name="multirent_settings[show_local_guides]" type="checkbox" value="1" <?php checked( $settings['show_local_guides'], '1' ); ?>> <?php esc_html_e( 'Show guide cards.', 'multirent-companion' ); ?></label></p>
						<p><label><input name="multirent_settings[show_local_highlights]" type="checkbox" value="1" <?php checked( $settings['show_local_highlights'], '1' ); ?>> <?php esc_html_e( 'Show local highlights.', 'multirent-companion' ); ?></label></p>
						<p><label><input name="multirent_settings[show_local_activities]" type="checkbox" value="1" <?php checked( $settings['show_local_activities'], '1' ); ?>> <?php esc_html_e( 'Show trips and activities.', 'multirent-companion' ); ?></label></p>
						<p><label><input name="multirent_settings[show_local_links]" type="checkbox" value="1" <?php checked( $settings['show_local_links'], '1' ); ?>> <?php esc_html_e( 'Show useful links sidebar.', 'multirent-companion' ); ?></label></p>
						<p><label><input name="multirent_settings[show_local_content]" type="checkbox" value="1" <?php checked( $settings['show_local_content'], '1' ); ?>> <?php esc_html_e( 'Show page editor content.', 'multirent-companion' ); ?></label></p>
						<p class="description"><?php esc_html_e( 'Show or hide full Local page areas while keeping saved field values available for later.', 'multirent-companion' ); ?></p>
					</td>
				</tr>
				<?php foreach ( array( 'local_page_title', 'local_page_intro', 'local_guide_lines', 'local_highlight_lines', 'local_activity_lines', 'local_link_lines' ) as $key ) : ?>
					<tr>
						<th scope="row"><label for="multirent-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( ucwords( str_replace( '_', ' ', $key ) ) ); ?></label></th>
						<td>
							<?php if ( in_array( $key, array( 'local_page_intro', 'local_guide_lines', 'local_highlight_lines', 'local_activity_lines', 'local_link_lines' ), true ) ) : ?>
								<textarea class="large-text" rows="5" id="multirent-<?php echo esc_attr( $key ); ?>" name="multirent_settings[<?php echo esc_attr( $key ); ?>]"><?php echo esc_textarea( $settings[ $key ] ); ?></textarea>
							<?php else : ?>
								<input class="regular-text" id="multirent-<?php echo esc_attr( $key ); ?>" name="multirent_settings[<?php echo esc_attr( $key ); ?>]" type="text" value="<?php echo esc_attr( $settings[ $key ] ); ?>">
							<?php endif; ?>
							<?php multirent_companion_description( $key ); ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button( esc_html__( 'Save Local Page Settings', 'multirent-companion' ) ); ?>
		</form>
	</div>
	<?php
}

function multirent_companion_render_readme_page() {
	?>
	<div class="wrap multirent-readme-wrap">
		<h1><?php esc_html_e( 'MultiRent Help / README', 'multirent-companion' ); ?></h1>
		<p><?php esc_html_e( 'This page shows the README.md file included with the plugin so site owners can always find setup instructions from the WordPress dashboard.', 'multirent-companion' ); ?></p>
		<div class="multirent-readme-panel">
			<?php echo wp_kses_post( multirent_companion_readme_html() ); ?>
		</div>
	</div>
	<?php
}
