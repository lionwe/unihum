<?php
$location = 'menu-header';

if (isset($args['location'])) {
    $location = $args['location'];
}

$args = array(
    'theme_location' => $location,
    'container' => 'ul',
    'menu_class' => 'nav-list',
);

wp_nav_menu($args);
?>

