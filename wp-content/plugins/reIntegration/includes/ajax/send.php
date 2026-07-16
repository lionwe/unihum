<?php

add_action('wp_ajax_reintegration_send_form', 'reintegration_send_form_ajax');
add_action('wp_ajax_nopriv_reintegration_send_form', 'reintegration_send_form_ajax');

function reintegration_send_form_ajax()
{
    if (!check_ajax_referer('reintegration_form', 'nonce', false)) {
        wp_send_json_error(
            array('message' => __('Сесію завершено. Оновіть сторінку та спробуйте ще раз.', 'reintegration')),
            403
        );
    }

    if (!isset($_POST['form_data']) || !is_string($_POST['form_data'])) {
        wp_send_json_error(
            array('message' => __('Недостатньо даних для відправлення форми.', 'reintegration')),
            400
        );
    }

    $raw_form_data = wp_unslash($_POST['form_data']);

    if (strlen($raw_form_data) > 20480) {
        wp_send_json_error(
            array('message' => __('Форма містить завеликі значення.', 'reintegration')),
            413
        );
    }

    $form_data = json_decode($raw_form_data, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($form_data)) {
        wp_send_json_error(
            array('message' => __('Некоректний формат даних форми.', 'reintegration')),
            400
        );
    }

    if (count($form_data) > 50) {
        wp_send_json_error(
            array('message' => __('Форма містить забагато полів.', 'reintegration')),
            413
        );
    }

    $validated_data = array();
    $validation_errors = array();

    foreach ($form_data as $field_name => $field_value) {
        $field_name = sanitize_text_field((string) $field_name);

        if ($field_name === '' || !is_scalar($field_value)) {
            continue;
        }

        $field_value = trim((string) $field_value);
        $field_length = function_exists('mb_strlen')
            ? mb_strlen($field_value, 'UTF-8')
            : strlen($field_value);
        $is_phone = preg_match('/phone|телефон|номер/iu', $field_name) === 1;
        $is_textarea = preg_match('/message|comment|повідомлення|текст|text|коментар|питання/iu', $field_name) === 1;
        
        if ($is_phone) {
            $max_length = 32;
        } elseif ($is_textarea) {
            $max_length = 600;
        } else {
            $max_length = 50;
        }

        if ($field_length > $max_length) {
            $validation_errors[$field_name] = sprintf(
                __('Максимальна довжина — %d символів.', 'reintegration'),
                $max_length
            );
            continue;
        }

        if (reintegration_value_has_emoji($field_value)) {
            $validation_errors[$field_name] = __('Смайлики використовувати не можна.', 'reintegration');
            continue;
        }

        if ($is_phone) {
            $phone_digits = preg_replace('/\D+/', '', $field_value);
            if (strlen($phone_digits) < 12) {
                $validation_errors[$field_name] = __(
                    'Введіть коректний номер телефону — мінімум 12 цифр.',
                    'reintegration'
                );
                continue;
            }
        }

        $validated_data[$field_name] = sanitize_textarea_field($field_value);
    }

    if ($validation_errors !== array()) {
        wp_send_json_error(
            array(
                'message' => __('Перевірте правильність заповнення полів.', 'reintegration'),
                'errors' => $validation_errors,
            ),
            422
        );
    }

    $remote_address = isset($_SERVER['REMOTE_ADDR'])
        ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']))
        : 'unknown';
    $rate_limit_key = 'reintegration_rate_' . md5($remote_address);
    $request_count = (int) get_transient($rate_limit_key);

    if ($request_count >= 10) {
        wp_send_json_error(
            array('message' => __('Забагато спроб. Спробуйте пізніше.', 'reintegration')),
            429
        );
    }

    set_transient($rate_limit_key, $request_count + 1, 10 * MINUTE_IN_SECONDS);

    $validated_data = apply_filters('reintegration_before_send_form_data', $validated_data);
    $referer = wp_get_referer();
    $sent = REIntegration_Integrations::send($validated_data, $referer);

    if (!$sent) {
        wp_send_json_error(
            array('message' => __('Не вдалося відправити форму. Спробуйте ще раз.', 'reintegration')),
            500
        );
    }

    wp_send_json_success(
        array(
            'message' => __('Форму успішно відправлено.', 'reintegration'),
        )
    );
}

function reintegration_value_has_emoji($value)
{
    return preg_match(
        '/[\x{1F000}-\x{1FAFF}\x{2600}-\x{27BF}\x{FE0F}\x{200D}]/u',
        $value
    ) === 1;
}
