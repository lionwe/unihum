<?php
add_action('admin_menu', 'reintegration_register_admin_pages');

function reintegration_register_admin_pages()
{
	// Головна сторінка
	$hookHome = add_menu_page(
		'reIntegration',             // Назва сторінки
		'Форми',             // Назва в меню
		'manage_options',            // Права доступу
		'reintegration',             // slug
		'reintegration_main_page',   // Callback-функція
		'dashicons-feedback',        // Іконка
		32                           // Позиція
	);

	add_action('admin_enqueue_scripts', function ($current_hook) use ($hookHome) {
		if ($current_hook === $hookHome) {
			wp_enqueue_script(
				'reintegration-home-script',
				plugin_dir_url(__FILE__) . '../src/js/home.js',
				[], // без jQuery
				'1.0',
				true
			);

			wp_add_inline_script('reintegration-home-script', 'const reintegration_ajax = ' . json_encode([
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('reintegration_home'),
			]) . ';', 'before');
		}
	});

	$hookForm = add_submenu_page(
		"reintegration",
		'Редагування форм',
		'Редагування форм',
		'manage_options',
		'reintegration_form',
		'reintegration_form_page'
	);

	add_action('admin_enqueue_scripts', function ($current_hook) use ($hookForm) {
		if ($current_hook === $hookForm) {
			wp_enqueue_script(
				'reintegration-form-script',
				plugin_dir_url(__FILE__) . '../src/js/form.js',
				[], // без jQuery
				'1.0',
				true
			);

			wp_add_inline_script('reintegration-form-script', 'const reintegration_ajax = ' . json_encode([
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('reintegration_save_form'),
			]) . ';', 'before');
		}
	});

	$hookHistory = add_submenu_page(
		"reintegration",
		'Історія відправлень',
		'Історія відправлень',
		'manage_options',
		'reintegration_history',
		'reintegration_history_page'
	);
	add_action('admin_enqueue_scripts', function ($current_hook) use ($hookHistory) {
		if ($current_hook === $hookHistory) {
			wp_enqueue_script(
				'reintegration-history-script',
				plugin_dir_url(__FILE__) . '../src/js/history.js',
				[], // без jQuery
				'1.0',
				true
			);

			wp_add_inline_script('reintegration-history-script', 'const reintegration_ajax = ' . json_encode([
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('reintegration_history'),
			]) . ';', 'before');
		}
	});

	$hookSettings = add_submenu_page(
		"reintegration",
		'Налаштування',
		'Налаштування',
		'manage_options',
		'reintegration_settings',
		'reintegration_settings_page'
	);

	add_action('admin_enqueue_scripts', function ($current_hook) use ($hookSettings) {
		if ($current_hook === $hookSettings) {
			wp_enqueue_style(
				'reintegration-settings-style',
				plugin_dir_url(__FILE__) . '../src/css/settings.css',
				[], // без jQuery
				'1.0'
			);
			wp_enqueue_script(
				'reintegration-settings-script',
				plugin_dir_url(__FILE__) . '../src/js/settings.js',
				[], // без jQuery
				'1.0',
				true
			);
			wp_add_inline_script('reintegration-settings-script', 'const reintegration_ajax = ' . json_encode([
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('reintegration_settings'),
			]) . ';', 'before');
		}
	});
}
function reintegration_main_page()
{
	require_once plugin_dir_path(__FILE__) . 'home.php';
}
function reintegration_form_page()
{
	require_once plugin_dir_path(__FILE__) . 'form.php';
}

function reintegration_history_page()
{
	require_once plugin_dir_path(__FILE__) . 'history.php';
}

function reintegration_settings_page()
{
	require_once plugin_dir_path(__FILE__) . 'settings.php';
}