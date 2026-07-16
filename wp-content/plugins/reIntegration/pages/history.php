<?php

$history = REIntegration_History::getAll();

?>

<div class="wrap">
	<h1>Історія відправлень</h1>

	<?php if (empty($history)) {
		echo '<p>Історії ще немає.</p>';
	} else {
		?>
		<table class="widefat fixed striped">
			<thead>
				<tr>
					<th>Статус</th>
					<th>Дані</th>
					<th>Дата створення</th>
					<th>Видалити</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($history as $item) { ?>
					<tr>
						<td><?php echo esc_html($item->status) ?></td>
						<td>
							<?php
							$data = json_decode($item->data, true);
							if (is_array($data)) {
								foreach ($data as $key => $value) {
									echo '<strong>' . esc_html($key) . '</strong>: ' . esc_html($value) . '<br>';
								}
							} else {
								echo 'Невірний формат даних.';
							}
							?>
						</td>
						<td><?php echo esc_html($item->created_at) ?></td>
						<td>
							<button class="button button-link-delete" id="<?php echo esc_attr($item->id) ?>">
								Видалити
							</button>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	<?php } ?>
</div>