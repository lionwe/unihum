<?php

class Settings
{
	public static function printField($params)
	{
		$options = self::getOptions($params['slug'] ?? '');
		$name = $params['name'] ?? '';
		$value = $options[$name] ?? '';
		$placeholder = $params['placeholder'] ?? '';
		$type = $params['type'] ?? 'text';
		$label = $params['label'] ?? '';


		if (empty($name)) {
			echo "Поле не може бути пустим";
			return;
		}
		// ==== Спеціальний тип для авторизації через Google ====
		if ($type === 'google_auth') {
			self::printGoogleAuthField($params['slug'], $name, $label);
			return;
		}
		if (!empty($label)) {
			echo '<label><span>' . $label . '</span>';
		}
		if ($type !== 'textarea'):
			?>
			<input type="<?php echo esc_attr($type); ?>" name="<?php echo esc_attr($name); ?>"
				value="<?php echo esc_attr($value); ?>" placeholder="<?php echo esc_attr($placeholder); ?>">
		<?php else: ?>
			<textarea name="<?php echo esc_attr($name); ?>"
				placeholder="<?php echo esc_attr($placeholder); ?>"><?php echo esc_textarea($value); ?></textarea>
			<?php
		endif;
		if (!empty($label)) {
			echo "</label>";
		}
	}
	public static function printIcon($params)
	{
		$icon = $params['icon'] ?? '';
		$slug = $params['slug'] ?? '';
		?>
		<div class="integration-icon">
			<?php if (!empty($slug)): ?>
				<a href="<?php echo add_query_arg("integration_name", $slug, admin_url('admin.php?page=reintegration_settings')); ?>">
				<?php endif; ?>
				<img src="<?php echo REINTEGRATION_PLUGIN_DIR . "src/images/" . $icon ?>" alt="<?php echo esc_attr($slug); ?>">
				<?php if (!empty($slug))
					echo "</a>"; ?>
		</div>
		<?php
	}
	public static function saveOptions($options, $slug)
	{
		if (empty($slug)) {
			return "Поле slug не може бути пустим";
		}
		global $wpdb;

		$table = REIntegration_Database::getTableNames()['integrations'];

		// Перевіряємо, чи існує запис
		$exists = $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT(*) FROM $table WHERE name = %s",
			$slug
		));

		// Якщо існує — оновлюємо
		if ($exists) {
			$oldOptions = $wpdb->get_var($wpdb->prepare("SELECT settings FROM $table WHERE name = %s", $slug));

			if ($oldOptions === json_encode($options)) {
				return "Налаштування не змінено";
			}

			$wpdb->update($table, [
				'settings' => json_encode($options),
			], [
				'name' => $slug,
			]);

			return $wpdb->rows_affected > 0 ? "Налаштування збережено" : "Помилка збереження налаштувань";
		}

		// Якщо не існує — створюємо
		$wpdb->insert($table, [
			'name' => $slug,
			'settings' => json_encode($options),
		]);

		return $wpdb->insert_id ? "Налаштування створено" : "Помилка створення налаштувань";
	}
	public static function getOptions($slug = "")
	{
		global $wpdb;
		$slug = $_GET['integration_name'] ?? $slug;
		$table = REIntegration_Database::getTableNames()['integrations'];
		$options = $wpdb->get_var($wpdb->prepare("SELECT settings FROM $table WHERE name = %s", $slug));
		return json_decode($options ?? "", true);
	}


}

class Settings_Page
{
	public static function getPage($slug)
	{
		return;
	}
}

if (!function_exists('ri_get_settings')) {
	function ri_get_settings($slug = "")
	{
		return Settings::getOptions($slug);
	}
}
if (!function_exists('ri_save_settings')) {
	function ri_save_settings($options, $slug)
	{
		return Settings::saveOptions($options, $slug);
	}
}