<?php

add_action('admin_post_reintegration_telegram_save_settings', function(){
	// 🔐 Перевірка прав
	if (!current_user_can('manage_options')) {
		wp_die('Недостатньо прав для виконання цієї дії');
	}

	// 🔐 Перевірка nonce
	check_admin_referer('reintegration_telegram_settings');

	// 🔧 Отримуємо й очищаємо дані
	$token = isset($_POST['telegram_token']) ? sanitize_text_field($_POST['telegram_token']) : '';
	$chat_id = isset($_POST['telegram_chat_id']) ? sanitize_text_field($_POST['telegram_chat_id']) : '';
	$res = ri_save_settings([
		'telegram_token' => $token,
		'telegram_chat_id' => $chat_id,
	], 'telegram');
	if ($res === false) {
		wp_die('Помилка збереження налаштувань. Спробуйте ще раз.');
	} else {
		wp_redirect(admin_url('admin-post.php?action=reintegration_telegram_ui&status=success'));
		exit;
	}
});
