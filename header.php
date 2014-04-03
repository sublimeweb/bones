<!doctype html>
<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!-->
<html <?php language_attributes(); ?> class="no-js">
<!--<![endif]-->
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>
<?php wp_title(''); ?>
</title>
<meta name="HandheldFriendly" content="True">
<meta name="MobileOptimized" content="320">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php the_field('SO_large_favicon', 'option');?>">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php the_field('SO_retina_iphone', 'option');?>">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php the_field('SO_non_retina_iphone', 'option');?>">
<link rel="apple-touch-icon-precomposed" href="<?php the_field('SO_android_&_standard_favicon', 'option');?>">
<link rel="icon" href="<?php the_field('SO_standard_website_favicon', 'option'); ?>">
<!--[if IE]>
			<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
<![endif]-->
<meta name="msapplication-TileColor" content="<?php the_field('SO_ie10_web_tile_colour', 'option'); ?>">
<meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/library/images/win8-tile-icon.png">
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
<?php wp_head(); ?>
<?php the_field('service-google_code', 'option'); ?>
</head>
<body <?php body_class(); ?>>
<div id="container">
<header class="header" role="banner">
  <div id="inner-header" class="wrap cf">
    <?php // to use a image just replace the bloginfo('name') with your img src and remove the surrounding <p> ?>
    <p id="logo" class="h1"><a href="<?php echo home_url(); ?>" rel="nofollow">
      <?php bloginfo('name'); ?>
      </a></p>
 
    <?php // bloginfo('description'); ?>
    <nav role="navigation">
      <?php wp_nav_menu(array(
    					'container' => false,                         // remove nav container
    					'container_class' => 'menu cf',                 // class of container (should you choose to use it)
    					'menu' => __( 'The Main Menu', 'bonestheme' ),  // nav name
    					'menu_class' => 'nav top-nav cf',               // adding custom nav class
    					'theme_location' => 'main-nav',                 // where it's located in the theme
    					'before' => '',                                 // before the menu
        			'after' => '',                                  // after the menu
        			'link_before' => '',                            // before each link
        			'link_after' => '',                             // after each link
        			'depth' => 0,                                   // limit the depth of the nav
    					'fallback_cb' => ''                         // fallback function (if there is one)
						)); ?>
    </nav>
  </div>
</header>