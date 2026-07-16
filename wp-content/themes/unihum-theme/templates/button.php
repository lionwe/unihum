<?php

/**
 * Button template.
 *
 * @var array $args {
 *     @type string $link    Button URL. Leave empty to render a button element.
 *     @type string $text    Button label.
 *     @type string $class   Additional BEM modifier classes.
 *     @type string $target  Link target.
 *     @type int    $icon_id Icon attachment ID.
 *     @type string $type    Button type: button, submit or reset.
 *     @type string $aria_label Accessible button label.
 *     @type string $download Downloaded file name for link buttons.
 * }
 */

$link = isset($args['link']) ? esc_url_raw((string) $args['link']) : '';
$text = isset($args['text']) ? sanitize_text_field((string) $args['text']) : '';
$target = isset($args['target']) ? sanitize_key((string) $args['target']) : '_self';
$icon_id = isset($args['icon_id']) ? absint($args['icon_id']) : 0;
$icon_url = $icon_id > 0
    ? esc_url_raw((string) wp_get_attachment_url($icon_id))
    : '';
$icon_style = $icon_url
    ? sprintf("--btn-icon-url: url('%s');", esc_url($icon_url))
    : '';
$type = isset($args['type']) ? sanitize_key((string) $args['type']) : 'button';
$aria_label = isset($args['aria_label']) ? sanitize_text_field((string) $args['aria_label']) : '';
$aria_label_attribute = $aria_label !== '' ? sprintf(' aria-label="%s"', esc_attr($aria_label)) : '';
$download = isset($args['download']) ? sanitize_file_name((string) $args['download']) : '';
$download_attribute = $download !== '' ? sprintf(' download="%s"', esc_attr($download)) : '';
$additional_classes = isset($args['class']) ? (string) $args['class'] : 'btn--primary';

if ($text === '') {
    return;
}

$allowed_targets = array('_self', '_blank');
$allowed_types = array('button', 'submit', 'reset');

if (!in_array($target, $allowed_targets, true)) {
    $target = '_self';
}

if (!in_array($type, $allowed_types, true)) {
    $type = 'button';
}

$class_names = preg_split('/\s+/', trim($additional_classes)) ?: array();
$class_names = array_filter(array_map('sanitize_html_class', $class_names));
$button_class = trim(implode(' ', array_merge(array('btn'), $class_names)));

if ($link === '') :
    ?>
    <button class="<?php echo esc_attr($button_class); ?>" type="<?php echo esc_attr($type); ?>"<?php echo $aria_label_attribute; ?>>
        <span class="btn__text"><?php echo esc_html($text); ?></span>
        <?php if ($icon_url !== '') : ?>
            <span class="btn__icon" style="<?php echo esc_attr($icon_style); ?>" aria-hidden="true">
                <?php echo wp_get_attachment_image($icon_id, 'thumbnail', false, array('class' => 'btn__image')); ?>
            </span>
        <?php endif; ?>
    </button>
    <?php
    return;
endif;
?>
<a
    class="<?php echo esc_attr($button_class); ?>"
    href="<?php echo esc_url($link); ?>"
    target="<?php echo esc_attr($target); ?>"
    <?php echo $aria_label_attribute; ?>
    <?php echo $download_attribute; ?>
    <?php echo $target === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>
>
    <span class="btn__text"><?php echo esc_html($text); ?></span>
    <?php if ($icon_url !== '') : ?>
        <span class="btn__icon" style="<?php echo esc_attr($icon_style); ?>" aria-hidden="true">
            <?php echo wp_get_attachment_image($icon_id, 'thumbnail', false, array('class' => 'btn__image')); ?>
        </span>
    <?php endif; ?>
</a>
