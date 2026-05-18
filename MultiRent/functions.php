<?php
/**
 * Multi Apartment Rental theme bootstrap.
 *
 * @package MultiRent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MULTIRENT_VERSION', '0.1.37' );
define( 'MULTIRENT_DIR', get_template_directory() );
define( 'MULTIRENT_URI', get_template_directory_uri() );

function multirent_setup() {
	load_theme_textdomain( 'multirent', MULTIRENT_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'custom-logo', array( 'height' => 90, 'width' => 260, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );

	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary menu', 'multirent' ),
			'footer'  => esc_html__( 'Footer menu', 'multirent' ),
		)
	);
}
add_action( 'after_setup_theme', 'multirent_setup' );

function multirent_enqueue_assets() {
	wp_enqueue_style( 'multirent-theme', MULTIRENT_URI . '/assets/css/theme.css', array(), MULTIRENT_VERSION );
	wp_add_inline_style( 'multirent-theme', multirent_custom_color_css() );
	wp_enqueue_script( 'multirent-navigation', MULTIRENT_URI . '/assets/js/navigation.js', array(), MULTIRENT_VERSION, true );
	wp_enqueue_script( 'multirent-gallery-lightbox', MULTIRENT_URI . '/assets/js/gallery-lightbox.js', array(), MULTIRENT_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'multirent_enqueue_assets' );

function multirent_default_menu() {
	?>
	<ul id="primary-menu" class="menu">
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'multirent' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/rentals/' ) ); ?>"><?php esc_html_e( 'Rentals', 'multirent' ); ?></a></li>
		<li><a href="<?php echo esc_url( multirent_display_option( 'contact_button_url', '#contact' ) ); ?>"><?php esc_html_e( 'Contact', 'multirent' ); ?></a></li>
	</ul>
	<?php
}

function multirent_menu_url( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return home_url( '/' );
	}

	if ( str_starts_with( $url, '#' ) || preg_match( '#^https?://#i', $url ) || str_starts_with( $url, 'mailto:' ) || str_starts_with( $url, 'tel:' ) ) {
		return $url;
	}

	return home_url( '/' . ltrim( $url, '/' ) );
}

function multirent_menu_items() {
	$menu_items_text = multirent_display_option( 'menu_items', '' );
	$items           = array();
	$lines           = preg_split( '/\r\n|\r|\n/', (string) $menu_items_text );
	$hidden_paths    = multirent_hidden_menu_paths();

	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( '' === $line || ! str_contains( $line, '|' ) ) {
			continue;
		}

		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		if ( '' === $parts[0] ) {
			continue;
		}

		$url = multirent_menu_url( $parts[1] );
		if ( multirent_is_hidden_menu_url( $url, $hidden_paths ) ) {
			continue;
		}

		$items[] = array(
			'label' => sanitize_text_field( $parts[0] ),
			'url'   => $url,
		);
	}

	return $items;
}

function multirent_hidden_menu_paths() {
	$settings = multirent_plugin_settings();
	$roles    = array(
		array( 'show_key' => 'show_apartments_page', 'page_id_key' => 'apartments_page_id', 'path' => '/apartments/' ),
		array( 'show_key' => 'show_contact_page', 'page_id_key' => 'contact_page_id', 'path' => '/contact/' ),
		array( 'show_key' => 'show_local_page', 'page_id_key' => 'local_page_id', 'path' => '/local/' ),
	);

	$hidden_paths = array();
	foreach ( $roles as $role ) {
		if ( isset( $settings[ $role['show_key'] ] ) && '1' === (string) $settings[ $role['show_key'] ] ) {
			continue;
		}

		$hidden_paths[] = $role['path'];
		$page_id        = isset( $settings[ $role['page_id_key'] ] ) ? absint( $settings[ $role['page_id_key'] ] ) : 0;
		if ( $page_id ) {
			$path = wp_parse_url( get_permalink( $page_id ), PHP_URL_PATH );
			if ( $path ) {
				$hidden_paths[] = '/' . trim( (string) $path, '/' ) . '/';
			}
		}
	}

	return array_values( array_unique( $hidden_paths ) );
}

function multirent_is_hidden_menu_url( $url, $hidden_paths ) {
	$path = wp_parse_url( $url, PHP_URL_PATH );
	if ( ! $path ) {
		return false;
	}

	$path = '/' . trim( (string) $path, '/' ) . '/';
	return in_array( $path, $hidden_paths, true );
}

function multirent_primary_menu() {
	$items = multirent_menu_items();
	if ( $items ) {
		?>
		<ul id="primary-menu" class="menu">
			<?php foreach ( $items as $item ) : ?>
				<li><a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a></li>
			<?php endforeach; ?>
		</ul>
		<?php
		return;
	}

	wp_nav_menu(
		array(
			'theme_location' => 'primary',
			'menu_id'        => 'primary-menu',
			'container'      => false,
			'fallback_cb'    => 'multirent_default_menu',
		)
	);
}

function multirent_document_title_parts( $parts ) {
	$property_name = multirent_display_option( 'property_name', '' );
	if ( $property_name ) {
		$parts['site'] = $property_name;
	}

	$property_tagline = multirent_display_option( 'property_tagline', '' );
	if ( $property_tagline ) {
		$parts['tagline'] = $property_tagline;
	} elseif ( isset( $parts['tagline'] ) ) {
		unset( $parts['tagline'] );
	}

	return $parts;
}
add_filter( 'document_title_parts', 'multirent_document_title_parts' );

function multirent_plugin_settings() {
	$settings = get_option( 'multirent_settings', array() );
	return is_array( $settings ) ? $settings : array();
}

function multirent_display_option( $key, $default = '' ) {
	$settings = multirent_plugin_settings();
	if ( isset( $settings[ $key ] ) && '' !== $settings[ $key ] ) {
		return $settings[ $key ];
	}

	return multirent_option( $key, $default );
}

function multirent_color_schemes() {
	return array(
		'coastal' => array(
			'label'    => esc_html__( 'Coastal Blue', 'multirent' ),
			'ink'      => '#102033',
			'muted'    => '#5d7088',
			'primary'  => '#087ea4',
			'dark'     => '#06364d',
			'surface'  => '#f8fbfd',
			'accent'   => '#dff3fb',
		),
		'olive'   => array(
			'label'    => esc_html__( 'Olive Garden', 'multirent' ),
			'ink'      => '#1d2b22',
			'muted'    => '#617164',
			'primary'  => '#4f7b45',
			'dark'     => '#223d29',
			'surface'  => '#f8faf4',
			'accent'   => '#e5efd8',
		),
		'coral'   => array(
			'label'    => esc_html__( 'Coral Sunset', 'multirent' ),
			'ink'      => '#2c2630',
			'muted'    => '#766a72',
			'primary'  => '#c7524a',
			'dark'     => '#4b2830',
			'surface'  => '#fff8f5',
			'accent'   => '#ffe1d8',
		),
		'graphite' => array(
			'label'    => esc_html__( 'Graphite', 'multirent' ),
			'ink'      => '#171d24',
			'muted'    => '#5e6875',
			'primary'  => '#3d6f86',
			'dark'     => '#202a34',
			'surface'  => '#f6f7f8',
			'accent'   => '#dce6eb',
		),
	);
}

function multirent_color_tokens() {
	$scheme_key = multirent_display_option( 'color_scheme', 'coastal' );
	$schemes    = multirent_color_schemes();
	$tokens     = isset( $schemes[ $scheme_key ] ) ? $schemes[ $scheme_key ] : $schemes['coastal'];

	$use_custom_colors = '1' === (string) multirent_display_option( 'use_custom_colors', '0' );
	if ( $use_custom_colors ) {
		foreach ( array( 'ink', 'muted', 'primary', 'dark', 'surface', 'accent' ) as $key ) {
			$custom = multirent_display_option( 'color_' . $key, '' );
			if ( $custom && sanitize_hex_color( $custom ) ) {
				$tokens[ $key ] = sanitize_hex_color( $custom );
			}
		}
	}

	return $tokens;
}

function multirent_custom_color_css() {
	$tokens = multirent_color_tokens();
	return sprintf(
		':root{--ink:%1$s;--muted:%2$s;--sea:%3$s;--sea-dark:%4$s;--paper:%5$s;--sky:%6$s;}',
		esc_html( $tokens['ink'] ),
		esc_html( $tokens['muted'] ),
		esc_html( $tokens['primary'] ),
		esc_html( $tokens['dark'] ),
		esc_html( $tokens['surface'] ),
		esc_html( $tokens['accent'] )
	);
}

function multirent_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'multirent_home',
		array(
			'title'       => esc_html__( 'Multi Apartment Rental Setup', 'multirent' ),
			'description' => esc_html__( 'Configure the homepage, property details, reviews widget, and contact call to action without editing code.', 'multirent' ),
			'priority'    => 30,
		)
	);

	$settings = array(
		'property_name'       => array( 'label' => esc_html__( 'Property name', 'multirent' ), 'default' => esc_html__( 'Your Rental Property', 'multirent' ), 'type' => 'text' ),
		'property_tagline'    => array( 'label' => esc_html__( 'Property tagline', 'multirent' ), 'default' => '', 'type' => 'text' ),
		'hero_title'          => array( 'label' => esc_html__( 'Hero title', 'multirent' ), 'default' => esc_html__( 'Flexible stays for every guest', 'multirent' ), 'type' => 'text' ),
		'hero_text'           => array( 'label' => esc_html__( 'Hero text', 'multirent' ), 'default' => esc_html__( 'Showcase apartments, rooms, villas, or holiday homes with clear details and easy inquiry paths.', 'multirent' ), 'type' => 'textarea' ),
		'hero_button_text'    => array( 'label' => esc_html__( 'Hero button text', 'multirent' ), 'default' => esc_html__( 'View rentals', 'multirent' ), 'type' => 'text' ),
		'hero_button_url'     => array( 'label' => esc_html__( 'Hero button URL', 'multirent' ), 'default' => '#rentals', 'type' => 'url' ),
		'intro_eyebrow'       => array( 'label' => esc_html__( 'Intro eyebrow', 'multirent' ), 'default' => esc_html__( 'About the property', 'multirent' ), 'type' => 'text' ),
		'intro_title'         => array( 'label' => esc_html__( 'Intro title', 'multirent' ), 'default' => esc_html__( 'Manage every unit from WordPress', 'multirent' ), 'type' => 'text' ),
		'intro_text'          => array( 'label' => esc_html__( 'Intro text', 'multirent' ), 'default' => esc_html__( 'Add rental units, amenities, photos, capacity, booking links, and guest-facing details from the WordPress dashboard.', 'multirent' ), 'type' => 'textarea' ),
		'stats_lines'         => array( 'label' => esc_html__( 'Homepage stats, one per line: value | label', 'multirent' ), 'default' => "4 | Rental units\n100 m | Example distance\n24/7 | Self-managed content", 'type' => 'textarea' ),
		'reviews_shortcode'   => array( 'label' => esc_html__( 'Reviews shortcode', 'multirent' ), 'default' => '', 'type' => 'text' ),
		'contact_title'       => array( 'label' => esc_html__( 'Contact title', 'multirent' ), 'default' => esc_html__( 'Ready to receive inquiries?', 'multirent' ), 'type' => 'text' ),
		'contact_text'        => array( 'label' => esc_html__( 'Contact text', 'multirent' ), 'default' => esc_html__( 'Connect this section to your contact page, booking form, or external reservation system.', 'multirent' ), 'type' => 'textarea' ),
		'contact_button_text' => array( 'label' => esc_html__( 'Contact button text', 'multirent' ), 'default' => esc_html__( 'Contact us', 'multirent' ), 'type' => 'text' ),
		'contact_button_url'  => array( 'label' => esc_html__( 'Contact button URL', 'multirent' ), 'default' => '#contact', 'type' => 'url' ),
		'color_scheme'        => array( 'label' => esc_html__( 'Color scheme', 'multirent' ), 'default' => 'coastal', 'type' => 'select' ),
		'use_custom_colors'   => array( 'label' => esc_html__( 'Use custom colors', 'multirent' ), 'default' => '0', 'type' => 'checkbox' ),
	);

	foreach ( $settings as $key => $config ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $config['default'],
				'sanitize_callback' => 'url' === $config['type'] ? 'esc_url_raw' : 'sanitize_textarea_field',
			)
		);
		$control_args = array(
			'label'   => $config['label'],
			'section' => 'multirent_home',
			'type'    => $config['type'],
		);

		if ( 'color_scheme' === $key ) {
			$control_args['choices'] = wp_list_pluck( multirent_color_schemes(), 'label' );
		}

		$wp_customize->add_control( $key, $control_args );
	}

	foreach ( array( 'primary', 'dark', 'surface', 'accent' ) as $color_key ) {
		$wp_customize->add_setting(
			'color_' . $color_key,
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_' . $color_key, array( 'label' => sprintf( esc_html__( 'Custom %s color', 'multirent' ), $color_key ), 'section' => 'multirent_home' ) ) );
	}

	$wp_customize->add_setting( 'hero_image', array( 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'hero_image', array( 'label' => esc_html__( 'Hero image', 'multirent' ), 'section' => 'multirent_home', 'mime_type' => 'image' ) ) );
	$wp_customize->add_setting( 'page_logo', array( 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'page_logo', array( 'label' => esc_html__( 'Page logo', 'multirent' ), 'section' => 'multirent_home', 'mime_type' => 'image' ) ) );
}
add_action( 'customize_register', 'multirent_customize_register' );

function multirent_option( $key, $default = '' ) {
	$value = get_theme_mod( $key, $default );
	return is_string( $value ) ? $value : $default;
}

function multirent_stats() {
	$lines = preg_split( '/\r\n|\r|\n/', multirent_display_option( 'stats_lines', "4 | Rental units\n100 m | Example distance\n24/7 | Self-managed content" ) );
	$stats = array();
	foreach ( $lines as $line ) {
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		if ( 2 === count( $parts ) && '' !== $parts[0] && '' !== $parts[1] ) {
			$stats[] = array( 'value' => $parts[0], 'label' => $parts[1] );
		}
	}
	return $stats;
}

function multirent_lines_to_items( $text ) {
	$items = array();
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( '' !== $line ) {
			$items[] = $line;
		}
	}

	return $items;
}

function multirent_lines_to_cards( $text ) {
	$cards = array();
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	foreach ( $lines as $line ) {
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		if ( 2 === count( $parts ) && '' !== $parts[0] && '' !== $parts[1] ) {
			$cards[] = array( 'title' => $parts[0], 'text' => $parts[1] );
		}
	}

	return $cards;
}

function multirent_lines_to_links( $text ) {
	$links = array();
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	foreach ( $lines as $line ) {
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		if ( 2 === count( $parts ) && '' !== $parts[0] && '' !== $parts[1] ) {
			$links[] = array( 'label' => $parts[0], 'url' => $parts[1] );
		}
	}

	return $links;
}

function multirent_unit_detail( $post_id, $key ) {
	return get_post_meta( $post_id, '_' . $key, true );
}

function multirent_unit_detail_labels() {
	return array(
		'capacity'   => esc_html__( 'Guests', 'multirent' ),
		'bedrooms'   => esc_html__( 'Bedrooms', 'multirent' ),
		'bathrooms'  => esc_html__( 'Bathrooms', 'multirent' ),
		'size'       => esc_html__( 'Size', 'multirent' ),
		'price_note' => esc_html__( 'Price', 'multirent' ),
	);
}

function multirent_render_unit_details( $post_id, $compact = false ) {
	$items = array();
	foreach ( multirent_unit_detail_labels() as $key => $label ) {
		$value = multirent_unit_detail( $post_id, $key );
		if ( $value ) {
			$items[ $key ] = array( 'label' => $label, 'value' => $value );
		}
	}

	if ( ! $items ) {
		return;
	}

	if ( $compact ) {
		$items = ! empty( $items['capacity'] ) ? array( 'capacity' => $items['capacity'] ) : array();
		if ( ! $items ) {
			return;
		}
		?>
		<ul class="detail-list">
			<?php foreach ( $items as $key => $item ) : ?>
				<li><span><?php echo esc_html( $item['label'] ); ?></span><strong><?php echo esc_html( multirent_unit_summary_value( $key, $item['value'] ) ); ?></strong></li>
			<?php endforeach; ?>
		</ul>
		<?php
		return;
	}

	$summary_keys = array( 'capacity', 'bedrooms', 'bathrooms' );
	$fact_keys    = array( 'size' );
	?>
	<div class="unit-detail-panel">
		<?php if ( ! empty( $items['price_note'] ) ) : ?>
			<div class="unit-price-callout">
				<span><?php echo esc_html( $items['price_note']['label'] ); ?></span>
				<strong><?php echo esc_html( $items['price_note']['value'] ); ?></strong>
			</div>
		<?php endif; ?>

		<div class="unit-summary-grid">
			<?php foreach ( $summary_keys as $key ) : ?>
				<?php if ( ! empty( $items[ $key ] ) ) : ?>
					<div class="unit-summary-item">
						<span><?php echo esc_html( $items[ $key ]['label'] ); ?></span>
						<strong><?php echo esc_html( multirent_unit_summary_value( $key, $items[ $key ]['value'] ) ); ?></strong>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>

		<ul class="detail-list detail-list-secondary">
			<?php foreach ( $fact_keys as $key ) : ?>
				<?php if ( ! empty( $items[ $key ] ) ) : ?>
					<li><span><?php echo esc_html( $items[ $key ]['label'] ); ?></span><strong><?php echo esc_html( $items[ $key ]['value'] ); ?></strong></li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
}

function multirent_unit_summary_value( $key, $value ) {
	$clean_value = trim( (string) $value );

	if ( 'capacity' === $key ) {
		$clean_value = str_replace( array( html_entity_decode( '&ndash;', ENT_QUOTES, 'UTF-8' ), html_entity_decode( '&mdash;', ENT_QUOTES, 'UTF-8' ) ), '-', $clean_value );
	}

	if ( 'capacity' === $key && preg_match( '/\d+\s*(?:-\s*\d+)?/', $clean_value, $matches ) ) {
		return preg_replace( '/\s+/', '', $matches[0] );
	}

	if ( in_array( $key, array( 'capacity', 'bedrooms', 'bathrooms' ), true ) ) {
		$clean_value = preg_replace( '/\s+(guests?|bedrooms?|bathrooms?)\b/i', '', $clean_value );
	}

	return trim( $clean_value );
}

function multirent_amenity_icons() {
	return array(
		'parking'          => '&#x1F17F;&#xFE0F;',
		'wifi'             => '&#x1F4F6;',
		'wi-fi'            => '&#x1F4F6;',
		'balcony'          => '&#x1FA91;',
		'bathroom'         => '&#x1F6BF;',
		'air-condition'    => '&#x2744;&#xFE0F;',
		'air-conditioning' => '&#x2744;&#xFE0F;',
		'tv'               => '&#x1F4FA;',
		'sat-tv'           => '&#x1F4E1;',
		'bbq'              => '&#x1F525;',
		'terrace'          => '&#x2600;&#xFE0F;',
		'terace'           => '&#x2600;&#xFE0F;',
		'no-smoking'       => '&#x1F6AD;',
		'kitchen'          => '&#x1F373;',
		'separate-entrance' => '&#x1F6AA;',
		'dishwasher'       => '&#x1F9FC;',
		'coffee-machine'   => '&#x2615;',
		'microwave'        => '&#x1F4A1;',
		'washing-machine'  => '&#x1F9FA;',
		'iron'             => '&#x1F9FA;',
		'hair-dryer'       => '&#x1F4A8;',
		'baby-cot'         => '&#x1F6CF;&#xFE0F;',
		'ev-charger'       => '&#x1F50C;',
		'pets-allowed'     => '&#x1F43E;',
		'pets-not-allowed' => '&#x1F6AB;',
	);
}

function multirent_render_unit_amenities( $post_id, $compact = false ) {
	if ( ! taxonomy_exists( 'rental_amenity' ) ) {
		return;
	}

	$terms = get_the_terms( $post_id, 'rental_amenity' );
	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return;
	}

	$icons = multirent_amenity_icons();
	$terms = $compact ? array_slice( $terms, 0, 6 ) : $terms;
	?>
	<ul class="amenity-list<?php echo $compact ? ' amenity-list-compact' : ''; ?>" aria-label="<?php esc_attr_e( 'Amenities', 'multirent' ); ?>">
		<?php foreach ( $terms as $term ) : ?>
			<?php $icon = isset( $icons[ $term->slug ] ) ? $icons[ $term->slug ] : '&#x2713;'; ?>
			<li class="amenity-chip amenity-<?php echo esc_attr( $term->slug ); ?>">
				<span class="amenity-icon" aria-hidden="true"><?php echo wp_kses_post( $icon ); ?></span>
				<span><?php echo esc_html( $term->name ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
}

function multirent_unit_gallery_images( $post_id ) {
	$thumbnail_id = get_post_thumbnail_id( $post_id );
	$qr_image_id  = absint( get_post_meta( $post_id, '_qr_code_image_id', true ) );
	$stored_ids    = get_post_meta( $post_id, '_gallery_image_ids', true );
	if ( $stored_ids ) {
		$image_ids = array_filter( array_map( 'absint', explode( ',', $stored_ids ) ) );
		$image_ids = array_values(
			array_filter(
				array_unique( $image_ids ),
				function( $image_id ) use ( $thumbnail_id, $qr_image_id ) {
					return $image_id && (int) $image_id !== (int) $thumbnail_id && (int) $image_id !== $qr_image_id && wp_attachment_is_image( $image_id );
				}
			)
		);

		if ( $image_ids ) {
			return $image_ids;
		}
	}

	$attachments = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'post_parent'    => $post_id,
			'post_status'    => 'inherit',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order ID',
			'order'          => 'ASC',
		)
	);

	$image_ids = array();
	foreach ( $attachments as $attachment ) {
		if ( (int) $attachment->ID === (int) $thumbnail_id || (int) $attachment->ID === $qr_image_id ) {
			continue;
		}

		$image_ids[] = (int) $attachment->ID;
	}

	return $image_ids;
}

function multirent_youtube_video_id( $url ) {
	$parts = wp_parse_url( trim( (string) $url ) );
	if ( empty( $parts['host'] ) ) {
		return '';
	}

	$host = strtolower( preg_replace( '/^www\./', '', $parts['host'] ) );
	$path = isset( $parts['path'] ) ? trim( $parts['path'], '/' ) : '';

	if ( 'youtu.be' === $host && $path ) {
		$segments = explode( '/', $path );
		return preg_match( '/^[A-Za-z0-9_-]{6,}$/', $segments[0] ) ? $segments[0] : '';
	}

	if ( ! in_array( $host, array( 'youtube.com', 'm.youtube.com', 'music.youtube.com', 'youtube-nocookie.com' ), true ) ) {
		return '';
	}

	if ( ! empty( $parts['query'] ) ) {
		parse_str( $parts['query'], $query );
		if ( ! empty( $query['v'] ) && preg_match( '/^[A-Za-z0-9_-]{6,}$/', $query['v'] ) ) {
			return $query['v'];
		}
	}

	$segments = $path ? explode( '/', $path ) : array();
	foreach ( array( 'embed', 'shorts', 'live' ) as $prefix ) {
		$index = array_search( $prefix, $segments, true );
		if ( false !== $index && ! empty( $segments[ $index + 1 ] ) && preg_match( '/^[A-Za-z0-9_-]{6,}$/', $segments[ $index + 1 ] ) ) {
			return $segments[ $index + 1 ];
		}
	}

	return '';
}

function multirent_youtube_embed_url( $url ) {
	$video_id = multirent_youtube_video_id( $url );
	return $video_id ? 'https://www.youtube-nocookie.com/embed/' . rawurlencode( $video_id ) . '?rel=0' : '';
}

function multirent_youtube_thumbnail_url( $url ) {
	$video_id = multirent_youtube_video_id( $url );
	return $video_id ? 'https://img.youtube.com/vi/' . rawurlencode( $video_id ) . '/hqdefault.jpg' : '';
}

function multirent_render_unit_gallery( $post_id ) {
	$image_ids = multirent_unit_gallery_images( $post_id );
	$video_url = multirent_unit_detail( $post_id, 'video_url' );
	$video_embed_url = multirent_youtube_embed_url( $video_url );
	$video_thumbnail_url = multirent_youtube_thumbnail_url( $video_url );

	if ( ! $image_ids && ! $video_embed_url ) {
		return;
	}
	?>
	<aside class="unit-gallery-card" aria-label="<?php esc_attr_e( 'Apartment gallery', 'multirent' ); ?>">
		<p class="eyebrow"><?php esc_html_e( 'Gallery', 'multirent' ); ?></p>
		<div class="unit-gallery-grid">
			<?php foreach ( $image_ids as $image_id ) : ?>
				<a href="<?php echo esc_url( wp_get_attachment_url( $image_id ) ); ?>" class="unit-gallery-link" data-gallery-type="image">
					<?php echo wp_get_attachment_image( $image_id, 'medium_large', false, array( 'class' => 'unit-gallery-image' ) ); ?>
				</a>
			<?php endforeach; ?>
			<?php if ( $video_embed_url ) : ?>
				<a href="<?php echo esc_url( $video_url ); ?>" class="unit-gallery-link unit-gallery-video-link" data-gallery-type="video" data-video-src="<?php echo esc_url( $video_embed_url ); ?>" aria-label="<?php esc_attr_e( 'Play apartment video', 'multirent' ); ?>">
					<img src="<?php echo esc_url( $video_thumbnail_url ); ?>" class="unit-gallery-image" alt="<?php esc_attr_e( 'Apartment video thumbnail', 'multirent' ); ?>">
					<span class="unit-gallery-play" aria-hidden="true"></span>
				</a>
			<?php endif; ?>
		</div>
	</aside>
	<?php
}

function multirent_map_query( $address = '', $coordinates = '' ) {
	$coordinates = trim( (string) $coordinates );
	$address     = trim( (string) $address );

	if ( $coordinates && preg_match( '/^-?\d{1,3}(?:\.\d+)?\s*,\s*-?\d{1,3}(?:\.\d+)?$/', $coordinates ) ) {
		return preg_replace( '/\s+/', '', $coordinates );
	}

	return $address;
}

function multirent_render_qr_map_tile( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'qr_image_id' => 0,
			'map_query'   => '',
			'title'       => __( 'Guest information', 'multirent' ),
			'qr_label'    => __( 'QR code', 'multirent' ),
			'map_label'   => __( 'Apartment map', 'multirent' ),
			'class'       => '',
		)
	);

	$qr_image_id = absint( $args['qr_image_id'] );
	$map_query   = trim( (string) $args['map_query'] );
	$map_title   = $args['map_label'] ? $args['map_label'] : __( 'Apartment map', 'multirent' );

	if ( ! $qr_image_id && ! $map_query ) {
		return;
	}
	?>
	<aside class="qr-map-card <?php echo esc_attr( $args['class'] ); ?>">
		<?php if ( $args['title'] ) : ?>
			<h2><?php echo esc_html( $args['title'] ); ?></h2>
		<?php endif; ?>
		<?php if ( $qr_image_id ) : ?>
			<div class="qr-map-block qr-map-block-code">
				<?php if ( $args['qr_label'] ) : ?>
					<p class="eyebrow"><?php echo esc_html( $args['qr_label'] ); ?></p>
				<?php endif; ?>
				<?php echo wp_get_attachment_image( $qr_image_id, 'medium', false, array( 'class' => 'qr-code-image' ) ); ?>
			</div>
		<?php endif; ?>
		<?php if ( $map_query ) : ?>
			<div class="qr-map-block qr-map-block-map">
				<?php if ( $args['map_label'] ) : ?>
					<p class="eyebrow"><?php echo esc_html( $args['map_label'] ); ?></p>
				<?php endif; ?>
				<iframe title="<?php echo esc_attr( $map_title ); ?>" src="<?php echo esc_url( 'https://www.google.com/maps?q=' . rawurlencode( $map_query ) . '&output=embed' ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
			</div>
		<?php endif; ?>
	</aside>
	<?php
}

function multirent_render_unit_guest_info( $post_id ) {
	$map_query = multirent_map_query( multirent_unit_detail( $post_id, 'map_address' ), multirent_unit_detail( $post_id, 'map_coordinates' ) );
	multirent_render_qr_map_tile(
		array(
			'qr_image_id' => get_post_meta( $post_id, '_qr_code_image_id', true ),
			'map_query'   => $map_query,
			'title'       => '',
			'qr_label'    => '',
			'map_label'   => '',
			'class'       => 'unit-extra-card',
		)
	);
}

function multirent_button_url( $url ) {
	return $url ? $url : '#';
}

