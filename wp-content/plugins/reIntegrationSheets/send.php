<?php
add_action("ri_send_integration", function ($data) {
	if (!isset($data['timestamp']) && !isset($data['time'])) {
		$data['timestamp'] = date('Y-m-d H:i:s');
	}

	foreach ($data as $key => $value) {
		if (is_string($value)) {
			if (preg_match('/phone|телефон/i', $key)) {
				$data[$key] = preg_replace('/\D+/', '', $value);
			}
		}
	}

	$settings = Sheets_Database::getFields('sheets');

	$token = $settings['token'] ?? null;
	$refreshToken = $settings['token_refresh'] ?? null;
	$fieldsOrder = $settings['fields_order'] ?? null;
	$spreadsheetId = $settings['page_id'] ?? null;
	$sheetName = $settings['sheet_name'] ?? 'Sheet1';

	if (!$spreadsheetId || !$fieldsOrder) {
		error_log("Google Sheets Integration: Відсутні обов'язкові налаштування.");
		return false;
	}

	$fields = array_map('trim', explode(' ', $fieldsOrder));
	$usedKeys = [];
	$values = [];

	foreach ($fields as $field) {
		$values[] = $data[$field] ?? '';
		$usedKeys[] = $field;
	}

	foreach ($data as $key => $value) {
		if (!in_array($key, $usedKeys)) {
			$values[] = $value;
		}
	}

	$range = rawurlencode("$sheetName!A1");
	$url = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$range}:append?valueInputOption=USER_ENTERED";
	$body = json_encode(['values' => [$values]]);

	$sendRequest = function ($accessToken) use ($url, $body) {
		return wp_remote_post($url, [
			'headers' => [
				'Authorization' => "Bearer $accessToken",
				'Content-Type' => 'application/json',
			],
			'body' => $body,
		]);
	};

	if (empty($token)) {
		error_log("Google Sheets Integration: Порожній access token.");
	}

	$response = $sendRequest($token);
	$statusCode = wp_remote_retrieve_response_code($response);
	$responseBody = wp_remote_retrieve_body($response);

	if ($statusCode === 401 && $refreshToken) {
		error_log("Google Sheets Integration: Access token протерміновано, оновлюємо через зовнішній сервіс...");

		// ❗️Оновлення токена через callback.php
		$tokenResponse = wp_remote_get("https://sheets.recipe-agency.com.ua/callback.php?refresh_token=" . urlencode($refreshToken));

		if (is_wp_error($tokenResponse)) {
			error_log("Google Sheets Integration: Помилка під час запиту на оновлення токена — " . $tokenResponse->get_error_message());
			return false;
		}

		$tokenBody = json_decode(wp_remote_retrieve_body($tokenResponse), true);

		if (!empty($tokenBody['access_token'])) {
			$newToken = $tokenBody['access_token'];
			error_log("Google Sheets Integration: Отримано новий токен через сервіс.");

			// Збереження нового access token у БД
			Sheets_Database::setFields( [
				'token' => $newToken,
				'token_refresh' => $tokenBody['refresh_token'] ?? $refreshToken, // Зберігаємо новий refresh token, якщо він є
				'page_id' => $settings['page_id'],
				'sheet_name' => $settings['sheet_name'],
				'fields_order' => $settings['fields_order'],
			]);

			// Повторна спроба запиту
			$response = $sendRequest($newToken);
			$statusCode = wp_remote_retrieve_response_code($response);
			$responseBody = wp_remote_retrieve_body($response);
		} else {
			error_log("Google Sheets Integration: Не вдалося витягнути access_token з відповіді. Вміст: " . wp_remote_retrieve_body($tokenResponse));
			return false;
		}
	}

	if (is_wp_error($response)) {
		error_log("Google Sheets Integration: WP Error — " . $response->get_error_message());
		return false;
	}

	error_log("Google Sheets Integration: Відповідь API [$statusCode]");

	return ($statusCode >= 200 && $statusCode < 300);
});