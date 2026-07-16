<?php

add_action('wp_ajax_reintegration_get_settings_page', 'reintegration_get_settings_page');

function reintegration_get_settings_page()
{
	$integration_name = $_POST['integration_name'] ?? '';
	if (empty($integration_name)) {
		wp_send_json_error('Integration name is empty');
	}
	ob_start();
	Settings_Page::getPage($integration_name);
	$content = ob_get_clean();
	wp_send_json_success([
		'content' => $content,
	]);
}

add_action('wp_ajax_reintegration_save_settings', 'reintegration_save_settings');

function reintegration_save_settings()
{
	$slug = $_POST['slug'] ?? '';
	$options = $_POST['options'] ?? '';
	if (empty($slug) || empty($options)) {
		wp_send_json_error('Slug or options is empty');
	}
	$options = json_decode(stripslashes($options), true);
	$result = Settings::saveOptions($options, $slug);
	wp_send_json_success($result);
}