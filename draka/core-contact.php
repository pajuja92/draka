<?php

global $jal_db_version;
$jal_db_version = "1.0";

// function qc_install() {

//   global $wpdb;
//   global $jal_db_version;
//   $table_name = $wpdb->prefix . "draka_save";
//   // http://codex.wordpress.org/Creating_Tables_with_Plugins
//   $sql = "CREATE TABLE $table_name (
//     `save_id` MEDIUMINT(9) UNSIGNED NOT NULL AUTO_INCREMENT ,
//     `user_id` BIGINT(20) UNSIGNED NOT NULL ,
//     `user_nicename` VARCHAR(100) NOT NULL ,
//     `save_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,
//     `user_sum` MEDIUMINT(8) NOT NULL ,
//     `user_met` CHAR(2) NOT NULL COMMENT 'Metodyka uzytkownika' ,
//     `user_level` VARCHAR(8) NOT NULL,
//     PRIMARY KEY (`save_id`)
//   ) ENGINE = InnoDB;";
//
//
//   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
//   dbDelta($sql);
//   add_option("jal_db_version", $jal_db_version);
// }
//
// function qc_uninstall() {
//   global $wpdb;
//   global $jal_db_version;
//   $table_name = $wpdb->prefix . "quickcontact";
//   $wpdb->query("DROP TABLE IF EXISTS $table_name");
//   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
//   dbDelta($sql);
// }

//http://codex.wordpress.org/AJAX_in_Plugins
add_action('wp_ajax_nopriv_my_qc_form', 'my_qc_form_callback');
add_action('wp_ajax_my_qc_form','my_qc_form_callback');
function my_qc_form_callback() {
  global $wpdb; // this is how you get access to the database
  $table_name = $wpdb->prefix . "draka_save";
  $values = array();
  $values = json_decode( str_replace("\\", null, $_POST['answers'] ) , false); // grola: https://developer.wordpress.org/reference/functions/wp_unslash/
  $user_id = $_POST['user_id']; // grola: $user_id = sanitize_key( $_POST['user_id'] ); https://developer.wordpress.org/reference/functions/sanitize_key/
  $user_nicename = $_POST['user_nicename']; // grola: sanityzacja https://developer.wordpress.org/reference/functions/sanitize_text_field/
  $choosen_met = $_POST['choosen_met']; // grola: sanityzacja https://developer.wordpress.org/reference/functions/sanitize_text_field/
  $user_level = $_POST['user_level']; // grola: sanityzacja https://developer.wordpress.org/reference/functions/sanitize_text_field/

  // echo "<pre>";
  // var_dump( $values );
  // echo "</pre>";
  $score = 0;
  foreach ($values as $key) {
    $score += $key->value;
  }
  echo $score;
  $rows_affected = $wpdb->insert( $table_name, array(
    'save_id' => null,
    'user_id' => $user_id,
    'user_nicename' => $user_nicename,
    'save_date' => current_time('mysql'),
    'user_sum' => $score,
    'user_met' => $choosen_met,
    'user_level' => $user_level
  ));
  if($rows_affected==1){
    echo "Your message was sent.";
  } else {
    echo "Error, try again later.";
  }

  die(); // this is required to return a proper result
}
?>
