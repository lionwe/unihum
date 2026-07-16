<?php
class REIntegration_Form_Shortcode
{
	public static function render_form($atts)
	{
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts,
			'reintegration_form'
		);

		if ($atts['id']) {
			$form = REIntegration_Forms::get($atts['id']);
			if ($form) {
				return "<div class=\"reintegration-form\">" . stripslashes($form->code) . "</div>";
			} else {
				return '<p>Форма не знайдена.</p>';
			}
		} else {
			return '<p>Вказаний ID форми не є валідним.</p>';
		}
	}
}

// Ініціалізація шорткоду
add_shortcode('reintegration_form', array('REIntegration_Form_Shortcode', 'render_form'));