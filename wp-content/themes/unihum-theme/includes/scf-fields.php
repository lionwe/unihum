<?php

/**
 * Configure SCF (ACF) to save and load field groups from the theme's scf-fields directory.
 */

// 1. Set the path where SCF will SAVE JSON files
add_filter('acf/settings/save_json', function ($path) {
    return get_template_directory() . '/scf-fields';
});

// 2. Add the path from which SCF will LOAD JSON files
add_filter('acf/settings/load_json', function ($paths) {
    // Remove the default path if we only want our theme-specific ones
    if (isset($paths[0])) {
        unset($paths[0]);
    }

    // Add our theme's scf-fields directory
    $paths[] = get_template_directory() . '/scf-fields';

    return $paths;
});
