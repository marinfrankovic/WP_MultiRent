<?php
/**
 * Plugin Name: MultiRent Companion
 * Description: End-to-end setup tools, rental unit management, amenities, and GUI settings for the Multi Apartment Rental theme.
 * Version: 0.2.3
 * Requires at least: 6.5
 * Tested up to: 7.0
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

define( 'MULTIRENT_COMPANION_VERSION', '0.2.3' );

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
		'separate-entrance' => 'Separate entrance',
		'dishwasher'       => 'Dishwasher',
		'coffee-machine'   => 'Coffee machine',
		'microwave'        => 'Microwave',
		'washing-machine'  => 'Washing machine',
		'iron'             => 'Iron',
		'hair-dryer'       => 'Hair dryer',
		'baby-cot'         => 'Baby cot',
		'ev-charger'       => 'EV charger',
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
			'supports'     => array( 'title', 'editor', 'excerpt', 'page-attributes', 'custom-fields' ),
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
				'auth_callback'     => function( $allowed, $meta_key, $post_id ) {
					return current_user_can( 'edit_post', $post_id );
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
			'auth_callback'     => function( $allowed, $meta_key, $post_id ) {
				return current_user_can( 'edit_post', $post_id );
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
			'auth_callback'     => function( $allowed, $meta_key, $post_id ) {
				return current_user_can( 'edit_post', $post_id );
			},
		)
	);

	register_post_meta(
		'rental_unit',
		'_multirent_apartment_page_ids',
		array(
			'single'            => true,
			'type'              => 'string',
			'show_in_rest'      => true,
			'sanitize_callback' => 'multirent_companion_sanitize_apartment_page_ids',
			'auth_callback'     => function( $allowed, $meta_key, $post_id ) {
				return current_user_can( 'edit_post', $post_id );
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

function multirent_companion_sanitize_apartment_page_ids( $value ) {
	$ids = is_array( $value ) ? $value : explode( ',', trim( (string) $value, ',' ) );
	$ids = array_filter( array_map( 'absint', $ids ) );
	$ids = array_values( array_unique( $ids ) );

	return $ids ? ',' . implode( ',', $ids ) . ',' : '';
}

function multirent_companion_apartment_page_ids_for_editor( $post_id ) {
	$stored = multirent_companion_sanitize_apartment_page_ids( get_post_meta( $post_id, '_multirent_apartment_page_ids', true ) );
	if ( ! $stored ) {
		$slot = multirent_companion_page_slot( 'apartment', 1 );
		return ! empty( $slot['page_id'] ) ? array( absint( $slot['page_id'] ) ) : array();
	}

	return array_values( array_filter( array_map( 'absint', explode( ',', trim( $stored, ',' ) ) ) ) );
}

function multirent_companion_apartment_page_options_for_editor() {
	$options = array();
	foreach ( multirent_companion_page_slots( 'apartment' ) as $slot ) {
		if ( empty( $slot['enabled'] ) || empty( $slot['page_id'] ) ) {
			continue;
		}

		$options[] = array(
			'id'    => absint( $slot['page_id'] ),
			'label' => $slot['button_label'] ? $slot['button_label'] : sprintf( __( 'Apartment Page %d', 'multirent-companion' ), $slot['index'] ),
			'title' => get_the_title( $slot['page_id'] ),
			'index' => absint( $slot['index'] ),
		);
	}

	return $options;
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
		'property_tagline'    => esc_html__( 'Optional short tagline shown under the property name in the site header. This overrides the default WordPress tagline for the theme header.', 'multirent-companion' ),
		'page_logo'           => esc_html__( 'Optional logo image shown to the left of the property name in the site header. Leave empty to show the property name without any logo.', 'multirent-companion' ),
		'global_font'         => esc_html__( 'Font used across the public site for body text, headings, menus, cards, buttons, and hero text. Fonts are bundled with the theme and do not load from external services.', 'multirent-companion' ),
		'hero_title'          => esc_html__( 'Main landing-page headline. Use a short phrase that explains the stay you offer.', 'multirent-companion' ),
		'hero_text'           => esc_html__( 'Short landing-page introduction below the headline.', 'multirent-companion' ),
		'hero_title_size'     => esc_html__( 'Optional hero headline size in centimeters. Leave empty to use the responsive theme default.', 'multirent-companion' ),
		'hero_button_text'    => esc_html__( 'Text shown on the main landing-page call-to-action button.', 'multirent-companion' ),
		'hero_button_url'     => esc_html__( 'Where the hero button opens. Use a page path such as /apartments/ or a full URL.', 'multirent-companion' ),
		'intro_eyebrow'       => esc_html__( 'Small label shown above the landing-page intro heading.', 'multirent-companion' ),
		'intro_title'         => esc_html__( 'Heading for the landing-page intro section below the hero.', 'multirent-companion' ),
		'intro_text'          => esc_html__( 'Text for the landing-page intro section. Explain what guests can manage or discover.', 'multirent-companion' ),
		'stats_lines'         => esc_html__( 'Landing-page facts, one per line, using value | label. Example: 4 | Apartments.', 'multirent-companion' ),
		'show_front_page_rentals' => esc_html__( 'When enabled, rental-unit cards appear on the homepage below the intro and stats.', 'multirent-companion' ),
		'front_page_rental_count' => esc_html__( 'Maximum number of rental units shown on the homepage. Use the Apartments page for the full list.', 'multirent-companion' ),
		'reviews_shortcode'   => esc_html__( 'Shortcode from a reviews plugin. The Reviews section checkbox must also be enabled in Homepage section visibility.', 'multirent-companion' ),
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

function multirent_companion_global_font_choices() {
	return array(
		'theme-default' => esc_html__( 'Theme default', 'multirent-companion' ),
		'playfair'      => esc_html__( 'Playfair Display', 'multirent-companion' ),
		'inter'         => esc_html__( 'Inter', 'multirent-companion' ),
		'poppins'       => esc_html__( 'Poppins', 'multirent-companion' ),
		'montserrat'    => esc_html__( 'Montserrat', 'multirent-companion' ),
		'lora'          => esc_html__( 'Lora', 'multirent-companion' ),
		'merriweather'  => esc_html__( 'Merriweather', 'multirent-companion' ),
		'raleway'       => esc_html__( 'Raleway', 'multirent-companion' ),
		'oswald'        => esc_html__( 'Oswald', 'multirent-companion' ),
		'nunito-sans'   => esc_html__( 'Nunito Sans', 'multirent-companion' ),
		'source-sans-3' => esc_html__( 'Source Sans 3', 'multirent-companion' ),
	);
}

function multirent_companion_sanitize_global_font( $value ) {
	$value   = sanitize_key( $value );
	$choices = multirent_companion_global_font_choices();
	return isset( $choices[ $value ] ) ? $value : 'theme-default';
}

function multirent_companion_sanitize_hero_title_size( $value ) {
	$value = is_string( $value ) ? trim( $value ) : $value;
	if ( '' === $value ) {
		return '';
	}

	$size = (float) $value;
	if ( $size <= 0 ) {
		return '';
	}

	return (string) min( 5, max( 0.8, round( $size, 2 ) ) );
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
	add_meta_box( 'multirent_unit_apartment_pages', esc_html__( 'Apartment Page Assignment', 'multirent-companion' ), 'multirent_companion_render_unit_apartment_pages_meta_box', 'rental_unit', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'multirent_companion_add_unit_meta_box' );

function multirent_companion_render_unit_apartment_pages_fields( $post_id ) {
	$selected_apartment_page_ids = multirent_companion_apartment_page_ids_for_editor( $post_id );
	$apartment_page_options      = multirent_companion_apartment_page_options_for_editor();
	?>
	<input type="hidden" name="multirent_apartment_page_ids_present" value="1">
	<div class="multirent-unit-assignment-control">
		<p><?php esc_html_e( 'Choose which apartment overview pages should show this rental unit. If nothing is selected, the unit belongs to Apartment Page 1.', 'multirent-companion' ); ?></p>
		<?php if ( $apartment_page_options ) : ?>
			<?php foreach ( $apartment_page_options as $option ) : ?>
				<p><label><input type="checkbox" name="multirent_apartment_page_ids[]" value="<?php echo esc_attr( $option['id'] ); ?>" <?php checked( in_array( absint( $option['id'] ), $selected_apartment_page_ids, true ) ); ?>> <?php echo esc_html( $option['label'] ); ?> <span class="description">#<?php echo esc_html( $option['id'] ); ?></span></label></p>
			<?php endforeach; ?>
		<?php else : ?>
			<p class="description"><?php esc_html_e( 'No enabled apartment pages are configured yet. Add them in MultiRent Setup > Pages & Buttons.', 'multirent-companion' ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}

function multirent_companion_render_unit_apartment_pages_meta_box( $post ) {
	wp_nonce_field( 'multirent_save_unit_details', 'multirent_unit_nonce' );
	multirent_companion_render_unit_apartment_pages_fields( $post->ID );
}

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

	if ( isset( $_POST['multirent_apartment_page_ids_present'] ) ) {
		$assigned_page_ids = isset( $_POST['multirent_apartment_page_ids'] ) && is_array( $_POST['multirent_apartment_page_ids'] ) ? wp_unslash( $_POST['multirent_apartment_page_ids'] ) : array();
		update_post_meta( $post_id, '_multirent_apartment_page_ids', multirent_companion_sanitize_apartment_page_ids( $assigned_page_ids ) );
	}

}
add_action( 'save_post_rental_unit', 'multirent_companion_save_unit_details' );

function multirent_companion_default_settings() {
	return array(
		'property_name'       => 'Your Rental Property',
		'property_tagline'    => '',
		'page_logo'           => '',
		'global_font'         => 'theme-default',
		'hero_title'          => 'Flexible stays for every guest',
		'hero_text'           => 'Showcase apartments, rooms, villas, or holiday homes with clear details and easy inquiry paths.',
		'hero_title_size'     => '',
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
		'apartment_page_1_enabled' => '1',
		'apartment_page_1_id' => '',
		'apartment_page_1_template' => 'template-apartments-grid.php',
		'apartment_page_1_button_label' => 'Apartments',
		'apartment_page_1_show_hero' => '1',
		'apartment_page_1_show_menu' => '1',
		'apartment_page_2_enabled' => '0',
		'apartment_page_2_id' => '',
		'apartment_page_2_template' => 'template-apartments-grid.php',
		'apartment_page_2_button_label' => 'Apartments 2',
		'apartment_page_2_show_hero' => '0',
		'apartment_page_2_show_menu' => '0',
		'apartment_page_3_enabled' => '0',
		'apartment_page_3_id' => '',
		'apartment_page_3_template' => 'template-apartments-grid.php',
		'apartment_page_3_button_label' => 'Apartments 3',
		'apartment_page_3_show_hero' => '0',
		'apartment_page_3_show_menu' => '0',
		'contact_page_1_enabled' => '1',
		'contact_page_1_id' => '',
		'contact_page_1_template' => 'template-contact.php',
		'contact_page_1_button_label' => 'Contact',
		'contact_page_1_show_hero' => '1',
		'contact_page_1_show_menu' => '1',
		'contact_page_1_title' => 'Contact and booking inquiry',
		'contact_page_1_intro' => 'Send your dates, guest count, and preferred rental unit so the owner can reply with availability.',
		'contact_page_1_address' => "Your property name\nStreet and house number\nCity, country",
		'contact_page_1_phone' => '',
		'contact_page_1_mobile' => '',
		'contact_page_1_email' => '',
		'contact_page_1_form_shortcode' => '',
		'contact_page_1_map_query' => '',
		'contact_page_1_map_note' => 'Add arrival notes, parking instructions, or map corrections here.',
		'contact_page_1_qr_code_image_id' => '',
		'contact_page_1_booking_help_lines' => "Preferred arrival and departure dates\nNumber of adults and children\nPreferred apartment or flexible choice\nParking, arrival, or mobility questions",
		'contact_page_1_show_details' => '1',
		'contact_page_1_show_booking_help' => '1',
		'contact_page_1_show_map' => '1',
		'contact_page_1_show_content' => '1',
		'contact_page_1_show_form' => '1',
		'contact_page_1_show_map_note' => '1',
		'contact_page_2_enabled' => '0',
		'contact_page_2_id' => '',
		'contact_page_2_template' => 'template-contact.php',
		'contact_page_2_button_label' => 'Contact 2',
		'contact_page_2_show_hero' => '0',
		'contact_page_2_show_menu' => '0',
		'contact_page_2_title' => 'Contact and booking inquiry',
		'contact_page_2_intro' => '',
		'contact_page_2_address' => '',
		'contact_page_2_phone' => '',
		'contact_page_2_mobile' => '',
		'contact_page_2_email' => '',
		'contact_page_2_form_shortcode' => '',
		'contact_page_2_map_query' => '',
		'contact_page_2_map_note' => '',
		'contact_page_2_qr_code_image_id' => '',
		'contact_page_2_booking_help_lines' => '',
		'contact_page_2_show_details' => '1',
		'contact_page_2_show_booking_help' => '1',
		'contact_page_2_show_map' => '1',
		'contact_page_2_show_content' => '1',
		'contact_page_2_show_form' => '1',
		'contact_page_2_show_map_note' => '1',
		'contact_page_3_enabled' => '0',
		'contact_page_3_id' => '',
		'contact_page_3_template' => 'template-contact.php',
		'contact_page_3_button_label' => 'Contact 3',
		'contact_page_3_show_hero' => '0',
		'contact_page_3_show_menu' => '0',
		'contact_page_3_title' => 'Contact and booking inquiry',
		'contact_page_3_intro' => '',
		'contact_page_3_address' => '',
		'contact_page_3_phone' => '',
		'contact_page_3_mobile' => '',
		'contact_page_3_email' => '',
		'contact_page_3_form_shortcode' => '',
		'contact_page_3_map_query' => '',
		'contact_page_3_map_note' => '',
		'contact_page_3_qr_code_image_id' => '',
		'contact_page_3_booking_help_lines' => '',
		'contact_page_3_show_details' => '1',
		'contact_page_3_show_booking_help' => '1',
		'contact_page_3_show_map' => '1',
		'contact_page_3_show_content' => '1',
		'contact_page_3_show_form' => '1',
		'contact_page_3_show_map_note' => '1',
	);
}

function multirent_companion_settings() {
	$settings = get_option( 'multirent_settings', array() );
	return wp_parse_args( is_array( $settings ) ? $settings : array(), multirent_companion_default_settings() );
}

function multirent_companion_page_slot_count() {
	return 3;
}

function multirent_companion_page_slot( $type, $index, $settings = null ) {
	$settings = is_array( $settings ) ? $settings : multirent_companion_settings();
	$index    = min( multirent_companion_page_slot_count(), max( 1, absint( $index ) ) );
	$type     = 'contact' === $type ? 'contact' : 'apartment';
	$prefix   = $type . '_page_' . $index;

	$legacy_page_key = 'apartment' === $type ? 'apartments_page_id' : 'contact_page_id';
	$legacy_show_key = 'apartment' === $type ? 'show_apartments_page' : 'show_contact_page';

	$page_id = isset( $settings[ $prefix . '_id' ] ) ? absint( $settings[ $prefix . '_id' ] ) : 0;
	if ( 1 === $index && ! $page_id && ! empty( $settings[ $legacy_page_key ] ) ) {
		$page_id = absint( $settings[ $legacy_page_key ] );
	}

	$enabled = isset( $settings[ $prefix . '_enabled' ] ) ? '1' === (string) $settings[ $prefix . '_enabled' ] : ( 1 === $index && '1' === (string) $settings[ $legacy_show_key ] );
	$show_menu = isset( $settings[ $prefix . '_show_menu' ] ) ? '1' === (string) $settings[ $prefix . '_show_menu' ] : $enabled;
	$button_label = isset( $settings[ $prefix . '_button_label' ] ) ? trim( (string) $settings[ $prefix . '_button_label' ] ) : '';
	$template = isset( $settings[ $prefix . '_template' ] ) ? (string) $settings[ $prefix . '_template' ] : '';

	return array(
		'type'         => $type,
		'index'        => $index,
		'prefix'       => $prefix,
		'enabled'      => $enabled,
		'page_id'      => $page_id,
		'template'     => $template,
		'button_label' => $button_label,
		'show_hero'    => ! empty( $settings[ $prefix . '_show_hero' ] ) && '1' === (string) $settings[ $prefix . '_show_hero' ],
		'show_menu'    => $show_menu,
	);
}

function multirent_companion_page_slots( $type, $settings = null ) {
	$slots = array();
	for ( $index = 1; $index <= multirent_companion_page_slot_count(); $index++ ) {
		$slots[] = multirent_companion_page_slot( $type, $index, $settings );
	}
	return $slots;
}

function multirent_companion_slot_setting_keys() {
	$keys = array();
	for ( $index = 1; $index <= multirent_companion_page_slot_count(); $index++ ) {
		foreach ( array( 'enabled', 'id', 'template', 'button_label', 'show_hero', 'show_menu' ) as $suffix ) {
			$keys[] = 'apartment_page_' . $index . '_' . $suffix;
		}

		foreach ( array( 'enabled', 'id', 'template', 'button_label', 'show_hero', 'show_menu', 'title', 'intro', 'address', 'phone', 'mobile', 'email', 'form_shortcode', 'map_query', 'map_note', 'qr_code_image_id', 'booking_help_lines', 'show_details', 'show_booking_help', 'show_map', 'show_content', 'show_form', 'show_map_note' ) as $suffix ) {
			$keys[] = 'contact_page_' . $index . '_' . $suffix;
		}
	}
	return $keys;
}

function multirent_companion_copy_setting_if_unset( &$settings, $target_key, $source_key ) {
	if ( array_key_exists( $target_key, $settings ) && '' !== (string) $settings[ $target_key ] ) {
		return false;
	}

	if ( ! array_key_exists( $source_key, $settings ) || '' === (string) $settings[ $source_key ] ) {
		return false;
	}

	$settings[ $target_key ] = $settings[ $source_key ];
	return true;
}

function multirent_companion_migrate_page_slots() {
	$migration_key = 'multirent_page_slots_migrated_0_1_39';
	if ( get_option( $migration_key ) ) {
		return;
	}

	$settings = get_option( 'multirent_settings', array() );
	$settings = is_array( $settings ) ? $settings : array();
	$changed  = false;

	$changed = multirent_companion_copy_setting_if_unset( $settings, 'apartment_page_1_id', 'apartments_page_id' ) || $changed;
	$changed = multirent_companion_copy_setting_if_unset( $settings, 'apartment_page_1_enabled', 'show_apartments_page' ) || $changed;
	$changed = multirent_companion_copy_setting_if_unset( $settings, 'apartment_page_1_button_label', 'hero_button_text' ) || $changed;
	if ( empty( $settings['apartment_page_1_template'] ) ) {
		$apartment_page_id = ! empty( $settings['apartment_page_1_id'] ) ? absint( $settings['apartment_page_1_id'] ) : 0;
		$template          = $apartment_page_id ? get_post_meta( $apartment_page_id, '_wp_page_template', true ) : '';
		$settings['apartment_page_1_template'] = isset( multirent_companion_apartments_page_templates()[ $template ] ) ? $template : 'template-apartments-grid.php';
		$changed = true;
	}
	if ( ! array_key_exists( 'apartment_page_1_show_hero', $settings ) ) {
		$settings['apartment_page_1_show_hero'] = '1';
		$changed = true;
	}
	if ( ! array_key_exists( 'apartment_page_1_show_menu', $settings ) ) {
		$settings['apartment_page_1_show_menu'] = ! empty( $settings['apartment_page_1_enabled'] ) ? '1' : '0';
		$changed = true;
	}

	$contact_map = array(
		'contact_page_1_id'                 => 'contact_page_id',
		'contact_page_1_enabled'            => 'show_contact_page',
		'contact_page_1_button_label'       => 'contact_button_text',
		'contact_page_1_title'              => 'contact_page_title',
		'contact_page_1_intro'              => 'contact_page_intro',
		'contact_page_1_address'            => 'contact_address',
		'contact_page_1_phone'              => 'contact_phone',
		'contact_page_1_mobile'             => 'contact_mobile',
		'contact_page_1_email'              => 'contact_email',
		'contact_page_1_form_shortcode'     => 'contact_form_shortcode',
		'contact_page_1_map_query'          => 'contact_map_query',
		'contact_page_1_map_note'           => 'contact_map_note',
		'contact_page_1_qr_code_image_id'   => 'contact_qr_code_image_id',
		'contact_page_1_booking_help_lines' => 'booking_help_lines',
		'contact_page_1_show_details'       => 'show_contact_details',
		'contact_page_1_show_booking_help'  => 'show_booking_help',
		'contact_page_1_show_map'           => 'show_contact_map',
		'contact_page_1_show_content'       => 'show_contact_content',
		'contact_page_1_show_form'          => 'show_contact_form',
		'contact_page_1_show_map_note'      => 'show_contact_map_note',
	);
	foreach ( $contact_map as $target_key => $source_key ) {
		$changed = multirent_companion_copy_setting_if_unset( $settings, $target_key, $source_key ) || $changed;
	}
	if ( empty( $settings['contact_page_1_template'] ) ) {
		$contact_page_id = ! empty( $settings['contact_page_1_id'] ) ? absint( $settings['contact_page_1_id'] ) : 0;
		$template        = $contact_page_id ? get_post_meta( $contact_page_id, '_wp_page_template', true ) : '';
		$settings['contact_page_1_template'] = isset( multirent_companion_contact_page_templates()[ $template ] ) ? $template : 'template-contact.php';
		$changed = true;
	}
	if ( ! array_key_exists( 'contact_page_1_show_hero', $settings ) ) {
		$settings['contact_page_1_show_hero'] = ! empty( $settings['contact_page_1_enabled'] ) ? '1' : '0';
		$changed = true;
	}
	if ( ! array_key_exists( 'contact_page_1_show_menu', $settings ) ) {
		$settings['contact_page_1_show_menu'] = ! empty( $settings['contact_page_1_enabled'] ) ? '1' : '0';
		$changed = true;
	}

	if ( $changed ) {
		update_option( 'multirent_settings', $settings );
		multirent_companion_save_slot_page_templates( wp_parse_args( $settings, multirent_companion_default_settings() ) );
	}

	update_option( $migration_key, '1' );
}
add_action( 'admin_init', 'multirent_companion_migrate_page_slots', 5 );

function multirent_companion_admin_menu() {
	add_menu_page( esc_html__( 'MultiRent Setup', 'multirent-companion' ), esc_html__( 'MultiRent Setup', 'multirent-companion' ), 'manage_options', 'multirent-setup', 'multirent_companion_render_setup_page', plugins_url( 'assets/images/multirent-admin-icon.svg', __FILE__ ), 58 );
	add_submenu_page( 'multirent-setup', esc_html__( 'Website Setup', 'multirent-companion' ), esc_html__( 'Website Setup', 'multirent-companion' ), 'manage_options', 'multirent-setup', 'multirent_companion_render_setup_page' );
	add_submenu_page( 'multirent-setup', esc_html__( 'Pages & Buttons', 'multirent-companion' ), esc_html__( 'Pages & Buttons', 'multirent-companion' ), 'manage_options', 'multirent-pages-buttons', 'multirent_companion_render_pages_buttons_page' );
	add_submenu_page( 'multirent-setup', esc_html__( 'Local Page', 'multirent-companion' ), esc_html__( 'Local Page', 'multirent-companion' ), 'manage_options', 'multirent-local-page', 'multirent_companion_render_local_page' );
	add_submenu_page( 'multirent-setup', esc_html__( 'Amenities', 'multirent-companion' ), esc_html__( 'Amenities', 'multirent-companion' ), 'manage_categories', 'edit-tags.php?taxonomy=rental_amenity&post_type=rental_unit' );
	add_submenu_page( 'multirent-setup', esc_html__( 'Demo Content', 'multirent-companion' ), esc_html__( 'Demo Content', 'multirent-companion' ), 'manage_options', 'multirent-demo-content', 'multirent_companion_render_demo_content_page' );
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
	$demo_items    = array();
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
		} elseif ( in_array( $submenu_slug, array( 'multirent-pages-buttons', 'multirent-local-page' ), true ) ) {
			$page_items[] = $submenu_item;
		} elseif ( 'multirent-demo-content' === $submenu_slug ) {
			$demo_items[] = $submenu_item;
		} elseif ( 'multirent-readme' === $submenu_slug ) {
			$help_items[] = $submenu_item;
		} else {
			$other_items[] = $submenu_item;
		}
	}

	$submenu['multirent-setup'] = array_values( array_merge( $website_items, $rental_items, $page_items, $other_items, $demo_items, $help_items ) );
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
			array( 'wp-plugins', 'wp-edit-post', 'wp-editor', 'wp-element', 'wp-components', 'wp-data', 'wp-core-data', 'wp-i18n', 'wp-block-editor' ),
			MULTIRENT_COMPANION_VERSION,
			true
		);
		wp_localize_script(
			'multirent-companion-unit-sidebar',
			'MultiRentUnitSidebar',
			array(
				'fields'              => multirent_companion_unit_sidebar_fields(),
				'apartmentPageKey'    => '_multirent_apartment_page_ids',
				'apartmentPages'      => multirent_companion_apartment_page_options_for_editor(),
				'apartmentPanelTitle' => esc_html__( 'Apartment Page Assignment', 'multirent-companion' ),
				'apartmentPanelHelp'  => esc_html__( 'Choose which apartment overview pages should show this rental unit. If nothing is selected, the unit belongs to Apartment Page 1.', 'multirent-companion' ),
				'noApartmentPages'    => esc_html__( 'No enabled apartment pages are configured yet. Add them in MultiRent Setup > Pages & Buttons.', 'multirent-companion' ),
				'qrImageKey'          => '_qr_code_image_id',
				'qrImageLabel'        => esc_html__( 'QR code image', 'multirent-companion' ),
				'qrImageHelp'         => esc_html__( 'Optional QR code image shown in an extra tile on the apartment detail page.', 'multirent-companion' ),
				'qrImageButton'       => esc_html__( 'Choose QR code image', 'multirent-companion' ),
				'qrImageRemove'       => esc_html__( 'Remove QR code image', 'multirent-companion' ),
				'panelTitle'          => esc_html__( 'Apartment Details', 'multirent-companion' ),
				'imageHelp'           => esc_html__( 'Use the Apartment Images box below the editor for the main apartment photo and extra gallery photos. The QR code image, video link, and apartment map fields are in Apartment Details. Gallery images can be reordered before saving.', 'multirent-companion' ),
				'publishHelp'         => esc_html__( 'After filling these fields, click Publish or Update.', 'multirent-companion' ),
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
		if ( in_array( $key, array( 'show_hero_section', 'show_intro_section', 'show_stats_section', 'show_front_page_rentals', 'show_reviews', 'show_seo_note', 'show_migration_note', 'show_apartments_page', 'show_contact_page', 'show_local_page', 'show_contact_details', 'show_booking_help', 'show_contact_map', 'show_contact_content', 'show_contact_form', 'show_contact_map_note', 'show_local_guides', 'show_local_highlights', 'show_local_activities', 'show_local_links', 'show_local_content', 'use_custom_colors' ), true ) || preg_match( '/^(apartment|contact)_page_\d+_(enabled|show_hero|show_menu)$/', $key ) || preg_match( '/^contact_page_\d+_show_/', $key ) ) {
			$output[ $key ] = ! empty( $input[ $key ] ) ? '1' : '0';
		} elseif ( 'front_page_rental_count' === $key ) {
			$output[ $key ] = (string) min( 50, max( 1, absint( $value ) ) );
		} elseif ( 'global_font' === $key ) {
			$output[ $key ] = multirent_companion_sanitize_global_font( $value );
		} elseif ( 'hero_title_size' === $key ) {
			$output[ $key ] = multirent_companion_sanitize_hero_title_size( $value );
		} elseif ( in_array( $key, array( 'apartments_page_id', 'contact_page_id', 'local_page_id', 'contact_qr_code_image_id' ), true ) || preg_match( '/^(apartment|contact)_page_\d+_id$/', $key ) || preg_match( '/^contact_page_\d+_qr_code_image_id$/', $key ) ) {
			$output[ $key ] = (string) absint( $value );
		} elseif ( in_array( $key, array( 'hero_image', 'page_logo' ), true ) ) {
			$output[ $key ] = absint( $value );
		} elseif ( 'contact_email' === $key || preg_match( '/^contact_page_\d+_email$/', $key ) ) {
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

	if ( in_array( $action, array( 'save_settings', 'save_page_slots', 'save_apartments_page', 'save_contact_page', 'save_local_page' ), true ) ) {
		$settings = isset( $_POST['multirent_settings'] ) && is_array( $_POST['multirent_settings'] ) ? multirent_companion_sanitize_settings( $_POST['multirent_settings'], $scope ) : multirent_companion_settings();
		update_option( 'multirent_settings', $settings );
		multirent_companion_sync_optional_page_visibility( $settings );

		if ( 'save_page_slots' === $action ) {
			multirent_companion_save_slot_page_templates( $settings );
			multirent_companion_apply_top_menu( $settings['menu_items'] );
			add_settings_error( 'multirent_messages', 'page_slots_saved', esc_html__( 'Page and hero button settings saved.', 'multirent-companion' ), 'updated' );
		} elseif ( 'save_apartments_page' === $action ) {
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

	if ( 'create_starter_site' === $action ) {
		if ( empty( $_POST['multirent_confirm_starter_site'] ) ) {
			add_settings_error( 'multirent_messages', 'starter_site_not_confirmed', esc_html__( 'Confirm the starter content action before running it.', 'multirent-companion' ), 'error' );
			return;
		}

		multirent_companion_create_starter_site();
		add_settings_error( 'multirent_messages', 'site_created', esc_html__( 'Starter pages, menu, amenities, and four rental units created.', 'multirent-companion' ), 'updated' );
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

	foreach ( array( 'apartment', 'contact' ) as $slot_type ) {
		foreach ( multirent_companion_page_slots( $slot_type, $settings ) as $slot ) {
			$page = multirent_companion_page_exists( $slot['page_id'] );
			if ( ! $page ) {
				continue;
			}

			wp_update_post(
				array(
					'ID'          => $page->ID,
					'post_status' => $slot['enabled'] ? 'publish' : 'draft',
				)
			);
		}
	}
}

function multirent_companion_save_slot_page_templates( $settings = null ) {
	$settings = is_array( $settings ) ? $settings : multirent_companion_settings();

	foreach ( array( 'apartment', 'contact' ) as $slot_type ) {
		$role = 'apartment' === $slot_type ? 'apartments' : 'contact';
		$templates = multirent_companion_role_page_templates( $role );
		foreach ( multirent_companion_page_slots( $slot_type, $settings ) as $slot ) {
			$page = multirent_companion_page_exists( $slot['page_id'] );
			if ( ! $page || empty( $templates[ $slot['template'] ] ) ) {
				continue;
			}

			update_post_meta( $page->ID, '_wp_page_template', $slot['template'] );
		}
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
	return multirent_companion_ensure_starter_units( $count );
}

function multirent_companion_get_starter_unit( $index ) {
	$existing_units = get_posts(
		array(
			'post_type'      => 'rental_unit',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
			'posts_per_page' => 1,
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'meta_key'       => '_multirent_starter_unit_index',
			'meta_value'     => (string) absint( $index ),
		)
	);

	if ( $existing_units ) {
		return $existing_units[0];
	}

	return get_page_by_path( 'rental-unit-' . absint( $index ), OBJECT, 'rental_unit' );
}

function multirent_companion_ensure_starter_units( $count = 4 ) {
	$author_id = multirent_companion_content_author_id();
	$created   = 0;

	for ( $index = 1; $index <= $count; $index++ ) {
		if ( multirent_companion_get_starter_unit( $index ) ) {
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'post_type'    => 'rental_unit',
				'post_status'  => 'publish',
				'post_author'  => $author_id,
				'post_title'   => sprintf( 'Rental Unit %d', $index ),
				'post_name'    => sprintf( 'rental-unit-%d', $index ),
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
			update_post_meta( $post_id, '_multirent_starter_unit_index', (string) $index );
			$created++;
		}
	}

	return $created;
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
	multirent_companion_ensure_starter_units( 4 );

	$settings = multirent_companion_settings();
	if ( ! empty( $page_ids['Apartments'] ) ) {
		$settings['apartments_page_id'] = (string) absint( $page_ids['Apartments'] );
		$settings['apartment_page_1_id'] = (string) absint( $page_ids['Apartments'] );
		$settings['apartment_page_1_enabled'] = '1';
		$settings['apartment_page_1_show_hero'] = '1';
		$settings['apartment_page_1_show_menu'] = '1';
		$settings['apartment_page_1_button_label'] = $settings['apartment_page_1_button_label'] ?: __( 'Apartments', 'multirent-companion' );
	}
	if ( ! empty( $page_ids['Contact'] ) ) {
		$settings['contact_page_id'] = (string) absint( $page_ids['Contact'] );
		$settings['contact_page_1_id'] = (string) absint( $page_ids['Contact'] );
		$settings['contact_page_1_enabled'] = '1';
		$settings['contact_page_1_show_hero'] = '1';
		$settings['contact_page_1_show_menu'] = '1';
		$settings['contact_page_1_button_label'] = $settings['contact_page_1_button_label'] ?: __( 'Contact', 'multirent-companion' );
	}
	if ( ! empty( $page_ids['Local'] ) ) {
		$settings['local_page_id'] = (string) absint( $page_ids['Local'] );
	}
	update_option( 'multirent_settings', $settings );

	multirent_companion_sync_optional_page_visibility( $settings );
	multirent_companion_apply_top_menu( multirent_companion_settings()['menu_items'] );

	flush_rewrite_rules();
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
	$items = multirent_companion_merge_unique_menu_items( $items, multirent_companion_slot_menu_items( multirent_companion_settings() ) );
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

function multirent_companion_menu_item_key( $url ) {
	$path = wp_parse_url( $url, PHP_URL_PATH );
	if ( $path ) {
		return '/' . trim( (string) $path, '/' ) . '/';
	}

	return strtolower( trim( (string) $url ) );
}

function multirent_companion_merge_unique_menu_items( $items, $extra_items ) {
	$seen = array();
	foreach ( $items as $item ) {
		$seen[ multirent_companion_menu_item_key( $item['url'] ) ] = true;
	}

	foreach ( $extra_items as $item ) {
		$key = multirent_companion_menu_item_key( $item['url'] );
		if ( isset( $seen[ $key ] ) ) {
			continue;
		}

		$items[]      = $item;
		$seen[ $key ] = true;
	}

	return $items;
}

function multirent_companion_slot_menu_items( $settings = null ) {
	$settings = is_array( $settings ) ? $settings : multirent_companion_settings();
	$items    = array();

	foreach ( array( 'apartment', 'contact' ) as $slot_type ) {
		foreach ( multirent_companion_page_slots( $slot_type, $settings ) as $slot ) {
			if ( ! $slot['enabled'] || ! $slot['show_menu'] || ! $slot['page_id'] ) {
				continue;
			}

			$permalink = get_permalink( $slot['page_id'] );
			if ( ! $permalink ) {
				continue;
			}

			$items[] = array(
				'label' => $slot['button_label'] ? $slot['button_label'] : get_the_title( $slot['page_id'] ),
				'url'   => $permalink,
			);
		}
	}

	return $items;
}

function multirent_companion_filter_hidden_menu_items( $items, $settings ) {
	$hidden_paths = array();
	foreach ( multirent_companion_page_roles() as $role => $role_config ) {
		if ( in_array( $role, array( 'apartments', 'contact' ), true ) ) {
			continue;
		}

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

	foreach ( array( 'apartment', 'contact' ) as $slot_type ) {
		foreach ( multirent_companion_page_slots( $slot_type, $settings ) as $slot ) {
			if ( $slot['enabled'] && $slot['show_menu'] ) {
				continue;
			}

			if ( ! $slot['page_id'] ) {
				continue;
			}

			$path = wp_parse_url( get_permalink( $slot['page_id'] ), PHP_URL_PATH );
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

		<h2><?php esc_html_e( 'Starter Content', 'multirent-companion' ); ?></h2>
		<p><?php esc_html_e( 'Use this first on a new site to create Home, Apartments, Contact, and Local pages, assign the Apartments page, create the top menu and amenities, and add four starter rental units you can rename or replace.', 'multirent-companion' ); ?></p>
		<p class="description"><?php esc_html_e( 'On an existing site this can set the front page, assign page templates, update the generated top menu, and publish or draft MultiRent role pages. Review the affected pages before using it outside a fresh setup.', 'multirent-companion' ); ?></p>
		<form method="post" action="" style="margin-bottom:24px;">
			<?php wp_nonce_field( 'multirent_setup_action', 'multirent_setup_nonce' ); ?>
			<input type="hidden" name="multirent_action" value="create_starter_site">
			<label><input type="checkbox" name="multirent_confirm_starter_site" value="1" required> <?php esc_html_e( 'I understand this will create missing starter content and may update the front page, templates, visibility, and generated top menu.', 'multirent-companion' ); ?></label>
			<?php submit_button( esc_html__( 'Create Starter Pages, Menu, Amenities, and Rental Units', 'multirent-companion' ), 'secondary' ); ?>
		</form>

		<hr>

		<form method="post" action="">
			<?php wp_nonce_field( 'multirent_setup_action', 'multirent_setup_nonce' ); ?>
			<input type="hidden" name="multirent_action" value="save_settings">
			<input type="hidden" name="multirent_settings_scope" value="property_name,property_tagline,page_logo,global_font,show_hero_section,hero_title,hero_text,hero_title_size,hero_image,show_intro_section,intro_eyebrow,intro_title,intro_text,show_stats_section,stats_lines,show_front_page_rentals,front_page_rental_count,reviews_shortcode,show_reviews,show_seo_note,show_migration_note,menu_items,color_scheme,use_custom_colors,color_primary,color_dark,color_surface,color_accent">
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
						<p class="description"><?php esc_html_e( 'These checkboxes hide or show full homepage blocks. The text and image fields below are kept even when their block is hidden.', 'multirent-companion' ); ?></p>
					</td>
				</tr>
			</table>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="multirent-global-font"><?php esc_html_e( 'Global site font', 'multirent-companion' ); ?></label></th>
					<td>
						<select id="multirent-global-font" name="multirent_settings[global_font]">
							<?php foreach ( multirent_companion_global_font_choices() as $font_key => $font_label ) : ?>
								<option value="<?php echo esc_attr( $font_key ); ?>" <?php selected( $settings['global_font'], $font_key ); ?>><?php echo esc_html( $font_label ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php multirent_companion_description( 'global_font' ); ?>
					</td>
				</tr>
				<?php foreach ( array( 'property_name', 'property_tagline', 'hero_title', 'hero_text', 'intro_eyebrow', 'intro_title', 'intro_text', 'stats_lines', 'reviews_shortcode' ) as $key ) : ?>
					<tr>
						<th scope="row"><label for="multirent-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( ucwords( str_replace( '_', ' ', $key ) ) ); ?></label></th>
						<td>
							<?php if ( in_array( $key, array( 'hero_text', 'intro_text', 'stats_lines' ), true ) ) : ?>
								<textarea class="large-text" rows="4" id="multirent-<?php echo esc_attr( $key ); ?>" name="multirent_settings[<?php echo esc_attr( $key ); ?>]"><?php echo esc_textarea( $settings[ $key ] ); ?></textarea>
							<?php else : ?>
								<input class="regular-text" id="multirent-<?php echo esc_attr( $key ); ?>" name="multirent_settings[<?php echo esc_attr( $key ); ?>]" type="text" value="<?php echo esc_attr( $settings[ $key ] ); ?>">
							<?php endif; ?>
							<?php multirent_companion_description( $key ); ?>
						</td>
					</tr>
					<?php if ( 'hero_text' === $key ) : ?>
						<tr>
							<th scope="row"><label for="multirent-hero-title-size"><?php esc_html_e( 'Hero title size', 'multirent-companion' ); ?></label></th>
							<td>
								<input class="small-text" id="multirent-hero-title-size" name="multirent_settings[hero_title_size]" type="number" min="0.8" max="5" step="0.1" value="<?php echo esc_attr( $settings['hero_title_size'] ); ?>"> <span><?php esc_html_e( 'cm', 'multirent-companion' ); ?></span>
								<?php multirent_companion_description( 'hero_title_size' ); ?>
							</td>
						</tr>
					<?php endif; ?>
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
			<p><?php esc_html_e( 'Landing page settings stay here because the landing page is mandatory. Use Pages & Buttons for apartment and contact pages. Use Local Page for local information.', 'multirent-companion' ); ?></p>

			<h2><?php esc_html_e( 'Top Menu Builder', 'multirent-companion' ); ?></h2>
			<p><?php esc_html_e( 'Add one manual menu link per line using this simple format: Label | URL. Use / for home, /apartments/ or /contact/ for pages, or a full external URL. Apartment and Contact page slots can also add themselves from Pages & Buttons when Show in top menu is checked; duplicate page URLs are skipped.', 'multirent-companion' ); ?></p>
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

function multirent_companion_render_demo_content_page() {
	settings_errors( 'multirent_messages' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Demo Content', 'multirent-companion' ); ?></h1>
		<p><?php esc_html_e( 'The full MultiRent demo is hosted separately so this WordPress site stays light and does not download sample media.', 'multirent-companion' ); ?></p>
		<p><?php esc_html_e( 'Open the public demo to preview apartment pages, galleries, maps, QR examples, contact details, local guide sections, and the finished theme layout before adding your own content.', 'multirent-companion' ); ?></p>
		<p><a class="button button-primary" href="https://demo.multirent.online" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open Public Demo', 'multirent-companion' ); ?></a></p>
		<p class="description"><?php esc_html_e( 'Use Website Setup to create starter pages, menu, amenities, and rental units for this site.', 'multirent-companion' ); ?></p>
	</div>
	<?php
}

function multirent_companion_page_select_any_field( $field_name, $selected_page_id, $field_id ) {
	$pages = get_pages(
		array(
			'post_status' => 'publish,draft,pending,private',
			'sort_column' => 'post_title',
		)
	);
	?>
	<select name="<?php echo esc_attr( $field_name ); ?>" id="<?php echo esc_attr( $field_id ); ?>">
		<option value="0"><?php esc_html_e( 'Select a page', 'multirent-companion' ); ?></option>
		<?php foreach ( $pages as $page ) : ?>
			<option value="<?php echo esc_attr( $page->ID ); ?>" <?php selected( absint( $selected_page_id ), $page->ID ); ?>><?php echo esc_html( $page->post_title . ' (#' . $page->ID . ')' ); ?></option>
		<?php endforeach; ?>
	</select>
	<?php
}

function multirent_companion_render_slot_page_select( $prefix, $page_id ) {
	multirent_companion_page_select_any_field( 'multirent_settings[' . $prefix . '_id]', $page_id, 'multirent-' . $prefix . '-id' );
}

function multirent_companion_page_slot_description( $suffix, $type = '' ) {
	$descriptions = array(
		'enabled'            => __( 'Makes this page slot available for public templates, top-menu inclusion, and hero-button selection.', 'multirent-companion' ),
		'show_menu'          => __( 'Adds this selected page to the generated/header menu. If the same URL is already listed in Top Menu Builder, it will not be duplicated.', 'multirent-companion' ),
		'id'                 => __( 'Choose the WordPress page this slot controls. Starter content uses Page 1 as the initial public page.', 'multirent-companion' ),
		'template'           => __( 'Choose the public layout used by the selected WordPress page.', 'multirent-companion' ),
		'button_label'       => __( 'Text shown on the homepage hero button for this page slot.', 'multirent-companion' ),
		'show_hero'          => __( 'Shows this selected page as a button under the homepage hero.', 'multirent-companion' ),
		'title'              => __( 'Main heading shown at the top of this contact page.', 'multirent-companion' ),
		'intro'              => __( 'Short introduction shown below this contact page heading.', 'multirent-companion' ),
		'address'            => __( 'Address block shown in this contact page details card. Use one line per address line.', 'multirent-companion' ),
		'phone'              => __( 'Primary phone number shown on this contact page. Leave empty to hide it.', 'multirent-companion' ),
		'mobile'             => __( 'Mobile phone number shown on this contact page. Leave empty to hide it.', 'multirent-companion' ),
		'email'              => __( 'Email address shown on this contact page. Leave empty to hide it.', 'multirent-companion' ),
		'form_shortcode'     => __( 'Contact-form shortcode rendered on this contact page, for example [contact-form-7 id="123"].', 'multirent-companion' ),
		'map_query'          => __( 'Search text for the embedded Google map, such as property name, street address, city, and country.', 'multirent-companion' ),
		'map_note'           => __( 'Short note below the map for parking, arrival instructions, or map corrections.', 'multirent-companion' ),
		'qr_code_image_id'   => __( 'Optional QR code image for this contact page. Leave empty to hide the QR tile.', 'multirent-companion' ),
		'booking_help_lines' => __( 'Checklist shown before guests send an inquiry. Add one requested detail per line.', 'multirent-companion' ),
		'show_details'       => __( 'Shows or hides the contact details card on this contact page.', 'multirent-companion' ),
		'show_booking_help'  => __( 'Shows or hides the booking-help checklist on this contact page.', 'multirent-companion' ),
		'show_map'           => __( 'Shows or hides the embedded map section on this contact page.', 'multirent-companion' ),
		'show_content'       => __( 'Shows or hides normal WordPress page-editor content on this contact page.', 'multirent-companion' ),
		'show_form'          => __( 'Shows or hides the contact form shortcode area on this contact page.', 'multirent-companion' ),
		'show_map_note'      => __( 'Shows or hides the note below the embedded map.', 'multirent-companion' ),
	);

	if ( 'apartment' === $type && 'template' === $suffix ) {
		return __( 'Choose how rental units assigned to this apartment page are displayed.', 'multirent-companion' );
	}

	return isset( $descriptions[ $suffix ] ) ? $descriptions[ $suffix ] : '';
}

function multirent_companion_slot_description( $suffix, $type = '' ) {
	$description = multirent_companion_page_slot_description( $suffix, $type );
	if ( '' === $description ) {
		return;
	}
	?>
	<span class="description multirent-field-description"><?php echo esc_html( $description ); ?></span>
	<?php
}

function multirent_companion_render_apartment_slot_panel( $slot, $templates ) {
	$prefix = $slot['prefix'];
	?>
	<details class="multirent-slot-panel">
		<summary><?php echo esc_html( sprintf( __( 'Apartment Page %d', 'multirent-companion' ), $slot['index'] ) ); ?><?php echo $slot['page_id'] ? ' - ' . esc_html( get_the_title( $slot['page_id'] ) ) : ''; ?></summary>
		<div class="multirent-slot-panel-body">
			<p><label><input name="multirent_settings[<?php echo esc_attr( $prefix ); ?>_enabled]" type="checkbox" value="1" <?php checked( $slot['enabled'] ); ?>> <?php esc_html_e( 'Enable this apartment page', 'multirent-companion' ); ?></label><?php multirent_companion_slot_description( 'enabled', 'apartment' ); ?></p>
			<p><label><input name="multirent_settings[<?php echo esc_attr( $prefix ); ?>_show_menu]" type="checkbox" value="1" <?php checked( $slot['show_menu'] ); ?>> <?php esc_html_e( 'Show in top menu', 'multirent-companion' ); ?></label><?php multirent_companion_slot_description( 'show_menu', 'apartment' ); ?></p>
			<div class="multirent-field-row">
				<label for="multirent-<?php echo esc_attr( $prefix ); ?>-id"><?php esc_html_e( 'WordPress page', 'multirent-companion' ); ?></label>
				<?php multirent_companion_render_slot_page_select( $prefix, $slot['page_id'] ); ?>
				<?php multirent_companion_slot_description( 'id', 'apartment' ); ?>
			</div>
			<div class="multirent-field-row">
				<label for="multirent-<?php echo esc_attr( $prefix ); ?>-template"><?php esc_html_e( 'Apartment template', 'multirent-companion' ); ?></label>
				<select id="multirent-<?php echo esc_attr( $prefix ); ?>-template" name="multirent_settings[<?php echo esc_attr( $prefix ); ?>_template]">
					<?php foreach ( $templates as $template_file => $template_data ) : ?>
						<option value="<?php echo esc_attr( $template_file ); ?>" <?php selected( $slot['template'], $template_file ); ?>><?php echo esc_html( $template_data['label'] ); ?></option>
					<?php endforeach; ?>
				</select>
				<?php multirent_companion_slot_description( 'template', 'apartment' ); ?>
			</div>
			<?php if ( $slot['page_id'] ) : ?><p><a class="button button-secondary" href="<?php echo esc_url( get_permalink( $slot['page_id'] ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Preview page', 'multirent-companion' ); ?></a></p><?php endif; ?>
		</div>
	</details>
	<?php
}

function multirent_companion_render_contact_slot_panel( $slot, $templates, $settings ) {
	$prefix = $slot['prefix'];
	?>
	<details class="multirent-slot-panel">
		<summary><?php echo esc_html( sprintf( __( 'Contact Page %d', 'multirent-companion' ), $slot['index'] ) ); ?><?php echo $slot['page_id'] ? ' - ' . esc_html( get_the_title( $slot['page_id'] ) ) : ''; ?></summary>
		<div class="multirent-slot-panel-body">
			<p><label><input name="multirent_settings[<?php echo esc_attr( $prefix ); ?>_enabled]" type="checkbox" value="1" <?php checked( $slot['enabled'] ); ?>> <?php esc_html_e( 'Enable this contact page', 'multirent-companion' ); ?></label><?php multirent_companion_slot_description( 'enabled', 'contact' ); ?></p>
			<p><label><input name="multirent_settings[<?php echo esc_attr( $prefix ); ?>_show_menu]" type="checkbox" value="1" <?php checked( $slot['show_menu'] ); ?>> <?php esc_html_e( 'Show in top menu', 'multirent-companion' ); ?></label><?php multirent_companion_slot_description( 'show_menu', 'contact' ); ?></p>
			<div class="multirent-field-grid">
				<div class="multirent-field-row"><label for="multirent-<?php echo esc_attr( $prefix ); ?>-id"><?php esc_html_e( 'WordPress page', 'multirent-companion' ); ?></label><?php multirent_companion_render_slot_page_select( $prefix, $slot['page_id'] ); ?><?php multirent_companion_slot_description( 'id', 'contact' ); ?></div>
				<div class="multirent-field-row"><label for="multirent-<?php echo esc_attr( $prefix ); ?>-template"><?php esc_html_e( 'Contact template', 'multirent-companion' ); ?></label><select id="multirent-<?php echo esc_attr( $prefix ); ?>-template" name="multirent_settings[<?php echo esc_attr( $prefix ); ?>_template]">
					<?php foreach ( $templates as $template_file => $template_data ) : ?>
						<option value="<?php echo esc_attr( $template_file ); ?>" <?php selected( $slot['template'], $template_file ); ?>><?php echo esc_html( $template_data['label'] ); ?></option>
					<?php endforeach; ?>
				</select><?php multirent_companion_slot_description( 'template', 'contact' ); ?></div>
			</div>

			<details class="multirent-inner-panel" open><summary><?php esc_html_e( 'Basic', 'multirent-companion' ); ?></summary>
				<?php foreach ( array( 'title', 'intro' ) as $suffix ) : ?>
					<?php multirent_companion_render_slot_text_field( $prefix, $suffix, $settings, in_array( $suffix, array( 'intro' ), true ) ); ?>
				<?php endforeach; ?>
			</details>
			<details class="multirent-inner-panel"><summary><?php esc_html_e( 'Contact Details', 'multirent-companion' ); ?></summary>
				<?php foreach ( array( 'address', 'phone', 'mobile', 'email' ) as $suffix ) : ?>
					<?php multirent_companion_render_slot_text_field( $prefix, $suffix, $settings, 'address' === $suffix ); ?>
				<?php endforeach; ?>
			</details>
			<details class="multirent-inner-panel"><summary><?php esc_html_e( 'Map & QR', 'multirent-companion' ); ?></summary>
				<?php foreach ( array( 'map_query', 'map_note' ) as $suffix ) : ?>
					<?php multirent_companion_render_slot_text_field( $prefix, $suffix, $settings, 'map_note' === $suffix ); ?>
				<?php endforeach; ?>
				<p><?php esc_html_e( 'QR code image', 'multirent-companion' ); ?></p>
				<?php multirent_companion_media_field( 'multirent_settings[' . $prefix . '_qr_code_image_id]', absint( $settings[ $prefix . '_qr_code_image_id' ] ), __( 'Choose QR code image', 'multirent-companion' ), __( 'Remove QR code image', 'multirent-companion' ) ); ?>
				<?php multirent_companion_slot_description( 'qr_code_image_id', 'contact' ); ?>
			</details>
			<details class="multirent-inner-panel"><summary><?php esc_html_e( 'Form & Booking Help', 'multirent-companion' ); ?></summary>
				<?php foreach ( array( 'form_shortcode', 'booking_help_lines' ) as $suffix ) : ?>
					<?php multirent_companion_render_slot_text_field( $prefix, $suffix, $settings, 'booking_help_lines' === $suffix ); ?>
				<?php endforeach; ?>
			</details>
			<details class="multirent-inner-panel"><summary><?php esc_html_e( 'Visibility', 'multirent-companion' ); ?></summary>
				<div class="multirent-slot-checkboxes">
					<?php foreach ( array( 'details' => __( 'Details', 'multirent-companion' ), 'booking_help' => __( 'Booking help', 'multirent-companion' ), 'map' => __( 'Map', 'multirent-companion' ), 'content' => __( 'Page content', 'multirent-companion' ), 'form' => __( 'Form', 'multirent-companion' ), 'map_note' => __( 'Map note', 'multirent-companion' ) ) as $suffix => $label ) : ?>
						<p><label><input name="multirent_settings[<?php echo esc_attr( $prefix . '_show_' . $suffix ); ?>]" type="checkbox" value="1" <?php checked( $settings[ $prefix . '_show_' . $suffix ], '1' ); ?>> <?php echo esc_html( $label ); ?></label><?php multirent_companion_slot_description( 'show_' . $suffix, 'contact' ); ?></p>
					<?php endforeach; ?>
				</div>
			</details>
			<?php if ( $slot['page_id'] ) : ?><p><a class="button button-secondary" href="<?php echo esc_url( get_permalink( $slot['page_id'] ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Preview page', 'multirent-companion' ); ?></a></p><?php endif; ?>
		</div>
	</details>
	<?php
}

function multirent_companion_render_slot_text_field( $prefix, $suffix, $settings, $textarea = false ) {
	$key   = $prefix . '_' . $suffix;
	$label = ucwords( str_replace( '_', ' ', $suffix ) );
	?>
	<p class="multirent-field-row"><label for="multirent-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
	<?php if ( $textarea ) : ?>
		<textarea class="large-text" rows="3" id="multirent-<?php echo esc_attr( $key ); ?>" name="multirent_settings[<?php echo esc_attr( $key ); ?>]"><?php echo esc_textarea( $settings[ $key ] ); ?></textarea>
	<?php else : ?>
		<input class="regular-text" id="multirent-<?php echo esc_attr( $key ); ?>" name="multirent_settings[<?php echo esc_attr( $key ); ?>]" type="text" value="<?php echo esc_attr( $settings[ $key ] ); ?>">
	<?php endif; ?><?php multirent_companion_slot_description( $suffix, str_starts_with( $prefix, 'contact' ) ? 'contact' : 'apartment' ); ?></p>
	<?php
}

function multirent_companion_render_pages_buttons_page() {
	$settings            = multirent_companion_settings();
	$apartment_templates = multirent_companion_apartments_page_templates();
	$contact_templates   = multirent_companion_contact_page_templates();
	$scope               = implode( ',', multirent_companion_slot_setting_keys() );
	settings_errors( 'multirent_messages' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Pages & Buttons', 'multirent-companion' ); ?></h1>
		<p><?php esc_html_e( 'Configure up to three apartment pages and three contact pages. Use the tabs below to keep setup readable.', 'multirent-companion' ); ?></p>
		<form method="post" action="">
			<?php wp_nonce_field( 'multirent_setup_action', 'multirent_setup_nonce' ); ?>
			<input type="hidden" name="multirent_action" value="save_page_slots">
			<input type="hidden" name="multirent_settings_scope" value="<?php echo esc_attr( $scope ); ?>">
			<div class="multirent-tabs">
				<input type="radio" id="multirent-tab-apartments" name="multirent_pages_tab" checked>
				<label for="multirent-tab-apartments"><?php esc_html_e( 'Apartment Pages', 'multirent-companion' ); ?></label>
				<input type="radio" id="multirent-tab-contacts" name="multirent_pages_tab">
				<label for="multirent-tab-contacts"><?php esc_html_e( 'Contact Pages', 'multirent-companion' ); ?></label>
				<input type="radio" id="multirent-tab-hero" name="multirent_pages_tab">
				<label for="multirent-tab-hero"><?php esc_html_e( 'Hero Buttons', 'multirent-companion' ); ?></label>
				<input type="radio" id="multirent-tab-help" name="multirent_pages_tab">
				<label for="multirent-tab-help"><?php esc_html_e( 'Help', 'multirent-companion' ); ?></label>

				<section class="multirent-tab-panel multirent-tab-panel-apartments">
					<?php foreach ( multirent_companion_page_slots( 'apartment', $settings ) as $slot ) : ?>
						<?php multirent_companion_render_apartment_slot_panel( $slot, $apartment_templates ); ?>
					<?php endforeach; ?>
				</section>
				<section class="multirent-tab-panel multirent-tab-panel-contacts">
					<?php foreach ( multirent_companion_page_slots( 'contact', $settings ) as $slot ) : ?>
						<?php multirent_companion_render_contact_slot_panel( $slot, $contact_templates, $settings ); ?>
					<?php endforeach; ?>
				</section>
				<section class="multirent-tab-panel multirent-tab-panel-hero">
					<p><?php esc_html_e( 'Choose which enabled pages appear as buttons under the homepage hero image.', 'multirent-companion' ); ?></p>
					<div class="multirent-hero-button-list">
						<?php foreach ( array_merge( multirent_companion_page_slots( 'apartment', $settings ), multirent_companion_page_slots( 'contact', $settings ) ) as $slot ) : ?>
							<?php $prefix = $slot['prefix']; ?>
							<div class="multirent-hero-button-row">
								<label><input name="multirent_settings[<?php echo esc_attr( $prefix ); ?>_show_hero]" type="checkbox" value="1" <?php checked( $slot['show_hero'] ); ?>> <?php echo esc_html( sprintf( '%s Page %d', 'apartment' === $slot['type'] ? __( 'Apartment', 'multirent-companion' ) : __( 'Contact', 'multirent-companion' ), $slot['index'] ) ); ?></label>
								<input class="regular-text" name="multirent_settings[<?php echo esc_attr( $prefix ); ?>_button_label]" type="text" value="<?php echo esc_attr( $slot['button_label'] ); ?>" placeholder="<?php esc_attr_e( 'Button label', 'multirent-companion' ); ?>" aria-label="<?php esc_attr_e( 'Button label', 'multirent-companion' ); ?>">
								<span class="multirent-hero-button-page description"><?php echo $slot['page_id'] ? esc_html( get_the_title( $slot['page_id'] ) . ' (#' . $slot['page_id'] . ')' ) : esc_html__( 'No page assigned yet', 'multirent-companion' ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				</section>
				<section class="multirent-tab-panel multirent-tab-panel-help">
					<div class="notice-card">
						<h2><?php esc_html_e( 'How this works', 'multirent-companion' ); ?></h2>
						<p><?php esc_html_e( 'Older single Apartments and Contact pages migrate into Page 1. Rental units with no assignment appear on Apartment Page 1.', 'multirent-companion' ); ?></p>
						<p><?php esc_html_e( 'Assign rental units from each Rental Unit editor. Enabled apartment page slots can show different groups of units.', 'multirent-companion' ); ?></p>
					</div>
				</section>
			</div>
			<?php submit_button( esc_html__( 'Save Pages & Buttons', 'multirent-companion' ) ); ?>
		</form>
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

