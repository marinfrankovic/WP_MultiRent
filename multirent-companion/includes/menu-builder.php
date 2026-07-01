<?php
/**
 * Top menu building helpers for MultiRent Companion.
 *
 * @package MultiRentCompanion
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
		$page           = multirent_companion_role_page( $role, $settings );
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
