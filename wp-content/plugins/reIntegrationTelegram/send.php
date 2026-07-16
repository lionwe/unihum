<?php


add_action('ri_send_integration', function ($data, $referer) {
	if (!defined('REINTEGRATION_PLUGIN_ACTIVE')) {
		error_log('reIntegration Telegram connector: Плагін reIntegration не активний.');
		return;
	}
	$sender = new TelegramSender();
	$sender->send($data, $referer);
}, 10, 2);



class TelegramSender
{
	private $token;
	private $chat_id;

	private function getSettings()
	{
		$settings = ri_get_settings('telegram');
		$this->token = $settings['telegram_token'] ?? '';
		$this->chat_id = $settings['telegram_chat_id'] ?? '';

		if (empty($this->token) || empty($this->chat_id)) {
			throw new Exception('Telegram token or chat ID is not set in the settings.');
		}
	}
	public function send($data, $referer = '')
	{
		self::errorController([$this, 'secureSend'], [$data, $referer]);
	}
	private function errorController($cb, $params = [])
	{
		try {
			self::getSettings();
			if (is_callable($cb)) {
				call_user_func_array($cb, $params);
			} else {
				error_log("Error Controller: Функція $cb не є викликаємою.");
			}
		} catch (Throwable $e) {
			error_log("Error Controller: Виняток у $cb — " . $e->getMessage());
		}
	}
	private function secureSend($data, $referer = '')
	{
		$url = $this->getWebhookUrl();
		if (empty($url) || empty($this->chat_id)) {
			return new Exception("TelegramSender: URL або chat_id не встановлені.");
		}
		error_log("TelegramSender: Надсилання даних у Telegram.");
		$message = self::formatMessage($data);
		$message .= "\n\nВідправлено з: " . esc_html($referer);
		$response = wp_remote_post($url, [
			'body' => [
				'chat_id' => $this->chat_id,
				'text' => $message,
				'parse_mode' => 'HTML',
			],
		]);
		error_log("TelegramSender: Відповідь від Telegram API: " . print_r($response, true));
		if (is_wp_error($response)) {
			return false;
		}
		return true;

	}
	private function getWebhookUrl()
	{
		return 'https://api.telegram.org/bot' . $this->token . '/sendMessage';
	}
	private function formatMessage($data)
	{
		$message = "Нова форма:\n";
		foreach ($data as $key => $value) {
			$message .= "<b>" . esc_html($key) . ":</b> " . esc_html($value) . "\n";
		}
		return $message;
	}
}