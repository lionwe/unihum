<?php

add_action('admin_post_reintegration_sheets_save_settings', 'reIntegrationSheets_handle_save');
function reIntegrationSheets_handle_save()
{
	// 🔐 Перевірка прав
	if (!current_user_can('manage_options')) {
		wp_die('Недостатньо прав для виконання цієї дії');
	}

	// 🔐 Перевірка nonce
	check_admin_referer('reintegration_sheets_settings');

	// 🔧 Отримуємо й очищаємо дані
	$page_id = isset($_POST['page_id']) ? sanitize_text_field($_POST['page_id']) : '';
	$sheet_name = isset($_POST['sheet_name']) ? sanitize_text_field($_POST['sheet_name']) : '';
	$fields_order = isset($_POST['fields_order']) ? sanitize_text_field($_POST['fields_order']) : '';
	$token = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '';
	$refresh = isset($_POST['token_refresh']) ? sanitize_text_field($_POST['token_refresh']) : '';
	$res = Sheets_Database::setFields([
		'page_id' => $page_id,
		'sheet_name' => $sheet_name,
		'fields_order' => $fields_order, // Розділяємо рядок на масив
		'token' => $token,
		'token_refresh' => $refresh
	]);
	if ($res === false) {
		wp_die('Помилка збереження налаштувань. Спробуйте ще раз.');
	}else{
		wp_redirect(admin_url('admin-post.php?action=reintegration_sheets_ui&status=success'));
		exit;
	}
}