<?php 

/* 
	 plugin Name: reIntegration Sheets connector
	 Description: Цей плагін призначений для розширення функціоналу плагіна reIntegration для роботи з Google Sheets.
	 Plugin URI: https://sheets.recipe-agency.com.ua/
	 Version: 0.1.0 beta
	 Author: Tor4in
	 Author URI: https://github.com/Tor4in
 */

if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
}


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

require_once plugin_dir_path(__FILE__) . 'settingsPage.php';


add_action('wp_loaded', function () {
	require_once plugin_dir_path(__FILE__) . 'database.php';
	require_once plugin_dir_path(__FILE__) . 'send.php';
	require_once plugin_dir_path(__FILE__) . 'submit.php';
});