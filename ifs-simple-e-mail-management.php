<?php
/*
Plugin Name: IFS Simple Mass Mailer
Plugin URI: http://www.inspiration-for-success.com/plugins/
Description: IFS e-mail management module for simple mass mailings.
Tags: mailer, mailing list, simple mailer, mass mailer
Version: 1.8
Stable tag: 1.71
Author: Guus Ellenkamp
Author URI: http://designs.activediscovery.net/
License: GPLv2
*/

if (!defined('_VALID_ADD')) define('_VALID_ADD',1);

require_once(ABSPATH.'/wp-content/plugins/ifs-simple-e-mail-management/includes/add_mini_lib.php');
require_once(ABSPATH.'/wp-content/plugins/ifs-simple-e-mail-management/includes/main-lib.php');
require_once(ABSPATH.'/wp-content/plugins/ifs-simple-e-mail-management/includes/ifs-frontend.php');
require_once(ABSPATH.'/wp-content/plugins/ifs-simple-e-mail-management/includes/ifs-backend.php');

register_activation_hook( __FILE__, 'ifs_install' );

add_action('wp_head','ifs_ajaxurl');

add_action('admin_menu','ifs_mgt_menu');
add_action('wp_ajax_ifs_action', 'ifs_action_callback');

add_action('wp_enqueue_scripts','ifs_front_end_register_script');
add_action('wp_ajax_ifs_frontend_action','ifs_frontend_handler');
add_action('wp_ajax_nopriv_ifs_frontend_action','ifs_frontend_handler');
add_filter('the_content','ifs_add_email_box');
add_action('loop_start','check_ifs_confirmation');
add_action('loop_start','ifs_unsubscribe');

?>