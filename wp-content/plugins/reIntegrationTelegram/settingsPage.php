<?php


add_filter("reIntegration_print_setting_page_icons_list", function ($icons) {
	$icons[] = array(
		"icon" => plugin_dir_url(__FILE__) . "assets/images/icon.webp",
		"order" => 1,
		"link" => admin_url("admin-post.php?action=reintegration_telegram_ui")
	);
	return $icons;
});

add_action("admin_post_reintegration_telegram_ui", function () {
	if (!defined('REINTEGRATION_PLUGIN_ACTIVE')) {
		wp_die('Плагін reIntegration не активний.');
	}
	if (!current_user_can('manage_options')) {
		wp_die('Недостатньо прав');
	}
	header('Content-Type: text/html; charset=utf-8'); ?>

	<!DOCTYPE html>
	<html lang="en">

	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Сторінка налаштувань</title>
		<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) . 'assets/css/style.css'; ?>">
	</head>

	<body>
		<div id="app">
			<span>
				<h1>Налаштування reIntegration Telegram conector</h1>
				<p>Цей плагін призначений для розширення функціоналу плагіна reIntegration для роботи з Telegram.</p>
				<p>Використовуйте цей інтерфейс для налаштування параметрів інтеграції.</p>
			</span>
			<form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
				<?php wp_nonce_field('reintegration_telegram_settings'); ?>
				<?php
				if (!function_exists("formField")) {
					function formField($name, $type, $placeholder = '', $required = false)
					{
						$requiredAttr = $required ? 'required' : '';
						$value = ri_get_settings("telegram")[$name] ?? '';
						ob_start();
						?>
						<label>
							<p><?php echo $placeholder ?></p>
							<input type="<?php echo $type ?>" name="<?php echo esc_attr($name) ?>" value="<?php echo esc_attr($value) ?>"
								placeholder="<?php echo esc_attr($placeholder) ?>" <?php echo $requiredAttr; ?>>
						</label>
						<?php
						$html = ob_get_clean();
						return $html;
					}
				}
				?>
				<input type="hidden" name="action" value="reintegration_telegram_save_settings">
				<!-- Ваші поля налаштувань тут -->
				<?php echo formField("telegram_token", "text", "Токен бота", true) ?>
				<?php echo formField("telegram_chat_id", "text", "ID чату", true) ?>

				<button type="submit">Зберегти налаштування</button>
				<?php
				if (isset($_GET['status']) && $_GET['status'] === 'success') {
					echo '<p class="success-message">Налаштування успішно збережено!</p>';
				}
				?>
			</form>
		</div>
		<script src="<?php echo plugin_dir_url(__FILE__) . 'script.js'; ?>"></script>
	</body>

	</html>
	<?php
	exit;
});