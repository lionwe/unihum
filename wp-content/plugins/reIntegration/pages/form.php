<?php
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$form = REIntegration_Forms::get($id);
// if (!$form) {
// 	wp_die('Форма не знайдена або не існує');
// }
?>
<div class="wrap">
	<h1><?php echo $id ? 'Редагування форми' : 'Створення нової форми'; ?></h1>
	<form method="post">
		<?php wp_nonce_field('reintegration_save_form'); ?>

		<table class="form-table">
			<tr>
				<th scope="row"><label for="form_name">Назва форми</label></th>
				<td><input name="form_name" id="form_name" type="text" value="<?php echo $form ? $form->name : ""; ?>"
						class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="form_code">Код форми</label></th>
				<td><textarea name="form_code" id="form_code" class="large-text code"
						rows="10"><?php echo $form ? stripslashes($form->code) : ""; ?></textarea></td>
			</tr>
		</table>

		<?php submit_button($id ? 'Оновити форму' : 'Створити форму'); ?>
	</form>
</div>