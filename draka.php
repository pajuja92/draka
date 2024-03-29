<?php
/**
 * Plugin Name: Draka - System współzawodnictwa drużyn
 * Description: System współzawodnictwa skierowany jest do drużynowych z Hufca ZHP Gdańsk-Śródmieście
 * Author: Mateusz Okła
 * Author URI: https://mateuszokla.pl/
 * Version: 1.1
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

function draka_enqueue_scripts() {

}

include __DIR__ . '/post-types.php';
include __DIR__ . '/advanced-custom-fields.php';
include __DIR__ . '/core/PageTemplater.php';
include __DIR__ . '/core/Draka.php';
include __DIR__ . '/core/User.php';
include __DIR__ . '/core/Question.php';
include __DIR__ . '/core/Questions.php';
include __DIR__ . '/core/Answer.php';
include __DIR__ . '/core/Database.php';
include __DIR__ . '/core/Options.php';


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
        $wp_customize_draka->add_section('draka_section_menu',array(
          'title'=>'Menu',
          'priority'=>15,
          'panel'=>'settings_panel_draka',
        ));

            $wp_customize_draka->add_setting('default_mainpage_url',array(
              'default' => __('/', 'draka'),
            ));
            $wp_customize_draka->add_control('control_mainpage_url',array(
              'label'=>'Strona główna',
              'type'=>'text',
              'section'=>'draka_section_menu',
              'settings'=>'default_mainpage_url',
            ));

            $wp_customize_draka->add_setting('default_ankieta_url',array(
                'default' => __('/ankieta/', 'draka'),
            ));
            $wp_customize_draka->add_control('control_ankieta_url',array(
                'label'=>'Strona ankiety',
                'type'=>'text',
                'section'=>'draka_section_menu',
                'settings'=>'default_ankieta_url',
            ));

            $wp_customize_draka->add_setting('default_ranking_url',array(
              'default' => __('#', 'draka'),
            ));
            $wp_customize_draka->add_control('control_ranking_url',array(
              'label'=>'Strona rankingu',
              'type'=>'text',
              'section'=>'draka_section_menu',
              'settings'=>'default_ranking_url',
            ));

        $wp_customize_draka->add_section('draka_page_widget',array(
          'title'=>'Widżety ankiety',
          'priority'=>15,
          'panel'=>'settings_panel_draka',
        ));

            $wp_customize_draka->add_setting('default_contact_form',array(
              'default' => __('', 'draka'),
            ));
            $wp_customize_draka->add_control('control_contact_form',array(
              'label'=>'Strona główna',
              'type'=>'text',
              'section'=>'draka_page_widget',
              'settings'=>'default_contact_form',
            ));
}
add_action( 'customize_register', 'mytheme_customize_register' );

function is_valid_email_domain( $login, $email, $errors ){
    $valid_email_domains = array("zhp.net.pl");// whitelist email domain lists
    $valid = false;
    foreach( $valid_email_domains as $d ){
        $d_length = strlen( $d );
        $current_email_domain = strtolower( substr( $email, -($d_length), $d_length));
        if( $current_email_domain == strtolower($d) ){
            $valid = true;
            break;
        }
    }
    // if invalid, return error message
    if( $valid === false ){
        $errors->add('domain_whitelist_error', __( '<strong>BŁĄD</strong>: możesz zalogować się tylko za pomocą maila w domenie @zhp.net.pl' ) );
    }
}
add_action('register_post', 'is_valid_email_domain',10,3 );

function redirect_admin( $redirect_to, $request, $user ){
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
        $redirect_to = get_site_url(); // Your redirect URL
    }
    return $redirect_to;
}
add_filter( 'login_redirect', 'redirect_admin', 10, 3 );
