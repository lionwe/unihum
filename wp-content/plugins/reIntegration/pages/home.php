<?php
$forms = REIntegration_Forms::get_all();
?>
<div class="wrap">
	<h1>Форми інтеграції</h1>

	<a href="<?php echo admin_url('admin.php?page=reintegration_form') ?>" class="page-title-action">Додати нову форму</a>

	<?php if (empty($forms)) {
		echo '<p>Форм ще не створено.</p>';
	} else {
		?>
		<table class="widefat fixed striped">
			<thead>
				<tr>
					<th>Назва</th>
					<th>Шорткод</th>
					<th>Дії</th>
				</tr>
			</thead>
			<tbody>

				<?php foreach ($forms as $form) { ?>
					<tr>
						<td> <?php echo esc_html($form->name) ?></td>
						<td><code>[reintegration_form id="<?php echo esc_attr($form->id) ?>"]</code></td>
						<td>
							<a class="button button-primary"
								href="<?php echo admin_url('admin.php?page=reintegration_form&id=' . intval($form->id)) ?>">
								Редагувати
							</a>
							<a class="button button-link-delete"
								href="<?php echo admin_url('admin.php?page=reintegration&action=delete&id=' . intval($form->id)) ?>">
								Видалити
							</a>
						</td>
					</tr>
				<?php } ?>

			</tbody>
		</table>
	<?php } ?>
</div>