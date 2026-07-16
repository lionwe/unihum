<?php

define("allowed_tags", [
	'a' => ['href' => [], 'title' => [], 'target' => []], // Додав target на всяк випадок
	'b' => [],
	'strong' => [],
	'em' => [],
	'u' => [],
	'i' => [],
	'br' => [],
	'p' => [],
	'img' => [
		'src' => [],
		'alt' => [],
		'loading' => [],
		'class' => [],
		'width' => [],
		'height' => []
	],
	'input' => ['type' => [], 'name' => [], 'value' => [], 'placeholder' => [], 'class' => [], 'id' => [], "required" => [], "data-mask" => [], "data-min-length" => [], "data-max-length" => [], "data-error-message" => []],
	'textarea' => ['name' => [], 'placeholder' => [], 'class' => [], 'id' => [], "required" => []],
	'select' => ['name' => []],
	'option' => ['value' => []],
	'label' => ['for' => [], 'class' => [], 'id' => []],
	'div' => ['class' => []],
	'span' => ['class' => []],
	'ul' => ['class' => [], 'id' => []],
	'ol' => ['class' => [], 'id' => []],
	'li' => ['class' => [], 'id' => []],
	'form' => ['action' => [], 'method' => [], 'id' => [], 'novalidate' => []], 
	'button' => ['type' => [], 'class' => []],
]);

add_action('wp_ajax_reintegration_create_form', 'reintegration_create_form_ajax');
function reintegration_create_form_ajax()
{
	if (!isset($_POST['form_name']) || !isset($_POST['form_code'])) {
		wp_send_json_error(['message' => 'Недостатньо даних для створення форми']);
		return;
	}

	$name = sanitize_text_field($_POST['form_name']);

	$code = wp_kses($_POST['form_code'], allowed_tags);

	// Створюємо нову форму з тимчасовим статусом
	$id = REIntegration_Forms::create($name, $code);

	if ($id) {
		wp_send_json_success(['message' => 'Форма створена', 'id' => $id]);
	} else {
		wp_send_json_error(['message' => 'Не вдалося створити форму']);
	}
}

add_action('wp_ajax_reintegration_save_form', 'reintegration_save_form_ajax');
function reintegration_save_form_ajax()
{
	if (!isset($_POST['id'])) {
		wp_send_json_error(['message' => 'Не вказано ID форми']);
		return;
	}

	$id = intval($_POST['id']);
	if (!isset($_POST['form_name']) || !isset($_POST['form_code'])) {
		wp_send_json_error(['message' => 'Недостатньо даних для збереження форми']);
		return;
	}
	$name = sanitize_text_field($_POST['form_name']);
	$code = wp_kses($_POST['form_code'], allowed_tags);

	// Оновлюємо статус форми на активний
	$updated = REIntegration_Forms::update($id, $name, $code);

	if ($updated) {
		wp_send_json_success(['message' => 'Форма збережена', 'id' => $id]);
	} else {
		wp_send_json_error(['message' => 'Не вдалося зберегти форму']);
	}
}


add_action('wp_ajax_reintegration_delete_form', 'reintegration_delete_form_ajax');
function reintegration_delete_form_ajax()
{
	if (!isset($_POST['id'])) {
		wp_send_json_error(['message' => 'Не вказано ID форми']);
		return;
	}

	$id = intval($_POST['id']);

	// Видаляємо форму
	$deleted = REIntegration_Forms::delete($id);

	if ($deleted) {
		wp_send_json_success(['message' => 'Форма видалена']);
	} else {
		wp_send_json_error(['message' => 'Не вдалося видалити форму']);
	}
}