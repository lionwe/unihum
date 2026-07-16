<?php

add_filter("reIntegration_print_setting_page_icons_list", function ($icons) {
	$icons[] = array(
		"icon" => plugin_dir_url(__FILE__) . "assets/images/icon.webp",
		"order" => 10,
		"link" => admin_url("admin-post.php?action=reintegration_sheets_ui")
	);
	return $icons;
});



add_action('admin_post_reintegration_sheets_ui', function(){
	function printGoogleAuthField()
	{
		$name = "token";
		$options = Sheets_Database::getFields();
		$token = $options[$name] ?? '';
		$refresh = $options[$name . "_refresh"] ?? '';

		if (!empty($token)) {
			// Вже авторизовано — показуємо парольне поле з токеном
			?>
				<div class="google-auth-field">
					<h3>Токен збережено</h3>
					<button id="remove-token" type="button">Видалити</button>
					<script>
						document.querySelector('.google-auth-field #remove-token').addEventListener('click', function (e) {
							e.preventDefault();
							document.querySelectorAll('.google-auth-field input').forEach(function (input) {
								input.value = '';
							});
							document.querySelector('.google-auth-field h3').textContent = 'Токен видалено.';
						})
					</script>
					<input type="hidden" name="<?php echo esc_attr($name); ?>" value="<?php echo $token ?>" readonly>
					<input type="hidden" name="<?php echo esc_attr($name . "_refresh"); ?>" value="<?php echo $refresh ?>" readonly>
				</div>
				<?php
		} else {
			// Ще не авторизовано — показуємо іконку-кнопку
			$auth_url = "https://sheets.recipe-agency.com.ua/authorize.php";
			?>
				<div class="google-auth-field">
					<a href="<?php echo esc_url($auth_url) ?>">
						<img src="<?php echo esc_url(plugins_url('./assets/images/SingInWithGoogle.webp', __FILE__)) ?>"
							alt="Авторизуватись через Google" width="40" height="40" style="vertical-align: middle;">
					</a>
				</div>
				<script>
					document.querySelectorAll('.google-auth-field a').forEach(function (link) {
						link.addEventListener('click', function (e) {
							e.preventDefault();
							const popup = window.open("<?php echo $auth_url ?>", '_blank', 'width=900,height=600,target="_blank"');

							window.addEventListener('message', function handleOAuthMessage(event) {
								console.log('Received message:', event)
								if (event.origin !== 'https://sheets.recipe-agency.com.ua') return;
								if (event.data.type !== 'google_oauth_success') return;

								window.removeEventListener('message', handleOAuthMessage);

								if (event.data.access_token.trim()) {
									document.querySelector('.google-auth-field').innerHTML = `
															<h3>
																Токен збережено
															</h3>
															<input type="hidden" name="<?php echo esc_attr($name); ?>"
																value="${event.data.access_token}" readonly>
															<input type="hidden" name="<?php echo esc_attr($name . "_refresh"); ?>"
																value="${event.data.refresh_token}" readonly>
															`
								}
							});
						});
					});
				</script>
		<?php }
	}
// 🔐 Захист
if (!current_user_can('manage_options')) {
	wp_die('Недостатньо прав');
}



header('Content-Type: text/html; charset=utf-8'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Сторінка плагіну</title>
	<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) . 'assets/css/style.css'; ?>">
</head>

<body>
	<div id="app">
		<span>
			<h1>Налаштування reIntegration Sheets conector</h1>
			<p>Цей плагін призначений для розширення функціоналу плагіна reIntegration для роботи з Google Sheets.</p>
			<p>Використовуйте цей інтерфейс для налаштування параметрів інтеграції.</p>
		</span>
		<form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
			<?php wp_nonce_field('reintegration_sheets_settings'); ?>
			<?php
			if (!function_exists("formField")) {
				function formField($name, $type, $placeholder = '', $required = false)
				{
					$requiredAttr = $required ? 'required' : '';
					$value = Sheets_Database::getFields()[$name] ?? '';
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
			<input type="hidden" name="action" value="reintegration_sheets_save_settings">
			<!-- Ваші поля налаштувань тут -->
			<?php echo formField("page_id", "text", "ID таблиці", true) ?>
			<?php echo formField("sheet_name", "text", "Назва аркуша", true) ?>
			<?php echo formField("fields_order", "text", "Порядок відображення полів", true) ?>
			<?php printGoogleAuthField(); ?>

			<button type="submit">Зберегти налаштування</button>
			<?php
			if (isset($_GET['status']) && $_GET['status'] === 'success') {
				echo '<p class="success-message">Налаштування успішно збережено!</p>';
			}
			?>
		</form>
	</div>
	<script src="<?php echo plugin_dir_url(__FILE__) . 'assets/js/script.js'; ?>"></script>
</body>

</html>
<?php exit;
});