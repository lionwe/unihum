<?php

$cta_field_prefix = sanitize_key((string) ($args['field_prefix'] ?? ''));
$cta_legacy_field_prefix = sanitize_key((string) ($args['legacy_field_prefix'] ?? ''));
$cta_variant = sanitize_html_class((string) ($args['variant'] ?? ''));
$cta_section_id = sanitize_html_class((string) ($args['section_id'] ?? ''));

if ($cta_field_prefix === '' || $cta_section_id === '') {
    return;
}

$cta_section_class = 'more-info';

if ($cta_variant !== '') {
    $cta_section_class .= ' more-info--' . $cta_variant;
}

$cta_get_field = static function (string $suffix) use (
    $cta_field_prefix,
    $cta_legacy_field_prefix
) {
    $value = get_field($cta_field_prefix . '_' . $suffix);

    if (
        $cta_legacy_field_prefix !== ''
        && ($value === false || $value === null || $value === '' || $value === 0)
    ) {
        return get_field($cta_legacy_field_prefix . '_' . $suffix);
    }

    return $value;
};

$cta_content = wp_kses_post((string) $cta_get_field('content'));
$cta_form_shortcode = sanitize_text_field(
    (string) $cta_get_field('form_shortcode')
);
$cta_background_desktop_id = absint($cta_get_field('background_desktop'));
$cta_background_mobile_id = absint($cta_get_field('background_mobile'));
$cta_allowed_mime_types = array('image/webp', 'image/avif');

if ($cta_content === '') {
    return;
}

$cta_validate_image = static function (int $attachment_id) use ($cta_allowed_mime_types): int {
    if ($attachment_id === 0) {
        return 0;
    }

    return in_array(get_post_mime_type($attachment_id), $cta_allowed_mime_types, true)
        ? $attachment_id
        : 0;
};

$cta_background_desktop_id = $cta_validate_image($cta_background_desktop_id);
$cta_background_mobile_id = $cta_validate_image($cta_background_mobile_id);
$cta_background_id = $cta_background_desktop_id ?: $cta_background_mobile_id;
$cta_background_mobile_srcset = $cta_background_mobile_id > 0
    ? wp_get_attachment_image_srcset($cta_background_mobile_id, 'full')
    : '';
$cta_form = '';

if ($cta_form_shortcode !== '' && shortcode_exists('reintegration_form')) {
    $cta_form = do_shortcode($cta_form_shortcode);
}

$cta_form_allowed_html = defined('allowed_tags') && is_array(allowed_tags)
    ? allowed_tags
    : wp_kses_allowed_html('post');
?>

<section class="<?php echo esc_attr($cta_section_class); ?>" id="<?php echo esc_attr($cta_section_id); ?>">
    <div class="container">
        <div class="more-info__inner">
            <?php if ($cta_background_id > 0) : ?>
                <picture class="more-info__background" aria-hidden="true">
                    <?php if ($cta_background_mobile_srcset !== '') : ?>
                        <source
                            media="(max-width: 47.9375rem)"
                            srcset="<?php echo esc_attr($cta_background_mobile_srcset); ?>"
                            sizes="calc(100vw - 2rem)"
                        >
                    <?php endif; ?>
                    <?php
                    echo wp_get_attachment_image(
                        $cta_background_id,
                        'full',
                        false,
                        array(
                            'class' => 'more-info__background-image',
                            'alt' => '',
                            'loading' => 'lazy',
                            'decoding' => 'async',
                            'sizes' => '(max-width: 47.9375rem) calc(100vw - 2rem), 81.25rem',
                        )
                    );
                    ?>
                </picture>
            <?php endif; ?>

            <div class="more-info__body">
                <div class="more-info__content">
                    <?php echo wp_kses_post($cta_content); ?>
                </div>

                <?php if ($cta_form !== '') : ?>
                    <div class="more-info__form section-form">
                        <?php echo wp_kses($cta_form, $cta_form_allowed_html); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
