<h1>Налаштування</h1>

<div class="integrations-list">
	<?php
	$icons = apply_filters('reIntegration_print_setting_page_icons_list', []);
	usort($icons, function ($a, $b) {
		return ($a['order'] ?? 0) <=> ($b['order'] ?? 0);
	});

	foreach ($icons as $icon) {
		if (empty($icon['icon'])) {
			continue;
		} ?>
		<div class="integration-icon">
			<?php if (!empty($icon['link'])): ?>
				<a href="<?php echo $icon['link'] ?>">
				<?php endif; ?>
				<img src="<?php echo $icon['icon'] ?>">
				<?php if (!empty($icon['link']))
					echo "</a>"; ?>
		</div>
	<?php }
	?>
</div>
<div class="integration-page">
	<?php
	$page = isset($_GET['integration_name']) ? $_GET['integration_name'] : '';

	Settings_Page::getPage($page);
	?>
</div>