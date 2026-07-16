<?php
class REIntegration_History
{
	private static function tableName()
	{
		return REIntegration_Database::getTableNames()['history'];
	}
	public static function create($data, $status = 'success')
	{
		global $wpdb;

		$wpdb->insert(
			self::tableName(),
			[
				"data" => json_encode($data),
				"status" => $status,
				"created_at" => current_time('mysql'),
			]
		);

		return $wpdb->rows_affected > 0;
	}
	public static function patch($id, $data, $status = 'success')
	{
		global $wpdb;

		$wpdb->update(
			self::tableName(),
			[
				"data" => $data,
				"status" => $status,
				"updated_at" => current_time('mysql'),
			],
			["id" => $id]
		);

		return $wpdb->rows_affected > 0;
	}
	public static function get($id)
	{
		global $wpdb;
		return $wpdb->get_row($wpdb->prepare("SELECT * FROM " . self::tableName() . " WHERE id = %d", $id));
	}
	public static function getAll()
	{
		global $wpdb;
		return $wpdb->get_results("SELECT * FROM " . self::tableName() . " ORDER BY created_at DESC");
	}
	public static function delete($id)
	{
		global $wpdb;
		return $wpdb->delete(self::tableName(), ["id" => $id]);
	}
}