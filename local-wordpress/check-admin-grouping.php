<?php
$post_type = get_post_type_object( 'rental_unit' );
$taxonomy  = get_taxonomy( 'rental_amenity' );
echo 'rental_show_in_menu=' . $post_type->show_in_menu . "\n";
echo 'amenity_show_in_menu=' . $taxonomy->show_in_menu . "\n";
