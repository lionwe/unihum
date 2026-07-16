<?php
/* 
	 plugin Name: reIntegration
	 Description: Цей плагін призначений для того щоб додати на сайт форму яка буде підвязана інтеграцією з різними сервісами та виконувати всю логіку на сервері.
	 Version: 0.1.0 beta
	 Author: Tor4in
	 Author URI: https://github.com/Tor4in
 */



if (
	!defined('ABSPATH') ||
	!defined('WPINC')
) {
	die;
}

define('REINTEGRATION_PLUGIN_ACTIVE', true);

defined('REINTEGRATION_PLUGIN_DIR') || define('REINTEGRATION_PLUGIN_DIR', plugin_dir_url(__FILE__));

require_once plugin_dir_path(__FILE__) . 'pages/index.php';
require_once plugin_dir_path(__FILE__) . 'includes/ajax/index.php';

require_once plugin_dir_path(__FILE__) . 'includes/index.php';

add_action('wp_enqueue_scripts', function () {
	$success_message = '<p>' . esc_html__('Дякуємо! Форму успішно відправлено.', 'reintegration') . '</p>';

	if (function_exists('get_field')) {
		$custom_success_message = wp_kses_post((string) get_field('site_form_success_message', 'option'));
		if ($custom_success_message !== '') {
			$success_message = $custom_success_message;
		}
	}

	wp_enqueue_script(
		'reintegration-script',
		plugin_dir_url(__FILE__) . 'dist/script.js',
		[],
		file_exists(plugin_dir_path(__FILE__) . 'dist/script.js') ? filemtime(plugin_dir_path(__FILE__) . 'dist/script.js') : '1.0',
		true
	);

	wp_script_add_data('reintegration-script', 'type', 'module');
	$script_data = array(
		'ajax_url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('reintegration_form'),
		'success_message' => $success_message,
		'success_duration' => 4000,
		'messages' => array(
			'required' => __('Заповніть це поле.', 'reintegration'),
			'emoji' => __('Смайлики використовувати не можна.', 'reintegration'),
			'too_long' => __('Максимальна довжина — %d символів.', 'reintegration'),
			'phone' => __('Введіть коректний номер телефону — мінімум %d цифр.', 'reintegration'),
			'invalid' => __('Перевірте правильність значення.', 'reintegration'),
			'sending' => __('Відправка...', 'reintegration'),
			'request_error' => __('Не вдалося відправити форму. Спробуйте ще раз.', 'reintegration'),
		),
	);

	wp_add_inline_script(
		'reintegration-script',
		'window.reintegration_ajax = ' . wp_json_encode(
			$script_data,
			JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
		) . ';',
		'before'
	);
});

register_activation_hook(__FILE__, function () {
	REIntegration_Database::install();
});

// TODO
// register_deactivation_hook(__FILE__, function () {
// 	// Деактивувати reIntegrationTelegram та reIntegrationSheets
// 	deactivate_plugins([
// 		'reIntegrationTelegram/reIntegrationTelegram.php',
// 		'reIntegrationSheets/reIntegrationSheets.php'
// 	]);
// });
