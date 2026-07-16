<?php

class REIntegration_Forms
{

	public static function table_name()
	{
		global $wpdb;
		return $wpdb->prefix . 'reintegration_forms';
	}

	public static function get_all()
	{
		global $wpdb;
		return $wpdb->get_results("SELECT * FROM " . self::table_name());
	}

	public static function get($id)
	{
		global $wpdb;
		return $wpdb->get_row($wpdb->prepare("SELECT * FROM " . self::table_name() . " WHERE id = %d", $id));
	}

	public static function create($name, $code = '')
	{
		global $wpdb;
		$wpdb->insert(self::table_name(), [
			'name' => $name,
			'code' => $code,
		]);

		return $wpdb->insert_id;
	}

	public static function update($id, $name, $code = '')
	{
		global $wpdb;
		$compare = self::get($id);
		$wpdb->update(self::table_name(), [
			'name' => $name,
			'code' => $code
		], ['id' => $id]);
		if ($compare->name == $name && $compare->code == $code) {
			return true;
		}
		return $wpdb->rows_affected > 0;
	}
	public static function delete($id)
	{
		global $wpdb;
		$table = $wpdb->prefix . 'reintegration_forms';
		$wpdb->delete($table, ['id' => $id]);
		return $wpdb->rows_affected > 0;
	}
}