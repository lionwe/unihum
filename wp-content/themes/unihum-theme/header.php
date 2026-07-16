<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php
$logo = get_custom_logo();
$site_name = get_bloginfo('name');
$site_description = get_bloginfo('description');
$languages = array();

if (function_exists('pll_the_languages')) {
    $languages = pll_the_languages(array(
        'raw' => 1,
        'hide_if_empty' => 0,
    ));
}
?>
<header class="header" data-header data-open="false">
    <div class="container">
        <div class="header__inner">
            <?php if ($logo) : ?>
                <div class="header__brand header__brand--logo">
                    <span class="header__logo"><?php echo $logo; ?></span>
                </div>
            <?php else : ?>
                <a class="header__brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr($site_name); ?>">
                    <span class="header__brand-name"><?php echo esc_html($site_name); ?></span>
                    <?php if ($site_description) : ?>
                        <span class="header__brand-description"><?php echo esc_html($site_description); ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>

            <button
                class="header__toggle"
                type="button"
                data-header-toggle
                data-header-label-open="<?php echo esc_attr__('Відкрити меню', 'unihum'); ?>"
                data-header-label-close="<?php echo esc_attr__('Закрити меню', 'unihum'); ?>"
                aria-expanded="false"
                aria-controls="site-navigation"
                aria-label="<?php echo esc_attr__('Відкрити меню', 'unihum'); ?>"
            >
                <span></span>
                <span></span>
                <span></span>
            </button>

            <div class="header__panel" id="site-navigation" data-header-panel>
                <nav class="header__navigation" aria-label="<?php echo esc_attr__('Основна навігація', 'unihum'); ?>">
                    <?php get_template_part('templates/navigation', null, array('location' => 'menu-header')); ?>
                </nav>

                <?php if (is_array($languages) && count($languages) > 1) : ?>
                    <ul class="header__languages" aria-label="<?php echo esc_attr__('Мови сайту', 'unihum'); ?>">
                        <?php foreach ($languages as $language) : ?>
                            <?php
                            $slug = isset($language['slug']) ? sanitize_key($language['slug']) : '';
                            $label = $slug === 'uk' ? 'UA' : strtoupper($slug);
                            $url = isset($language['url']) ? $language['url'] : '';
                            $is_current = !empty($language['current_lang']);
                            ?>
                            <li>
                                <?php if ($is_current) : ?>
                                    <span aria-current="true"><?php echo esc_html($label); ?></span>
                                <?php elseif ($url) : ?>
                                    <a href="<?php echo esc_url($url); ?>"><?php echo esc_html($label); ?></a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
