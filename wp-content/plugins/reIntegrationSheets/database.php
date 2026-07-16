<?php
class Sheets_Database
{
	private static function getTableName()
	{
		global $wpdb;

		$table_integrations = $wpdb->prefix . 'reintegration_integrations';

		return $table_integrations;
	}
	public static function getFields(){
		global $wpdb;

		$table_integrations = self::getTableName();

		$fields = $wpdb->get_row("SELECT settings FROM $table_integrations WHERE name = 'sheets'");
		if (empty($fields)) {
			return [];
		}
		$fields = json_decode($fields->settings, true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			return [];
		}
		return $fields;
	}
	public static function setFields($fields)
	{
		global $wpdb;

		$table_integrations = self::getTableName();

		// Перевірка наявності інтеграції
		$integration = $wpdb->get_row("SELECT * FROM $table_integrations WHERE name = 'sheets'");
		if (!$integration) {
			// Якщо інтеграція не існує, створюємо її
			$wpdb->insert(
				$table_integrations,
				array(
					'name' => 'sheets',
					'settings' => json_encode($fields),
				)
			);
			if ($wpdb->last_error) {
				return false; // Помилка вставки
			}
			return true;
		}

		// Оновлення налаштувань інтеграції
		$settings = json_encode($fields);
		if (json_last_error() !== JSON_ERROR_NONE) {
			return false; // Помилка кодування JSON
		}

		return $wpdb->update(
			$table_integrations,
			array('settings' => $settings),
			array('name' => 'sheets')
		);
	}
}