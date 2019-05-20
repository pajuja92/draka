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

// add_action('wp_loaded', 'Draka');
// function Draka() {
//   // global $draka;
//   $draka = Draka::get_instance();
//   //
//   // $draka->set_user_info( get_userdata( get_current_user_id() ) );
// }

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
  $draka = Draka::get_instance();
  $draka->install();
}

register_uninstall_hook( __FILE__, 'draka_uninstall');
function draka_uninstall() {
  $draka = Draka::get_instance();
  $draka->uninstall();
}

function mytheme_customize_register( $wp_customize_draka ) {
  $wp_customize_draka->add_panel('settings_panel_draka',array(
      'title'=>'Draka',
      'description'=> 'Tutaj możesz dokonać zmiany ustawień',
      'priority'=> 15,
  ));
      $wp_customize_draka->add_section('contat_section_menu',array(
          'title'=>'Menu',
          'priority'=>15,
          'panel'=>'settings_panel_draka',
      ));
              // telefon komórkowy
          $wp_customize_draka->add_setting('default_mainpage_url',array(
              'default' => __('#', 'draka'),
					));
          $wp_customize_draka->add_control('control_mainpage_url',array(
              'label'=>'Strona główna',
              'type'=>'text',
              'section'=>'contat_section_menu',
              'settings'=>'default_mainpage_url',
					));

              // telefon
          $wp_customize_draka->add_setting('default_ranking_url',array(
              'default' => __('#', 'draka'),
					));
          $wp_customize_draka->add_control('control_ranking_url',array(
              'label'=>'Strona rankingu',
              'type'=>'text',
              'section'=>'contat_section_menu',
              'settings'=>'default_ranking_url',
					));
}
add_action( 'customize_register', 'mytheme_customize_register' );
