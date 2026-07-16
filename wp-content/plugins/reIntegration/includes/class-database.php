<?php
class REIntegration_Database
{
	public static function install()
	{
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table_integrations = self::getTableNames()['integrations'];
		$table_forms = self::getTableNames()['forms'];
		$table_history = self::getTableNames()['history'];

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta("
            CREATE TABLE $table_integrations (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                settings TEXT,
                PRIMARY KEY (id)
            ) $charset_collate;
        ");

		dbDelta("
            CREATE TABLE $table_forms (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
								code TEXT,
                custom_integration_options TEXT,
                PRIMARY KEY (id)
            ) $charset_collate;
        ");

		dbDelta("
            CREATE TABLE $table_history (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                data TEXT,
                status VARCHAR(50),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;
        ");
	}
	// Функція для видалення таблиць при деактивації плагіна
	public static function uninstall()
	{
		global $wpdb;


		$table_integrations = self::getTableNames()['integrations'];
		$table_forms = self::getTableNames()['forms'];
		$table_history = self::getTableNames()['history'];

		$wpdb->query("DROP TABLE IF EXISTS $table_integrations");
		$wpdb->query("DROP TABLE IF EXISTS $table_forms");
		$wpdb->query("DROP TABLE IF EXISTS $table_history");
	}
	public static function getTableNames()
	{
		global $wpdb;

		$table_integrations = $wpdb->prefix . 'reintegration_integrations';
		$table_forms = $wpdb->prefix . 'reintegration_forms';
		$table_history = $wpdb->prefix . 'reintegration_history';

		return [
			'integrations' => $table_integrations,
			'forms' => $table_forms,
			'history' => $table_history,
		];
	}
}