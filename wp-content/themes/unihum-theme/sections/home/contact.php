<?php

$contact_title = sanitize_text_field((string) get_field('home_contact_title'));
$contact_description = wp_kses_post((string) get_field('home_contact_description'));
$contact_items = get_field('home_contact_items');
$contact_form_title = sanitize_text_field((string) get_field('home_contact_form_title'));
$contact_privacy_text = sanitize_textarea_field((string) get_field('home_contact_privacy_text'));
$contact_form = function_exists('unihum_get_cta_form_markup')
    ? unihum_get_cta_form_markup()
    : '';

$contact_items = is_array($contact_items)
    ? array_slice($contact_items, 0, 3)
    : array();

$contact_items = array_values(
    array_filter(
        $contact_items,
        static function ($contact_item): bool {
            if (!is_array($contact_item)) {
                return false;
            }

            return trim((string) ($contact_item['home_contact_item_title'] ?? '')) !== ''
                || trim((string) ($contact_item['home_contact_item_description'] ?? '')) !== '';
        }
    )
);

$contact_fallback_icons = array('planet.svg', 'people.svg', 'lotus.svg');
$contact_has_copy = $contact_title !== ''
    || $contact_description !== ''
    || $contact_items !== array();

if (!$contact_has_copy && $contact_form === '') {
    return;
}

$contact_section_class = 'contact';

if (!$contact_has_copy) {
    $contact_section_class .= ' contact--form-only';
} elseif ($contact_form === '') {
    $contact_section_class .= ' contact--copy-only';
}
?>

<section class="<?php echo esc_attr($contact_section_class); ?>" id="contacts">
    <div class="container">
        <div class="contact__layout">
            <?php if ($contact_has_copy) : ?>
                <div class="contact__content">
                    <?php if ($contact_title !== '') : ?>
                        <h2 class="contact__title"><?php echo esc_html($contact_title); ?></h2>
                    <?php endif; ?>

                    <div class="contact__ornament" aria-hidden="true">
                        <span></span>
                    </div>

                    <?php if ($contact_description !== '') : ?>
                        <div class="contact__description">
                            <?php echo wp_kses_post($contact_description); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($contact_items !== array()) : ?>
                        <div class="contact__items">
                            <?php foreach ($contact_items as $contact_item_index => $contact_item) : ?>
                                <?php
                                $contact_item = is_array($contact_item) ? $contact_item : array();
                                $contact_item_icon_id = absint(
                                    $contact_item['home_contact_item_icon'] ?? 0
                                );
                                $contact_item_title = sanitize_text_field(
                                    (string) ($contact_item['home_contact_item_title'] ?? '')
                                );
                                $contact_item_description = sanitize_textarea_field(
                                    (string) ($contact_item['home_contact_item_description'] ?? '')
                                );

                                $contact_fallback_icon = $contact_fallback_icons[
                                    $contact_item_index % count($contact_fallback_icons)
                                ];
                                ?>
                                <article class="contact__item">
                                    <div class="contact__item-icon" aria-hidden="true">
                                        <?php if ($contact_item_icon_id > 0) : ?>
                                            <?php
                                            echo wp_get_attachment_image(
                                                $contact_item_icon_id,
                                                'full',
                                                false,
                                                array(
                                                    'class' => 'contact__item-icon-image',
                                                    'alt' => '',
                                                    'loading' => 'lazy',
                                                    'decoding' => 'async',
                                                )
                                            );
                                            ?>
                                        <?php else : ?>
                                            <img
                                                class="contact__item-icon-image"
                                                src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/svg/' . $contact_fallback_icon); ?>"
                                                alt=""
                                                loading="lazy"
                                                decoding="async"
                                            >
                                        <?php endif; ?>
                                    </div>

                                    <div class="contact__item-content">
                                        <?php if ($contact_item_title !== '') : ?>
                                            <h3 class="contact__item-title">
                                                <?php echo esc_html($contact_item_title); ?>
                                            </h3>
                                        <?php endif; ?>

                                        <?php if ($contact_item_description !== '') : ?>
                                            <p class="contact__item-description">
                                                <?php echo esc_html($contact_item_description); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($contact_form !== '') : ?>
                <div class="contact__form-card">
                    <span class="contact__form-ornament" aria-hidden="true">✦</span>

                    <?php if ($contact_form_title !== '') : ?>
                        <h3 class="contact__form-title"><?php echo esc_html($contact_form_title); ?></h3>
                    <?php endif; ?>

                    <div class="contact__form">
                        <?php echo $contact_form; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>

                    <?php if ($contact_privacy_text !== '') : ?>
                        <p class="contact__privacy"><?php echo esc_html($contact_privacy_text); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
