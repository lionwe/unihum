<?php
class REIntegration_Integrations
{
	public static function send($data, $referer = '')
	{
		$historyData = $data;
		$historyData['referer'] = $referer;
		REIntegration_History::create($historyData);

		// wp_remote_post(admin_url('admin-post.php'), [
		// 	'blocking' => false,
		// 	'body' => [
		// 		'action' => 'reintegration_run_background_integrations',
		// 		'data' => wp_json_encode($data),
		// 		'nonce' => wp_create_nonce('reintegration_bg'),
		// 	],
		// ]);
		do_action('ri_send_integration', $data, $referer);
		return true;

	}
}

add_action('admin_post_nopriv_reintegration_run_background_integrations', 'reintegration_run_bg_integrations');
add_action('admin_post_reintegration_run_background_integrations', 'reintegration_run_bg_integrations');

function reintegration_run_bg_integrations()
{
	if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'reintegration_bg')) {
		wp_die('Недійсний запит');
	}

	$data = json_decode(stripslashes($_POST['data'] ?? '{}'), true);

	if (!$data || !is_array($data)) {
		wp_die('Некоректні дані');
	}

	// Тут уже виконуємо повільні інтеграції
	do_action('ri_send_integration', $data);

	wp_die(); // закриває запит
}