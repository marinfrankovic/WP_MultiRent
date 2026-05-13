<?php
/**
 * Plugin Name: MultiRent Companion
 * Description: End-to-end setup tools, rental unit management, amenities, and GUI settings for the Multi Apartment Rental theme.
 * Version: 0.1.15
 * Requires at least: 6.5
 * Requires PHP: 8.1
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

define( 'MULTIRENT_COMPANION_VERSION', '0.1.15' );

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
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
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
			'hierarchical' => false,
			'rewrite'      => array( 'slug' => 'amenity' ),
			'show_in_rest' => true,
			'show_in_menu' => 'multirent-setup',
		)
	);
}
add_action( 'init', 'multirent_companion_register_content_types' );

function multirent_companion_unit_fields() {
	return array(
		'capacity'   => esc_html__( 'Guest capacity', 'multirent-companion' ),
		'bedrooms'   => esc_html__( 'Bedrooms', 'multirent-companion' ),
		'bathrooms'  => esc_html__( 'Bathrooms', 'multirent-companion' ),
		'size'       => esc_html__( 'Size', 'multirent-companion' ),
		'distance'   => esc_html__( 'Location note', 'multirent-companion' ),
		'price_note' => esc_html__( 'Price note', 'multirent-companion' ),
		'booking_url'=> esc_html__( 'Booking or inquiry URL', 'multirent-companion' ),
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
				'sanitize_callback' => 'booking_url' === $key ? 'esc_url_raw' : 'sanitize_text_field',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'multirent_companion_register_unit_meta' );

function multirent_companion_unit_sidebar_fields() {
	$fields = array();
	foreach ( multirent_companion_unit_fields() as $key => $label ) {
		$fields[] = array(
			'key'         => '_' . $key,
			'label'       => $label,
			'type'        => 'booking_url' === $key ? 'url' : 'text',
			'description' => multirent_companion_field_description( $key ),
		);
	}
	return $fields;
}

function multirent_companion_field_descriptions() {
	return array(
		'property_name'       => esc_html__( 'Public property or rental-business name shown in the header and key theme areas.', 'multirent-companion' ),
		'hero_title'          => esc_html__( 'Main landing-page headline. Use a short phrase that explains the stay you offer.', 'multirent-companion' ),
		'hero_text'           => esc_html__( 'Short landing-page introduction below the headline.', 'multirent-companion' ),
		'hero_button_text'    => esc_html__( 'Text shown on the main landing-page call-to-action button.', 'multirent-companion' ),
		'hero_button_url'     => esc_html__( 'Where the hero button opens. Use a page path such as /apartments/ or a full URL.', 'multirent-companion' ),
		'intro_title'         => esc_html__( 'Heading for the landing-page intro section below the hero.', 'multirent-companion' ),
		'intro_text'          => esc_html__( 'Text for the landing-page intro section. Explain what guests can manage or discover.', 'multirent-companion' ),
		'stats_lines'         => esc_html__( 'Landing-page facts, one per line, using value | label. Example: 4 | Apartments.', 'multirent-companion' ),
		'show_front_page_rentals' => esc_html__( 'When enabled, rental-unit cards appear on the homepage below the intro and stats.', 'multirent-companion' ),
		'front_page_rental_count' => esc_html__( 'Maximum number of rental units shown on the homepage. Use the Apartments page for the full list.', 'multirent-companion' ),
		'reviews_shortcode'   => esc_html__( 'Shortcode from a reviews plugin. The reviews section also needs the Google Reviews checkbox enabled.', 'multirent-companion' ),
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
		'distance'            => esc_html__( 'Short location note, such as 100 m from beach or City center.', 'multirent-companion' ),
		'price_note'          => esc_html__( 'Short price message, such as On request, From 90 EUR, or Seasonal rates.', 'multirent-companion' ),
		'booking_url'         => esc_html__( 'Link for booking or inquiry button for this apartment. Use a full URL or a site page path.', 'multirent-companion' ),
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
	add_meta_box( 'multirent_unit_details', esc_html__( 'Apartment Page Editor', 'multirent-companion' ), 'multirent_companion_render_unit_meta_box', 'rental_unit', 'normal', 'high' );
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
		<h3><?php esc_html_e( 'Apartment Details', 'multirent-companion' ); ?></h3>
		<p><?php esc_html_e( 'Fill these fields once. The theme will show them on cards and apartment detail pages.', 'multirent-companion' ); ?></p>
	<?php
	foreach ( multirent_companion_unit_fields() as $key => $label ) {
		$value = get_post_meta( $post->ID, '_' . $key, true );
		$type  = 'booking_url' === $key ? 'url' : 'text';
		?>
		<p>
			<label for="multirent-<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $label ); ?></strong></label>
			<input class="widefat" id="multirent-<?php echo esc_attr( $key ); ?>" name="multirent_unit[<?php echo esc_attr( $key ); ?>]" type="<?php echo esc_attr( $type ); ?>" value="<?php echo esc_attr( $value ); ?>">
			<?php multirent_companion_description( $key ); ?>
		</p>
		<?php
	}
	?>
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

	$values = isset( $_POST['multirent_unit'] ) && is_array( $_POST['multirent_unit'] ) ? wp_unslash( $_POST['multirent_unit'] ) : array();
	foreach ( multirent_companion_unit_fields() as $key => $label ) {
		$value = isset( $values[ $key ] ) ? sanitize_text_field( $values[ $key ] ) : '';
		if ( 'booking_url' === $key ) {
			$value = esc_url_raw( $value );
		}
		update_post_meta( $post_id, '_' . $key, $value );
	}

	if ( isset( $_POST['multirent_featured_image_id'] ) ) {
		$featured_image_id = absint( $_POST['multirent_featured_image_id'] );
		if ( $featured_image_id ) {
			set_post_thumbnail( $post_id, $featured_image_id );
		} else {
			delete_post_thumbnail( $post_id );
		}
	}
}
add_action( 'save_post_rental_unit', 'multirent_companion_save_unit_details' );

function multirent_companion_default_settings() {
	return array(
		'property_name'       => 'Your Rental Property',
		'hero_title'          => 'Flexible stays for every guest',
		'hero_text'           => 'Showcase apartments, rooms, villas, or holiday homes with clear details and easy inquiry paths.',
		'hero_image'          => '',
		'hero_button_text'    => 'View rentals',
		'hero_button_url'     => '#rentals',
		'intro_title'         => 'Manage every unit from WordPress',
		'intro_text'          => 'Add rental units, amenities, photos, capacity, booking links, and guest-facing details from the WordPress dashboard.',
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
		'show_apartments_page' => '1',
		'show_contact_page'   => '1',
		'show_local_page'     => '1',
		'contact_page_title'  => 'Contact and booking inquiry',
		'contact_page_intro'  => 'Send your dates, guest count, and preferred rental unit so the owner can reply with availability.',
		'contact_address'     => "Your property name\nStreet and house number\nCity, country",
		'contact_phone'       => '',
		'contact_mobile'      => '',
		'contact_email'       => '',
		'contact_form_shortcode' => '',
		'contact_map_query'   => '',
		'contact_map_note'    => 'Add arrival notes, parking instructions, or map corrections here.',
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
	add_menu_page( esc_html__( 'MultiRent Setup', 'multirent-companion' ), esc_html__( 'MultiRent Setup', 'multirent-companion' ), 'manage_options', 'multirent-setup', 'multirent_companion_render_setup_page', 'dashicons-admin-generic', 58 );
	add_submenu_page( 'multirent-setup', esc_html__( 'Website Setup', 'multirent-companion' ), esc_html__( 'Website Setup', 'multirent-companion' ), 'manage_options', 'multirent-setup', 'multirent_companion_render_setup_page' );
	add_submenu_page( 'multirent-setup', esc_html__( 'Apartments Page', 'multirent-companion' ), esc_html__( 'Apartments Page', 'multirent-companion' ), 'manage_options', 'multirent-apartments-page', 'multirent_companion_render_apartments_page' );
	add_submenu_page( 'multirent-setup', esc_html__( 'Contact Page', 'multirent-companion' ), esc_html__( 'Contact Page', 'multirent-companion' ), 'manage_options', 'multirent-contact-page', 'multirent_companion_render_contact_page' );
	add_submenu_page( 'multirent-setup', esc_html__( 'Local Page', 'multirent-companion' ), esc_html__( 'Local Page', 'multirent-companion' ), 'manage_options', 'multirent-local-page', 'multirent_companion_render_local_page' );
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
	$other_items   = array();

	foreach ( $submenu['multirent-setup'] as $submenu_item ) {
		$submenu_slug = isset( $submenu_item[2] ) ? $submenu_item[2] : '';

		if ( 'multirent-setup' === $submenu_slug ) {
			$submenu_item[0] = esc_html__( 'Website Setup', 'multirent-companion' );
			$website_items[] = $submenu_item;
		} elseif ( 'edit.php?post_type=rental_unit' === $submenu_slug ) {
			$submenu_item[0] = esc_html__( 'Rental Units', 'multirent-companion' );
			$rental_items[] = $submenu_item;
		} elseif ( in_array( $submenu_slug, array( 'multirent-apartments-page', 'multirent-contact-page', 'multirent-local-page', 'multirent-readme' ), true ) ) {
			$page_items[] = $submenu_item;
		} else {
			$other_items[] = $submenu_item;
		}
	}

	$submenu['multirent-setup'] = array_values( array_merge( $website_items, $rental_items, $page_items, $other_items ) );
}
add_action( 'admin_menu', 'multirent_companion_order_admin_submenus', 99 );

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
			array( 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data', 'wp-i18n' ),
			MULTIRENT_COMPANION_VERSION,
			true
		);
		wp_localize_script(
			'multirent-companion-unit-sidebar',
			'MultiRentUnitSidebar',
			array(
				'fields'       => multirent_companion_unit_sidebar_fields(),
				'panelTitle'   => esc_html__( 'Apartment Details', 'multirent-companion' ),
				'imageHelp'    => esc_html__( 'Use the Set featured image button above for the apartment tile image.', 'multirent-companion' ),
				'publishHelp'  => esc_html__( 'After filling these fields, click Publish or Update.', 'multirent-companion' ),
			)
		);
	}

	wp_localize_script(
		'multirent-companion-admin',
		'MultiRentAdmin',
		array(
			'chooseImage' => esc_html__( 'Choose image', 'multirent-companion' ),
			'useImage'    => esc_html__( 'Use this image', 'multirent-companion' ),
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
		if ( in_array( $key, array( 'show_front_page_rentals', 'show_reviews', 'show_seo_note', 'show_migration_note', 'show_apartments_page', 'show_contact_page', 'show_local_page', 'show_contact_details', 'show_booking_help', 'show_contact_map', 'show_contact_content', 'show_contact_form', 'show_contact_map_note', 'show_local_guides', 'show_local_highlights', 'show_local_activities', 'show_local_links', 'show_local_content', 'use_custom_colors' ), true ) ) {
			$output[ $key ] = ! empty( $input[ $key ] ) ? '1' : '0';
		} elseif ( 'front_page_rental_count' === $key ) {
			$output[ $key ] = (string) min( 50, max( 1, absint( $value ) ) );
		} elseif ( 'hero_image' === $key ) {
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

	if ( 'save_settings' === $action ) {
		$settings = isset( $_POST['multirent_settings'] ) && is_array( $_POST['multirent_settings'] ) ? multirent_companion_sanitize_settings( $_POST['multirent_settings'], $scope ) : multirent_companion_settings();
		update_option( 'multirent_settings', $settings );
		multirent_companion_sync_optional_page_visibility( $settings );
		add_settings_error( 'multirent_messages', 'settings_saved', esc_html__( 'MultiRent settings saved.', 'multirent-companion' ), 'updated' );
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

}
add_action( 'admin_init', 'multirent_companion_handle_setup_actions' );

function multirent_companion_optional_pages() {
	return array(
		'Apartments' => 'show_apartments_page',
		'Contact'    => 'show_contact_page',
		'Local'      => 'show_local_page',
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

function multirent_companion_sync_optional_page_visibility( $settings = null ) {
	$settings = is_array( $settings ) ? $settings : multirent_companion_settings();

	foreach ( multirent_companion_optional_pages() as $page_title => $setting_key ) {
		$page = multirent_companion_get_page_by_title( $page_title );
		if ( ! $page ) {
			continue;
		}

		wp_update_post(
			array(
				'ID'          => $page->ID,
				'post_status' => '1' === (string) $settings[ $setting_key ] ? 'publish' : 'draft',
			)
		);
	}
}

function multirent_companion_create_units( $count ) {
	for ( $index = 1; $index <= $count; $index++ ) {
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'rental_unit',
				'post_status'  => 'publish',
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
			update_post_meta( $post_id, '_distance', 'Add location note' );
			update_post_meta( $post_id, '_price_note', 'On request' );
		}
	}
}

function multirent_companion_create_starter_site() {
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
			$page_id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $title, 'post_content' => $page_data['content'] ) );
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

	foreach ( array( 'Wi-Fi', 'Parking', 'Air conditioning', 'Kitchen', 'Sea view', 'Family friendly' ) as $term ) {
		if ( ! term_exists( $term, 'rental_amenity' ) ) {
			wp_insert_term( $term, 'rental_amenity' );
		}
	}

	multirent_companion_sync_optional_page_visibility( multirent_companion_settings() );
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
	if ( '1' !== (string) $settings['show_apartments_page'] ) {
		$hidden_paths[] = '/apartments/';
	}
	if ( '1' !== (string) $settings['show_contact_page'] ) {
		$hidden_paths[] = '/contact/';
	}
	if ( '1' !== (string) $settings['show_local_page'] ) {
		$hidden_paths[] = '/local/';
	}

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
			<input type="hidden" name="multirent_settings_scope" value="property_name,hero_title,hero_text,hero_image,hero_button_text,hero_button_url,intro_title,intro_text,stats_lines,show_front_page_rentals,front_page_rental_count,reviews_shortcode,show_reviews,show_seo_note,show_migration_note,contact_title,contact_text,contact_button_text,contact_button_url,menu_items,color_scheme,use_custom_colors,color_primary,color_dark,color_surface,color_accent">
			<h2><?php esc_html_e( 'Homepage and Brand', 'multirent-companion' ); ?></h2>
			<table class="form-table" role="presentation">
				<?php foreach ( array( 'property_name', 'hero_title', 'hero_text', 'hero_button_text', 'hero_button_url', 'intro_title', 'intro_text', 'stats_lines', 'reviews_shortcode', 'contact_title', 'contact_text', 'contact_button_text', 'contact_button_url' ) as $key ) : ?>
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
					<th scope="row"><?php esc_html_e( 'Hero image', 'multirent-companion' ); ?></th>
					<td>
						<?php multirent_companion_media_field( 'multirent_settings[hero_image]', absint( $settings['hero_image'] ), __( 'Choose hero image', 'multirent-companion' ), __( 'Remove hero image', 'multirent-companion' ) ); ?>
						<p class="description"><?php esc_html_e( 'This controls the large homepage image. Users can change it without opening theme files.', 'multirent-companion' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Homepage apartments', 'multirent-companion' ); ?></th>
					<td>
						<label><input name="multirent_settings[show_front_page_rentals]" type="checkbox" value="1" <?php checked( $settings['show_front_page_rentals'], '1' ); ?>> <?php esc_html_e( 'Show apartment cards on the homepage.', 'multirent-companion' ); ?></label>
						<?php multirent_companion_description( 'show_front_page_rentals' ); ?>
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
					<td><label><input name="multirent_settings[show_reviews]" type="checkbox" value="1" <?php checked( $settings['show_reviews'], '1' ); ?>> <?php esc_html_e( 'Show the reviews section when a reviews shortcode is entered.', 'multirent-companion' ); ?></label><p class="description"><?php esc_html_e( 'Use this only after you paste a reviews shortcode. If off, the reviews area stays hidden even when a shortcode is saved.', 'multirent-companion' ); ?></p></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'SEO reminder', 'multirent-companion' ); ?></th>
					<td><label><input name="multirent_settings[show_seo_note]" type="checkbox" value="1" <?php checked( $settings['show_seo_note'], '1' ); ?>> <?php esc_html_e( 'Show a private admin-only reminder to configure SEO metadata.', 'multirent-companion' ); ?></label><p class="description"><?php esc_html_e( 'This reminder is visible only to logged-in admins, not public visitors.', 'multirent-companion' ); ?></p></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Backup reminder', 'multirent-companion' ); ?></th>
					<td><label><input name="multirent_settings[show_migration_note]" type="checkbox" value="1" <?php checked( $settings['show_migration_note'], '1' ); ?>> <?php esc_html_e( 'Show a private admin-only reminder to create backups before major changes.', 'multirent-companion' ); ?></label><p class="description"><?php esc_html_e( 'This does not install a backup plugin; it only reminds admins to make backups.', 'multirent-companion' ); ?></p></td>
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
	$settings = multirent_companion_settings();
	settings_errors( 'multirent_messages' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Apartments Page', 'multirent-companion' ); ?></h1>
		<p><?php esc_html_e( 'Manage whether the Apartments page is visible and choose the apartment listing template from the normal WordPress page editor.', 'multirent-companion' ); ?></p>
		<form method="post" action="">
			<?php wp_nonce_field( 'multirent_setup_action', 'multirent_setup_nonce' ); ?>
			<input type="hidden" name="multirent_action" value="save_settings">
			<input type="hidden" name="multirent_settings_scope" value="show_apartments_page">
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Show Apartments page', 'multirent-companion' ); ?></th>
					<td><label><input name="multirent_settings[show_apartments_page]" type="checkbox" value="1" <?php checked( $settings['show_apartments_page'], '1' ); ?>> <?php esc_html_e( 'Publish the Apartments page and allow it in the generated top menu.', 'multirent-companion' ); ?></label><?php multirent_companion_description( 'show_apartments_page' ); ?></td>
				</tr>
			</table>
			<h2><?php esc_html_e( 'Available templates', 'multirent-companion' ); ?></h2>
			<ul class="ul-disc">
				<li><?php esc_html_e( 'Apartments - Grid: simple apartment card grid.', 'multirent-companion' ); ?></li>
				<li><?php esc_html_e( 'Apartments - Featured Guide: intro text plus apartment cards.', 'multirent-companion' ); ?></li>
				<li><?php esc_html_e( 'Apartments - Compact List: tighter apartment comparison list.', 'multirent-companion' ); ?></li>
			</ul>
			<p><?php esc_html_e( 'To switch template: open Pages > All Pages > Apartments, then choose the template in the page settings sidebar.', 'multirent-companion' ); ?></p>
			<?php submit_button( esc_html__( 'Save Apartments Page Settings', 'multirent-companion' ) ); ?>
		</form>
	</div>
	<?php
}

function multirent_companion_render_contact_page() {
	$settings = multirent_companion_settings();
	settings_errors( 'multirent_messages' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Contact Page', 'multirent-companion' ); ?></h1>
		<p><?php esc_html_e( 'Manage the optional Contact / Booking Inquiry page and decide which sections should appear.', 'multirent-companion' ); ?></p>
		<form method="post" action="">
			<?php wp_nonce_field( 'multirent_setup_action', 'multirent_setup_nonce' ); ?>
			<input type="hidden" name="multirent_action" value="save_settings">
			<input type="hidden" name="multirent_settings_scope" value="show_contact_page,contact_page_title,contact_page_intro,contact_address,contact_phone,contact_mobile,contact_email,contact_form_shortcode,contact_map_query,contact_map_note,booking_help_lines,show_contact_details,show_booking_help,show_contact_map,show_contact_content,show_contact_form,show_contact_map_note">
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Show Contact page', 'multirent-companion' ); ?></th>
					<td><label><input name="multirent_settings[show_contact_page]" type="checkbox" value="1" <?php checked( $settings['show_contact_page'], '1' ); ?>> <?php esc_html_e( 'Publish the Contact page and allow it in the generated top menu.', 'multirent-companion' ); ?></label><?php multirent_companion_description( 'show_contact_page' ); ?></td>
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
				<?php foreach ( array( 'show_contact_details' => 'Show contact details card', 'show_booking_help' => 'Show booking inquiry checklist', 'show_contact_map' => 'Show map iframe', 'show_contact_content' => 'Show page editor content', 'show_contact_form' => 'Show form shortcode area', 'show_contact_map_note' => 'Show map or arrival note' ) as $key => $label ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( $label ); ?></th>
						<td><label><input name="multirent_settings[<?php echo esc_attr( $key ); ?>]" type="checkbox" value="1" <?php checked( $settings[ $key ], '1' ); ?>> <?php esc_html_e( 'Enabled', 'multirent-companion' ); ?></label><p class="description"><?php esc_html_e( 'Turn this section on or off on the Contact page.', 'multirent-companion' ); ?></p></td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button( esc_html__( 'Save Contact Page Settings', 'multirent-companion' ) ); ?>
		</form>
	</div>
	<?php
}

function multirent_companion_render_local_page() {
	$settings = multirent_companion_settings();
	settings_errors( 'multirent_messages' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Local Page', 'multirent-companion' ); ?></h1>
		<p><?php esc_html_e( 'Manage the optional Local Information page and decide which travel-guide sections should appear.', 'multirent-companion' ); ?></p>
		<form method="post" action="">
			<?php wp_nonce_field( 'multirent_setup_action', 'multirent_setup_nonce' ); ?>
			<input type="hidden" name="multirent_action" value="save_settings">
			<input type="hidden" name="multirent_settings_scope" value="show_local_page,local_page_title,local_page_intro,local_guide_lines,local_highlight_lines,local_activity_lines,local_link_lines,show_local_guides,show_local_highlights,show_local_activities,show_local_links,show_local_content">
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Show Local page', 'multirent-companion' ); ?></th>
					<td><label><input name="multirent_settings[show_local_page]" type="checkbox" value="1" <?php checked( $settings['show_local_page'], '1' ); ?>> <?php esc_html_e( 'Publish the Local page and allow it in the generated top menu.', 'multirent-companion' ); ?></label><?php multirent_companion_description( 'show_local_page' ); ?></td>
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
				<?php foreach ( array( 'show_local_guides' => 'Show guide cards', 'show_local_highlights' => 'Show local highlights', 'show_local_activities' => 'Show trips and activities', 'show_local_links' => 'Show useful links sidebar', 'show_local_content' => 'Show page editor content' ) as $key => $label ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( $label ); ?></th>
						<td><label><input name="multirent_settings[<?php echo esc_attr( $key ); ?>]" type="checkbox" value="1" <?php checked( $settings[ $key ], '1' ); ?>> <?php esc_html_e( 'Enabled', 'multirent-companion' ); ?></label><p class="description"><?php esc_html_e( 'Turn this section on or off on the Local page.', 'multirent-companion' ); ?></p></td>
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
