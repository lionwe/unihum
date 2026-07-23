<?php
$image_id = absint(get_field('home_author_image'));
$eyebrow = trim((string) get_field('home_author_eyebrow'));
$name = trim((string) get_field('home_author_name'));
$role = trim((string) get_field('home_author_role'));
$bio = trim((string) get_field('home_author_bio'));
$link = get_field('home_author_link');
$meaning_title = trim((string) get_field('home_author_meaning_title'));
$meaning_primary = trim((string) get_field('home_author_meaning_primary'));
$meaning_secondary = trim((string) get_field('home_author_meaning_secondary'));
$features = get_field('home_author_features');

if ($name === '' && $role === '' && $bio === '') {
    return;
}

$link_url = is_array($link) ? (string) ($link['url'] ?? '') : '';
$link_title = is_array($link) ? (string) ($link['title'] ?? '') : '';
$link_target = is_array($link) && !empty($link['target']) ? (string) $link['target'] : '_self';
$fallback_image_url = get_theme_file_uri('assets/images/author.png');
$meaning_heading = $meaning_title !== '' ? rtrim($meaning_title, " \t\n\r\0\x0B:") . ':' : '';
if (!is_array($features)) {
    $features = array();
}

$features = array_values(array_filter($features, static function ($feature) {
    return is_array($feature)
        && trim((string) ($feature['home_author_feature_title'] ?? $feature['title'] ?? '')) !== '';
}));
?>
<section class="author" id="author">
    <div class="container">
        <div class="author__layout">
            <div class="author__media">
                <div class="author__image-wrap">
                    <?php if ($image_id) : ?>
                        <?php
                        echo wp_get_attachment_image(
                            $image_id,
                            'full',
                            false,
                            array(
                                'class' => 'author__image',
                                'loading' => 'lazy',
                            )
                        );
                        ?>
                    <?php else : ?>
                        <img
                            class="author__image"
                            src="<?php echo esc_url($fallback_image_url); ?>"
                            alt="<?php echo esc_attr__('АвваСарраХанн на тлі гірського краєвиду', 'unihum'); ?>"
                            loading="lazy"
                        >
                    <?php endif; ?>
                </div>

            </div>

            <div class="author__content">
                <p class="author__eyebrow"><?php echo esc_html($eyebrow); ?></p>
                <h2 class="author__name"><?php echo esc_html($name); ?></h2>
                <p class="author__role"><?php echo nl2br(esc_html($role)); ?></p>
                <div class="author__divider" aria-hidden="true"><span>✦</span></div>
                <p class="author__bio"><?php echo nl2br(esc_html($bio)); ?></p>

                <aside class="author__meaning" aria-label="<?php echo esc_attr($meaning_title); ?>">
                    <div class="author__symbol" aria-hidden="true">
                        <svg viewBox="0 0 160 160" role="presentation" focusable="false">
                            <circle cx="80" cy="80" r="52" />
                            <ellipse cx="80" cy="80" rx="52" ry="24" />
                            <ellipse cx="80" cy="80" rx="52" ry="24" transform="rotate(60 80 80)" />
                            <ellipse cx="80" cy="80" rx="52" ry="24" transform="rotate(120 80 80)" />
                            <circle cx="80" cy="80" r="8" />
                        </svg>
                    </div>
                    <div class="author__meaning-copy">
                        <h3 class="author__meaning-title"><?php echo esc_html($meaning_heading); ?></h3>
                        <ol class="author__meaning-list">
                            <li class="author__meaning-item author__meaning-item--primary">
                                <span class="author__meaning-text">
                                    <strong><?php echo nl2br(esc_html($meaning_primary)); ?></strong>
                                </span>
                            </li>
                            <li class="author__meaning-item">
                                <span class="author__meaning-text">
                                    <?php echo nl2br(esc_html($meaning_secondary)); ?>
                                </span>
                            </li>
                        </ol>
                    </div>
                </aside>

                <?php if ($link_url !== '') : ?>
                    <a
                        class="btn btn--text-link author__link"
                        href="<?php echo esc_url($link_url); ?>"
                        target="<?php echo esc_attr($link_target); ?>"
                        <?php echo $link_target === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>
                    >
                        <span class="btn__text">
                            <?php echo esc_html($link_title !== '' ? $link_title : __('Докладніше про автора', 'unihum')); ?>
                        </span>
                        <span class="btn__icon" aria-hidden="true">→</span>
                    </a>
                <?php else : ?>
                    <span class="btn btn--text-link btn--static author__link">
                        <span class="btn__text"><?php echo esc_html__('Докладніше про автора', 'unihum'); ?></span>
                        <span class="btn__icon" aria-hidden="true">→</span>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($features !== array()) : ?>
            <div class="author__features">
                <div class="author__features-header">
                    <p class="author__features-kicker"><?php echo esc_html__('Основні орієнтири', 'unihum'); ?></p>
                    <h3 class="author__features-title"><?php echo esc_html__('Принципи автора', 'unihum'); ?></h3>
                    <span class="author__features-count" aria-hidden="true">
                        <?php echo esc_html(sprintf('%02d', count($features))); ?>
                    </span>
                </div>

                <div class="author__features-grid">
                    <?php foreach ($features as $feature) : ?>
                        <?php
                        $feature_title = trim((string) ($feature['home_author_feature_title'] ?? $feature['title'] ?? ''));
                        $feature_description = trim((string) ($feature['home_author_feature_description'] ?? $feature['description'] ?? ''));
                        $feature_icon_id = absint($feature['home_author_feature_icon'] ?? 0);
                        $feature_icon = (string) ($feature['icon'] ?? 'lotus.svg');
                        $feature_icon_url = $feature_icon_id
                            ? wp_get_attachment_image_url($feature_icon_id, 'full')
                            : get_theme_file_uri('assets/images/svg/' . $feature_icon);

                        if (!$feature_icon_url) {
                            $feature_icon_url = get_theme_file_uri('assets/images/svg/lotus.svg');
                        }
                        ?>
                        <article class="author__feature">
                            <div class="author__feature-icon" aria-hidden="true">
                                <span
                                    class="author__feature-icon-mask"
                                    style="<?php echo esc_attr(sprintf('--author-feature-icon: url("%s");', esc_url($feature_icon_url))); ?>"
                                ></span>
                            </div>
                            <div class="author__feature-copy">
                                <h4 class="author__feature-title"><?php echo esc_html($feature_title); ?></h4>
                                <?php if ($feature_description !== '') : ?>
                                    <p class="author__feature-description"><?php echo esc_html($feature_description); ?></p>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
