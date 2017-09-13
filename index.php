<?php
/*
Plugin Name: Plugin constructor
Plugin URI: http://github.com/rardprf/plugin-constructor
Description: Конструктор плагинов дл WP
Version: 0.1
Author: rardprf
Author URI: http://github.com/rardprf
*/

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

register_activation_hook(__FILE__, 'msp_helloworld_activation');
register_deactivation_hook(__FILE__, 'msp_helloworld_deactivation');

wp_register_style('style.css', plugin_dir_url( __FILE__ ) . '/views/style.css', []);
wp_enqueue_style('style.css');

function msp_helloworld_activation() {
    global $wpdb;

    $table_name = $wpdb->get_blog_prefix() . 'pconstructur';

    dbDelta("CREATE TABLE IF NOT EXISTS `$table_name` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `path` varchar(64) NOT NULL,
  `version` varchar(16) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `wp_pconstructur`
 ADD PRIMARY KEY (`id`);
ALTER TABLE `wp_pconstructur`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
");
    register_uninstall_hook(__FILE__, 'msp_helloworld_uninstall');
}

function msp_helloworld_deactivation() {
    global $wpdb;

    $table_name = $wpdb->get_blog_prefix() . 'pconstructur';

    $wpdb->query("DROP TABLE `$table_name`;");
}

function msp_helloworld_uninstall() {

}

add_action('admin_menu', 'mt_add_pages');

function mt_add_pages() {
    // Add a new submenu under Options:
    add_menu_page('Конструктор плагинов', 'Конструктор плагинов', 8, 'plugin-constructor/main', 'page_index');
    add_submenu_page(null, 'Создать новый плагин', 'Создать плагин', 8, 'plugin-constructor/create', 'page_create');
}

function page_index() {
    require(__DIR__ . '/views/main.php');
}

function page_create() {
    require(__DIR__ . '/views/create.php');
}

?>