<?php

add_action('wp_ajax_reintegration_delete_lead', "reintegration_delete_lead");

function reintegration_delete_lead()
{
	if (!isset($_POST['id'])) {
		wp_send_json_error('ID не передано');
	}
	$id = intval($_POST['id']);
	if (REIntegration_History::delete($id)) {
		wp_send_json_success('Запис видалено');
	} else {
		wp_send_json_error('Не вдалося видалити запис');
	}
}