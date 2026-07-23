<?php
add_action('wp_enqueue_scripts', 'enqueue_scripts_and_styles');
add_action('after_setup_theme', 'theme_setup');
add_filter('upload_mimes', 'svg_upload_allow');
add_action('wpcf7_before_send_mail', 'send_message_to_telegram');
add_filter('wp_check_filetype_and_ext', 'fix_svg_mime_type', 10, 5);

require get_template_directory() . '/includes/post-types.php';
require get_template_directory() . '/includes/scf-fields.php';


function enqueue_scripts_and_styles()
{
    wp_enqueue_style(
        'theme-fonts',
        'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap',
        array(),
        null
    );
    $main_css_path = get_template_directory() . '/dist/css/main.bundle.css';
    wp_enqueue_style(
        'main-style',
        get_template_directory_uri() . '/dist/css/main.bundle.css',
        array('theme-fonts'),
        file_exists($main_css_path) ? filemtime($main_css_path) : '1.0.0'
    );

    $main_js_path = get_template_directory() . '/dist/js/main.bundle.js';
    wp_enqueue_script(
        'main-js',
        get_template_directory_uri() . '/dist/js/main.bundle.js',
        array(),
        file_exists($main_js_path) ? filemtime($main_js_path) : '1.0.0',
        true
    );
    wp_localize_script('main-js', 'params', array(
        'template_directory_url' => get_template_directory_uri(),
        'ajax_url' => admin_url('admin-ajax.php'),
        'page_template' => get_page_template_slug() ? get_page_template_slug() : ''
    ));
}

function theme_setup()
{
    show_admin_bar(false);
    register_nav_menus(array(
        'menu-header' => 'Header',
        'menu-footer' => 'Footer',
    ));

    add_theme_support('custom-logo');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
}

// ============================================
// ACF Options Page
// ============================================
add_action('acf/init', function () {
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page(array(
            'page_title' => 'Налаштування сайту',
            'menu_title' => 'Налаштування',
            'menu_slug' => 'site-options',
            'capability' => 'manage_options',
            'redirect' => false,
            'position' => 65,
            'icon_url' => 'dashicons-admin-generic',
            'update_button' => 'Зберегти налаштування',
            'updated_message' => 'Налаштування оновлені'
        ));
    }
});

// ============================================
// Helpers & SVG
// ============================================

function get_picture($args = [])
{
    $defaults = [
        'name' => '',
        'src' => '',
        'alt' => '',
        'class' => '',
        'lazy' => true,
    ];
    $args = wp_parse_args($args, $defaults);

    $img_src = $args['src'];
    $is_asset = false;

    if (!empty($args['name'])) {
        $img_src = get_template_directory_uri() . "/assets/images/" . $args['name'];
        $is_asset = true;
    }

    if (empty($img_src))
        return;

    $alt = 'alt="' . esc_attr($args['alt']) . '"';
    $class = $args['class'] ? 'class="' . esc_attr($args['class']) . '"' : '';
    $loading = $args['lazy'] ? 'loading="lazy"' : '';

    if ($is_asset) {
        // Check for WebP variant for theme assets
        $path_parts = pathinfo($args['name']);
        $webp_name = $path_parts['filename'] . '.webp';
        // Note: checking file existence on every load might be expensive, 
        // relying on convention that if using get_picture with name, webp exists.
        // For now, output generic structure.
        $webp_src = get_template_directory_uri() . "/assets/images/" . $webp_name;

        echo '<picture>';
        // Assuming webp exists if requested via this function for assets
        echo '<source srcset="' . esc_url($webp_src) . '" type="image/webp">';
        echo '<img src="' . esc_url($img_src) . '" ' . $alt . ' ' . $class . ' ' . $loading . '>';
        echo '</picture>';
    } else {
        // Fallback or external URL
        echo '<img src="' . esc_url($img_src) . '" ' . $alt . ' ' . $class . ' ' . $loading . '>';
    }
}

function svg_upload_allow($mimes)
{
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}

function fix_svg_mime_type($data, $file, $filename, $mimes, $real_mime = '')
{
    if (version_compare($GLOBALS['wp_version'], '5.1.0', '>=')) {
        $dosvg = in_array($real_mime, ['image/svg', 'image/svg+xml']);
    } else {
        $dosvg = ('.svg' === strtolower(substr($filename, -4)));
    }

    if ($dosvg) {
        if (current_user_can('manage_options')) {
            $data['ext'] = 'svg';
            $data['type'] = 'image/svg+xml';
        } else {
            $data['ext'] = false;
            $data['type'] = false;
        }
    }
    return $data;
}

function getHomePageID()
{
    $default_home_id = get_option('page_on_front');

    if (function_exists('pll_current_language') && function_exists('pll_get_post')) {
        $current_lang = pll_current_language();
        $translated_home_id = pll_get_post($default_home_id, $current_lang);
        return $translated_home_id ? $translated_home_id : $default_home_id;
    }

    return $default_home_id;
}

/**
 * Render the globally configured LeadForms Go CTA form.
 *
 * The option is restricted to the two shortcode tags registered by the plugin.
 * Shortcode callbacks return trusted form markup, including form controls and
 * data attributes that must not be stripped by a second KSES pass.
 */
function unihum_get_cta_form_markup(): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    $shortcode = trim(
        sanitize_text_field((string) get_field('site_cta_form_shortcode', 'option'))
    );

    if (
        $shortcode === ''
        || !preg_match(
            '/^\[\s*(leadforms_go_form|reintegration_form)\b[^\]]*\]\s*$/',
            $shortcode,
            $matches
        )
    ) {
        return '';
    }

    $shortcode_tag = sanitize_key((string) ($matches[1] ?? ''));

    if ($shortcode_tag === '' || !shortcode_exists($shortcode_tag)) {
        return '';
    }

    return do_shortcode($shortcode);
}

// ============================================
// GUTENBERG DISABLE (NUCLEAR OPTION)
// ============================================

// 1. Disable Editor Interface
add_filter('use_block_editor_for_post', '__return_false', 10);
add_filter('use_block_editor_for_post_type', '__return_false', 10);
add_filter('use_widgets_block_editor', '__return_false');

// 2. Remove Frontend Assets (Styles & Scripts)
add_action('wp_enqueue_scripts', function () {
    // Remove Gutenberg styles
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-blocks-style'); // Woocommerce blocks if present

    // Remove "Global Styles" (theme.json bloat)
    wp_dequeue_style('global-styles');

    // Remove Classic Theme styles (SVG filters in body)
    wp_dequeue_style('classic-theme-styles');
}, 100);

// 3. Remove SVG Filters from Body (Critical for clean DOM)
remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
remove_action('in_admin_header', 'wp_global_styles_render_svg_filters');

// 4. Disable Standard Gallery Styles
add_filter('use_default_gallery_style', '__return_false');

// Disable editor for Home page template
add_action('admin_init', function () {
    if (isset($_GET['post'])) {
        $post_id = intval($_GET['post']);
    } elseif (isset($_POST['post_ID'])) {
        $post_id = intval($_POST['post_ID']);
    }

    if (!empty($post_id)) {
        $template = get_post_meta($post_id, '_wp_page_template', true);
        if ($template === 'pages/home.php') {
            remove_post_type_support('page', 'editor');
        }
    }
});

// ============================================
// FIX: Output Buffering Zlib Conflict
// ============================================
remove_action('shutdown', 'wp_ob_end_flush_all', 1);
add_action('shutdown', function () {
    while (@ob_end_flush());
});

