<?php
/* 
	 plugin Name: reIntegration Telegram conector
	 Description: Допомнення до основного плагіну reIntegration з інтеграцією в телеграм.
	 Version: 0.1.0 beta
	 Author: Tor4in
	 Author URI: https://github.com/Tor4in
 */

if (!defined('ABSPATH'))
	exit; // Exit if accessed directly


add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
	if (!defined('REINTEGRATION_PLUGIN_ACTIVE')) {
		unset($links['activate']);
		$links[] = '<span style="color: red;">Потрібен reIntegration</span>';
	}
	return $links;
});
register_activation_hook(__FILE__, function () {
	if (!defined('REINTEGRATION_PLUGIN_ACTIVE')) {
		deactivate_plugins(plugin_basename(__FILE__));
		wp_die(
			'Плагін <strong>reIntegration Sheets connector</strong> не може бути активований без активного плагіна <strong>reIntegration</strong>.',
			'Помилка активації',
			array('back_link' => true)
		);
	}
});

require_once __DIR__ . '/settingsPage.php';
add_action('wp_loaded', function () {
	if (!defined('REINTEGRATION_PLUGIN_ACTIVE')) {
		return;
	}
	require_once __DIR__ . '/submit.php';
	require_once __DIR__ . '/send.php';
});


