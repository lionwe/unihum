<?php
$location = 'menu-header';

if (isset($args['location'])) {
    $location = $args['location'];
}

$args = array(
    'theme_location' => $location,
    'container' => false,
    'menu_class' => 'nav-list',
    'fallback_cb' => false,
    'depth' => 1,
);

wp_nav_menu($args);
?>

