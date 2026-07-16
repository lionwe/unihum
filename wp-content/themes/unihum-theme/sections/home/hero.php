<?php
$background_image_id = absint(get_field('home_hero_background'));
$background_mobile_image_id = absint(get_field('home_hero_background_mobile'));
$hero_image_id = $background_image_id ?: $background_mobile_image_id;
$hero_content = (string) get_field('home_hero_content');
$button = get_field('home_hero_button');
$button_icon_id = absint(get_field('site_icon_button', 'option'));
$quote = (string) get_field('home_hero_quote');
$quote_source = (string) get_field('home_hero_quote_source');
$quote_link = get_field('home_hero_quote_link');
if (wp_strip_all_tags($hero_content) === '') {
    $legacy_title = (string) get_field('home_hero_title');
    $legacy_description = (string) get_field('home_hero_description');

    if (wp_strip_all_tags($legacy_title) !== '') {
        $legacy_title_markup = wp_kses($legacy_title, array('br' => array(), 'strong' => array(), 'em' => array()));

        if (strpos($legacy_title_markup, '<br') === false) {
            $legacy_title_markup = (string) preg_replace('/\s—\s/u', ' —<br>', $legacy_title_markup, 1);
        }

        $hero_content = sprintf(
            '<h1>%s</h1>%s',
            $legacy_title_markup,
            wp_kses_post(wpautop($legacy_description))
        );
    }
}

if (wp_strip_all_tags($hero_content) === '') {
    return;
}

$button_url = is_array($button) && !empty($button['url']) ? $button['url'] : '';
$button_title = is_array($button) && !empty($button['title']) ? $button['title'] : '';
$button_target = is_array($button) && !empty($button['target']) ? $button['target'] : '_self';
$quote_link_url = is_array($quote_link) && !empty($quote_link['url']) ? $quote_link['url'] : '';
$quote_link_title = is_array($quote_link) && !empty($quote_link['title']) ? $quote_link['title'] : '';
$quote_link_target = is_array($quote_link) && !empty($quote_link['target']) ? $quote_link['target'] : '_self';
?>
<section class="hero" id="hero">
    <?php if ($hero_image_id) : ?>
        <div class="hero__media" aria-hidden="true">
            <picture>
                <?php if ($background_mobile_image_id) : ?>
                    <source
                        media="(max-width: 767px)"
                        srcset="<?php echo esc_url(wp_get_attachment_image_url($background_mobile_image_id, 'full')); ?>"
                    >
                <?php endif; ?>
                <?php
                echo wp_get_attachment_image(
                    $hero_image_id,
                    'full',
                    false,
                    array(
                        'class' => 'hero__image',
                        'loading' => 'eager',
                        'fetchpriority' => 'high',
                        'alt' => '',
                    )
                );
                ?>
            </picture>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="hero__grid">
            <div class="hero__content">
                <div class="hero__copy"><?php echo wp_kses_post($hero_content); ?></div>

                <?php if ($button_url && $button_title) : ?>
                    <?php
                    get_template_part('templates/button', null, array(
                        'link' => $button_url,
                        'text' => $button_title,
                        'target' => $button_target,
                        'icon_id' => $button_icon_id,
                        'class' => 'btn--primary',
                    ));
                    ?>
                <?php endif; ?>
            </div>

            <?php if ($quote) : ?>
                <aside class="hero__quote" aria-label="<?php echo esc_attr__('Цитата', 'unihum'); ?>">
                    <span class="hero__quote-mark" aria-hidden="true">“</span>
                    <p class="hero__quote-text"><?php echo nl2br(esc_html($quote)); ?></p>

                    <?php if ($quote_source) : ?>
                        <p class="hero__quote-source"><?php echo esc_html($quote_source); ?></p>
                    <?php endif; ?>

                    <?php if ($quote_link_url && $quote_link_title) : ?>
                        <a
                            class="hero__quote-link"
                            href="<?php echo esc_url($quote_link_url); ?>"
                            target="<?php echo esc_attr($quote_link_target); ?>"
                            <?php echo $quote_link_target === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>
                        >
                            <?php echo esc_html($quote_link_title); ?>
                        </a>
                    <?php endif; ?>
                </aside>
            <?php endif; ?>
        </div>
    </div>
</section>
