<?php
/**
 * Multi Apartment Rental theme bootstrap.
 *
 * @package MultiRent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MULTIRENT_VERSION', '0.2.1' );
define( 'MULTIRENT_DIR', get_template_directory() );
define( 'MULTIRENT_URI', get_template_directory_uri() );

function multirent_setup() {
	load_theme_textdomain( 'multirent', MULTIRENT_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'custom-logo', array( 'height' => 90, 'width' => 260, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption' ) );

	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary menu', 'multirent' ),
			'footer'  => esc_html__( 'Footer menu', 'multirent' ),
		)
	);
}
add_action( 'after_setup_theme', 'multirent_setup' );

function multirent_enqueue_assets() {
	wp_enqueue_style( 'multirent-fonts', MULTIRENT_URI . '/assets/fonts/fonts.css', array(), MULTIRENT_VERSION );
	wp_enqueue_style( 'multirent-theme', MULTIRENT_URI . '/assets/css/theme.css', array( 'multirent-fonts' ), MULTIRENT_VERSION );
	wp_add_inline_style( 'multirent-theme', multirent_custom_color_css() );
	wp_enqueue_script( 'multirent-navigation', MULTIRENT_URI . '/assets/js/navigation.js', array(), MULTIRENT_VERSION, true );
	wp_enqueue_script( 'multirent-gallery-lightbox', MULTIRENT_URI . '/assets/js/gallery-lightbox.js', array(), MULTIRENT_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'multirent_enqueue_assets' );

function multirent_global_font_choices() {
	return array(
		'theme-default' => array( 'label' => esc_html__( 'Theme default', 'multirent' ), 'css' => '' ),
		'playfair'      => array( 'label' => esc_html__( 'Playfair Display', 'multirent' ), 'css' => '"Playfair Display", Georgia, serif' ),
		'inter'         => array( 'label' => esc_html__( 'Inter', 'multirent' ), 'css' => '"Inter", Arial, sans-serif' ),
		'poppins'       => array( 'label' => esc_html__( 'Poppins', 'multirent' ), 'css' => '"Poppins", Arial, sans-serif' ),
		'montserrat'    => array( 'label' => esc_html__( 'Montserrat', 'multirent' ), 'css' => '"Montserrat", Arial, sans-serif' ),
		'lora'          => array( 'label' => esc_html__( 'Lora', 'multirent' ), 'css' => '"Lora", Georgia, serif' ),
		'merriweather'  => array( 'label' => esc_html__( 'Merriweather', 'multirent' ), 'css' => '"Merriweather", Georgia, serif' ),
		'raleway'       => array( 'label' => esc_html__( 'Raleway', 'multirent' ), 'css' => '"Raleway", Arial, sans-serif' ),
		'oswald'        => array( 'label' => esc_html__( 'Oswald', 'multirent' ), 'css' => '"Oswald", Arial, sans-serif' ),
		'nunito-sans'   => array( 'label' => esc_html__( 'Nunito Sans', 'multirent' ), 'css' => '"Nunito Sans", Arial, sans-serif' ),
		'source-sans-3' => array( 'label' => esc_html__( 'Source Sans 3', 'multirent' ), 'css' => '"Source Sans 3", Arial, sans-serif' ),
	);
}

function multirent_sanitize_global_font( $value ) {
	$value   = sanitize_key( $value );
	$choices = multirent_global_font_choices();
	return isset( $choices[ $value ] ) ? $value : 'theme-default';
}

function multirent_sanitize_hero_title_size( $value ) {
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

function multirent_global_font_css_vars() {
	$font    = multirent_sanitize_global_font( multirent_display_option( 'global_font', 'theme-default' ) );
	$choices = multirent_global_font_choices();
	$stack   = $choices[ $font ]['css'];
	if ( '' === $stack ) {
		return '';
	}

	return '--font-body:' . $stack . ';--font-heading:' . $stack . ';';
}

function multirent_hero_style_attr( $hero_image_url = '' ) {
	$styles = array();

	$size = multirent_sanitize_hero_title_size( multirent_display_option( 'hero_title_size', '' ) );
	if ( '' !== $size ) {
		$styles[] = '--hero-title-size:' . $size . 'cm';
	}

	if ( $hero_image_url ) {
		$styles[] = 'background-image:linear-gradient(90deg, rgba(7, 31, 54, 0.86), rgba(16, 94, 125, 0.48)), url(' . esc_url_raw( $hero_image_url ) . ')';
	}

	if ( ! $styles ) {
		return '';
	}

	return ' style="' . esc_attr( implode( ';', $styles ) ) . '"';
}

function multirent_default_menu() {
	?>
	<ul id="primary-menu" class="menu">
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'multirent' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/rentals/' ) ); ?>"><?php esc_html_e( 'Rentals', 'multirent' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'multirent' ); ?></a></li>
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
	$seen            = array();

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
		$seen[ multirent_menu_item_key( $url ) ] = true;
	}

	foreach ( multirent_slot_menu_items() as $item ) {
		if ( multirent_is_hidden_menu_url( $item['url'], $hidden_paths ) ) {
			continue;
		}

		$key = multirent_menu_item_key( $item['url'] );
		if ( isset( $seen[ $key ] ) ) {
			continue;
		}

		$items[]      = $item;
		$seen[ $key ] = true;
	}

	return $items;
}

function multirent_menu_item_key( $url ) {
	$path = wp_parse_url( $url, PHP_URL_PATH );
	if ( $path ) {
		return '/' . trim( (string) $path, '/' ) . '/';
	}

	return strtolower( trim( (string) $url ) );
}

function multirent_slot_menu_items() {
	$items = array();
	foreach ( array( 'apartment', 'contact' ) as $slot_type ) {
		foreach ( multirent_page_slots( $slot_type ) as $slot ) {
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

function multirent_hidden_menu_paths() {
	$settings = multirent_plugin_settings();
	$roles    = array(
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

	foreach ( array( 'apartment', 'contact' ) as $slot_type ) {
		foreach ( multirent_page_slots( $slot_type ) as $slot ) {
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

function multirent_normalized_url_path( $url ) {
	$path = wp_parse_url( $url, PHP_URL_PATH );
	if ( ! $path ) {
		return '/';
	}

	return '/' . trim( (string) $path, '/' ) . '/';
}

function multirent_is_current_menu_item( $item ) {
	$item_path    = multirent_normalized_url_path( $item['url'] ?? '/' );
	$current_path = multirent_normalized_url_path( home_url( add_query_arg( array(), $_SERVER['REQUEST_URI'] ?? '/' ) ) );
	$label        = isset( $item['label'] ) ? strtolower( trim( (string) $item['label'] ) ) : '';

	if ( $item_path === $current_path ) {
		return true;
	}

	if ( '/' !== $item_path && str_starts_with( $current_path, $item_path ) ) {
		return true;
	}

	if ( is_singular( 'rental_unit' ) || is_post_type_archive( 'rental_unit' ) || is_tax( 'rental_amenity' ) ) {
		return in_array( $item_path, array( '/apartments/', '/rentals/' ), true ) || 'apartments' === $label || 'rentals' === $label;
	}

	return false;
}

function multirent_primary_menu() {
	$items = multirent_menu_items();
	if ( $items ) {
		?>
		<ul id="primary-menu" class="menu">
			<?php foreach ( $items as $item ) : ?>
				<?php $is_current = multirent_is_current_menu_item( $item ); ?>
				<li class="<?php echo $is_current ? 'current-menu-item current_page_item' : ''; ?>"><a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a></li>
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

function multirent_page_slot_count() {
	return 3;
}

function multirent_page_slot( $type, $index ) {
	$settings = multirent_plugin_settings();
	$type     = 'contact' === $type ? 'contact' : 'apartment';
	$index    = min( multirent_page_slot_count(), max( 1, absint( $index ) ) );
	$prefix   = $type . '_page_' . $index;

	$legacy_page_key = 'apartment' === $type ? 'apartments_page_id' : 'contact_page_id';
	$legacy_show_key = 'apartment' === $type ? 'show_apartments_page' : 'show_contact_page';

	$page_id = isset( $settings[ $prefix . '_id' ] ) ? absint( $settings[ $prefix . '_id' ] ) : 0;
	if ( 1 === $index && ! $page_id && ! empty( $settings[ $legacy_page_key ] ) ) {
		$page_id = absint( $settings[ $legacy_page_key ] );
	}

	$enabled = isset( $settings[ $prefix . '_enabled' ] ) ? '1' === (string) $settings[ $prefix . '_enabled' ] : ( 1 === $index && '1' === (string) ( $settings[ $legacy_show_key ] ?? '1' ) );
	$show_menu = isset( $settings[ $prefix . '_show_menu' ] ) ? '1' === (string) $settings[ $prefix . '_show_menu' ] : $enabled;

	return array(
		'type'         => $type,
		'index'        => $index,
		'prefix'       => $prefix,
		'enabled'      => $enabled,
		'page_id'      => $page_id,
		'button_label' => trim( (string) ( $settings[ $prefix . '_button_label' ] ?? '' ) ),
		'show_hero'    => ! empty( $settings[ $prefix . '_show_hero' ] ) && '1' === (string) $settings[ $prefix . '_show_hero' ],
		'show_menu'    => $show_menu,
	);
}

function multirent_page_slots( $type ) {
	$slots = array();
	for ( $index = 1; $index <= multirent_page_slot_count(); $index++ ) {
		$slots[] = multirent_page_slot( $type, $index );
	}
	return $slots;
}

function multirent_current_page_slot( $type ) {
	$page_id = get_queried_object_id();
	foreach ( multirent_page_slots( $type ) as $slot ) {
		if ( $slot['page_id'] && absint( $slot['page_id'] ) === absint( $page_id ) ) {
			return $slot;
		}
	}
	return multirent_page_slot( $type, 1 );
}

function multirent_contact_display_option( $key, $default = '' ) {
	$slot = multirent_current_page_slot( 'contact' );
	$settings = multirent_plugin_settings();
	$map = array(
		'contact_page_title' => 'title',
		'contact_page_intro' => 'intro',
		'contact_address' => 'address',
		'contact_phone' => 'phone',
		'contact_mobile' => 'mobile',
		'contact_email' => 'email',
		'contact_form_shortcode' => 'form_shortcode',
		'contact_map_query' => 'map_query',
		'contact_map_note' => 'map_note',
		'contact_qr_code_image_id' => 'qr_code_image_id',
		'booking_help_lines' => 'booking_help_lines',
		'show_contact_details' => 'show_details',
		'show_booking_help' => 'show_booking_help',
		'show_contact_map' => 'show_map',
		'show_contact_content' => 'show_content',
		'show_contact_form' => 'show_form',
		'show_contact_map_note' => 'show_map_note',
	);

	if ( isset( $map[ $key ] ) ) {
		$slot_key = $slot['prefix'] . '_' . $map[ $key ];
		if ( isset( $settings[ $slot_key ] ) && '' !== $settings[ $slot_key ] ) {
			return $settings[ $slot_key ];
		}
	}

	return multirent_display_option( $key, $default );
}

function multirent_current_apartment_page_query_args() {
	$slot = multirent_current_page_slot( 'apartment' );
	$page_id = absint( $slot['page_id'] );
	$args = array(
		'post_type'      => 'rental_unit',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
	);

	if ( ! $page_id ) {
		return $args;
	}

	$meta_query = array(
		array(
			'key'     => '_multirent_apartment_page_ids',
			'value'   => ',' . $page_id . ',',
			'compare' => 'LIKE',
		),
	);

	if ( 1 === absint( $slot['index'] ) ) {
		$meta_query = array(
			'relation' => 'OR',
			$meta_query[0],
			array(
				'key'     => '_multirent_apartment_page_ids',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => '_multirent_apartment_page_ids',
				'value'   => '',
				'compare' => '=',
			),
		);
	}

	$args['meta_query'] = $meta_query;
	return $args;
}

function multirent_hero_buttons() {
	$buttons = array();
	foreach ( array( 'apartment', 'contact' ) as $type ) {
		foreach ( multirent_page_slots( $type ) as $slot ) {
			if ( ! $slot['enabled'] || ! $slot['show_hero'] || ! $slot['page_id'] ) {
				continue;
			}
			$label = $slot['button_label'] ? $slot['button_label'] : get_the_title( $slot['page_id'] );
			$buttons[] = array(
				'label' => $label,
				'url'   => get_permalink( $slot['page_id'] ),
				'type'  => $type,
			);
		}
	}

	if ( ! $buttons ) {
		$buttons[] = array(
			'label' => multirent_display_option( 'hero_button_text', __( 'View rentals', 'multirent' ) ),
			'url'   => multirent_display_option( 'hero_button_url', '#rentals' ),
			'type'  => 'apartment',
		);
	}

	return $buttons;
}

function multirent_render_hero_buttons() {
	$buttons = multirent_hero_buttons();
	if ( ! $buttons ) {
		return;
	}
	?>
	<div class="hero-actions">
		<?php foreach ( $buttons as $button ) : ?>
			<a class="button<?php echo 'contact' === $button['type'] ? ' button-light' : ''; ?>" href="<?php echo esc_url( $button['url'] ); ?>"><?php echo esc_html( $button['label'] ); ?></a>
		<?php endforeach; ?>
	</div>
	<?php
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
	$font_css_vars = multirent_global_font_css_vars();
	return sprintf(
		':root{--ink:%1$s;--muted:%2$s;--sea:%3$s;--sea-dark:%4$s;--paper:%5$s;--sky:%6$s;}',
		esc_html( $tokens['ink'] ),
		esc_html( $tokens['muted'] ),
		esc_html( $tokens['primary'] ),
		esc_html( $tokens['dark'] ),
		esc_html( $tokens['surface'] ),
		esc_html( $tokens['accent'] )
	) . ( $font_css_vars ? ':root{' . $font_css_vars . '}' : '' );
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
		'global_font'         => array( 'label' => esc_html__( 'Global site font', 'multirent' ), 'default' => 'theme-default', 'type' => 'select' ),
		'hero_title'          => array( 'label' => esc_html__( 'Hero title', 'multirent' ), 'default' => esc_html__( 'Flexible stays for every guest', 'multirent' ), 'type' => 'text' ),
		'hero_text'           => array( 'label' => esc_html__( 'Hero text', 'multirent' ), 'default' => esc_html__( 'Showcase apartments, rooms, villas, or holiday homes with clear details and easy inquiry paths.', 'multirent' ), 'type' => 'textarea' ),
		'hero_title_size'     => array( 'label' => esc_html__( 'Hero title size in cm', 'multirent' ), 'default' => '', 'type' => 'number' ),
		'hero_button_text'    => array( 'label' => esc_html__( 'Hero button text', 'multirent' ), 'default' => esc_html__( 'View rentals', 'multirent' ), 'type' => 'text' ),
		'hero_button_url'     => array( 'label' => esc_html__( 'Hero button URL', 'multirent' ), 'default' => '#rentals', 'type' => 'url' ),
		'intro_eyebrow'       => array( 'label' => esc_html__( 'Intro eyebrow', 'multirent' ), 'default' => esc_html__( 'About the property', 'multirent' ), 'type' => 'text' ),
		'intro_title'         => array( 'label' => esc_html__( 'Intro title', 'multirent' ), 'default' => esc_html__( 'Manage every unit from WordPress', 'multirent' ), 'type' => 'text' ),
		'intro_text'          => array( 'label' => esc_html__( 'Intro text', 'multirent' ), 'default' => esc_html__( 'Add rental units, amenities, photos, capacity, booking links, and guest-facing details from the WordPress dashboard.', 'multirent' ), 'type' => 'textarea' ),
		'stats_lines'         => array( 'label' => esc_html__( 'Homepage stats, one per line: value | label', 'multirent' ), 'default' => "4 | Rental units\n100 m | Example distance\n24/7 | Self-managed content", 'type' => 'textarea' ),
		'reviews_shortcode'   => array( 'label' => esc_html__( 'Reviews shortcode', 'multirent' ), 'default' => '', 'type' => 'text' ),
		'color_scheme'        => array( 'label' => esc_html__( 'Color scheme', 'multirent' ), 'default' => 'coastal', 'type' => 'select' ),
		'use_custom_colors'   => array( 'label' => esc_html__( 'Use custom colors', 'multirent' ), 'default' => '0', 'type' => 'checkbox' ),
	);

	foreach ( $settings as $key => $config ) {
		$sanitize_callback = 'sanitize_textarea_field';
		if ( 'url' === $config['type'] ) {
			$sanitize_callback = 'esc_url_raw';
		} elseif ( 'global_font' === $key ) {
			$sanitize_callback = 'multirent_sanitize_global_font';
		} elseif ( 'hero_title_size' === $key ) {
			$sanitize_callback = 'multirent_sanitize_hero_title_size';
		}

		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $config['default'],
				'sanitize_callback' => $sanitize_callback,
			)
		);
		$control_args = array(
			'label'   => $config['label'],
			'section' => 'multirent_home',
			'type'    => $config['type'],
		);

		if ( 'color_scheme' === $key ) {
			$control_args['choices'] = wp_list_pluck( multirent_color_schemes(), 'label' );
		} elseif ( 'global_font' === $key ) {
			$control_args['choices'] = wp_list_pluck( multirent_global_font_choices(), 'label' );
			$control_args['description'] = esc_html__( 'Applies to body text, headings, navigation, buttons, cards, and the hero headline. Fonts are bundled locally in the theme.', 'multirent' );
		} elseif ( 'hero_title_size' === $key ) {
			$control_args['description'] = esc_html__( 'Leave empty to use the responsive theme default. Suggested range: 1.0 to 3.0 cm.', 'multirent' );
			$control_args['input_attrs'] = array(
				'min'  => '0.8',
				'max'  => '5',
				'step' => '0.1',
			);
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
		'kitchen'          => '&#x1F372;',
		'separate-entrance' => '&#x1F6AA;',
		'dishwasher'       => '&#x1F37D;&#xFE0F;',
		'coffee-machine'   => '&#x2615;',
		'microwave'        => '&#x25AD;&#x25CF;',
		'washing-machine'  => '&#x1F455;',
		'iron'             => '&#x1F454;',
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

