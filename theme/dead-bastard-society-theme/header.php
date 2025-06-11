<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header>
    <h1><?php bloginfo('name'); ?></h1>
    <?php if (has_nav_menu('primary')) : ?>
        <?php wp_nav_menu(['theme_location' => 'primary']); ?>
    <?php endif; ?>
</header>
