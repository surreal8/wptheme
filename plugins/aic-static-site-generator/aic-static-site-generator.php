<?php
/*
Plugin Name: AIC Static Site Generator
Description: Produces a flat file version of a wordpress site
Version: 0.1
Author: The Art Institute of Chicago
*/

define('AIC_STATIC_SITE_GENERATOR_ROOT', dirname(__FILE__));

// prevent the downloading of non pretty link post files
//remove_action('wp_head', 'wp_shortlink_wp_head', 10);

require(AIC_STATIC_SITE_GENERATOR_ROOT.'/inc/options.php');
require(AIC_STATIC_SITE_GENERATOR_ROOT.'/inc/generator.php');
require(AIC_STATIC_SITE_GENERATOR_ROOT.'/inc/deploy.php');

?>
