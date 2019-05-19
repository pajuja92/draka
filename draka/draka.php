<?php
/**
 * Plugin Name: Draka - System współzawodnictwa drużyn
 * Description: System współzawodnictwa skierowany jest do drużynowych z Hufca ZHP Gdańsk-Śródmieście
 * Author: Mateusz Okła
 * Author URI: https://mateuszokla.pl/
 * Version: 1.0.69
 */

define( 'DRAKA_URL', plugin_dir_url( __FILE__ ) );
define( 'DRAKA_PATH', plugin_dir_path( __FILE__ ) );
define( 'DRAKA_FILE', __FILE__ );

function get_plugin_part_template( $path ) {
  $plugin_path = plugin_dir_path( __FILE__ ) . $path . '.php';
  if( file_exists( $plugin_path ) ) {
    include( $plugin_path );
  }
}

get_plugin_part_template( 'post-types' );
get_plugin_part_template( 'advanced-custom-fields' );
get_plugin_part_template( 'core/PageTemplater' );
get_plugin_part_template( 'core/Draka' );

add_action('wp_loaded', 'Draka');
function Draka() {
  // global $draka;
  $draka = Draka::get_instance();
  //
  // $draka->set_user_info( get_userdata( get_current_user_id() ) );
}

function register_draka_menus() {
	register_nav_menus( array(
		'draka_main_menu'   => __( 'Draka - główne menu' ),
		'draka_header_menu' => __( 'Draka - menu w nagłówku' ),
	));
}
add_action( 'after_setup_theme', 'register_draka_menus' );


function var_dumb( $object ) {
  ?><pre><?php var_dump( $object ); ?></pre><?php
}

function alertt( $obj ) {
  ?>
  <script>
  alert( <?php echo $obj; ?> );
  </script>
  <?php
}

register_activation_hook( __FILE__, 'draka_install');

function draka_install() {
  global $draka;
  $draka->install();
}

// register_deactivation_hook( __FILE__, 'draka_uninstall');

function draka_uninstall() {
  global $draka;
  $draka->uninstall();
}
