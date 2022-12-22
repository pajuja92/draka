<?php

class Draka {

	/**
	 * A reference to an instance of this class.
	 */
	private static $instance;

	/**
	 * Fields in class
	 */
	private $is_installed;
	private $is_answers_empty;
	private $user_answers;
	private $newQuestions;

	private $user;

	/**
	 * Constructor for class
	 */
	public function __construct()	{
		if( null == $this->is_installed ) {
			$this->install();
			$this->is_installed = true;
		}

		try {
            $this->user = User::get_instance();
        } catch ( Exception $e ) {
            add_action( 'admin_notices', function( $ex ) use ( $e ) {
                printf( '<div class="notice notice-error is-dismissible"><p>Błąd krytyczny: %s</p></div>', $e->getMessage() );
            });
        }

        $this->newQuestions = Questions::get_instance();

        $this->user_answers = array (
			'name' => get_the_ID(),
			'scores' => array(),
		);

		if( $this->is_answers_empty || $this->is_answers_empty == null) {
			$this->is_answers_empty = false;
		} else {
			$this->is_answers_empty = true;
		}

		add_action('wp_ajax_nopriv_draka_submit_callback', array( $this, 'submit_callback' ) );
		add_action('wp_ajax_draka_submit_callback', array( $this, 'submit_callback' ) );

		add_action('wp_ajax_nopriv_draka_seek_results_callback', array( $this, 'seek_results_callback' ) );
		add_action('wp_ajax_draka_seek_results_callback', array( $this, 'seek_results_callback' ) );
	}

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new Draka();
		}
		return self::$instance;
	}

	public function install() {
		global $wpdb;
		global $jal_db_version;
		// TODO: przenieść nazwę tabeli do pól klasy
		$user_table_name = $wpdb->prefix . "draka_save";
		$sql_user = "CREATE TABLE $user_table_name (
			`save_id` MEDIUMINT(9) UNSIGNED NOT NULL AUTO_INCREMENT ,
			`user_id` BIGINT(20) UNSIGNED NOT NULL ,
			`user_nicename` VARCHAR(100) NOT NULL ,
			`save_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,
			`user_sum` MEDIUMINT(8) NOT NULL ,
			`user_met` CHAR(2) NOT NULL COMMENT 'Metodyka uzytkownika' ,
			`user_level` VARCHAR(8) NOT NULL,
			`answers` TEXT NOT NULL COMMENT 'Odpowiedzi uzytkownika' ,
			PRIMARY KEY (`save_id`)
		) ENGINE = InnoDB;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql_user);
		add_option("jal_db_version", $jal_db_version);
	}

	function uninstall() {
		global $wpdb;
		global $jal_db_version;

		if( $this->delete_after_deactivate() ) :
			$table_name = $wpdb->prefix . "draka_save"; // tymczasowo dodane 1, aby nie usunąć przez przypadek bazy danych.
			$wpdb->query("DROP TABLE IF EXISTS $table_name");
			$table_name = $wpdb->prefix . "draka_answers"; // tymczasowo dodane 1, aby nie usunąć przez przypadek bazy danych.
			$wpdb->query("DROP TABLE IF EXISTS $table_name");
		endif;

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql );
		add_option("jal_db_version", $jal_db_version);
	}

	function delete_after_deactivate() {
		return true;
	}

	/**
	 *  Callback zapisujący odpowiedzi z ankiety do bazy danych
	 */
	function submit_callback() {
		global $jal_db_version;
		$jal_db_version = "1.0";
	 	global $wpdb; // this is how you get access to the database
	  	$user_table_name = $wpdb->prefix . "draka_save";

	  	$this->user_answers = array();
	  	$this->user_answers = json_decode( str_replace("\\", null, wp_unslash( $_POST['user_answers'] ) ) , false);

		$this->user->update_score( $this->user_answers );
		$score = $this->user->get_scoreSum();

        $current_time = current_time('mysql');
        $user_table_query = array(
            'save_id' => null,
            'user_id' => get_current_user_id(),
            'user_nicename' => $this->user->get_name(),
            'save_date' => $current_time,
            'user_sum' => $score,
            'user_met' => $this->user->get_age_group(),
            'user_level' => $this->user->get_level(),
            'answers'   => serialize( $this->user_answers )
        );
        $user_rows_affected = $wpdb->insert( $user_table_name, $user_table_query );

		if( $user_rows_affected == 1){
			echo "Wynik został zapisany.<br>Suma punktów: $score";
		} else {
			if( !is_user_logged_in() ) {
				echo "Zmiany NIE zostały zapisane. <a href='" . wp_logout_url() . "'>Zaloguj się!</a>";
			} elseif( !$this->user->get_age_group() ) {
				echo "Zmiany NIE zostały zapisane. <a href='" . get_edit_profile_url() . "'>Nie masz wybranej metodyki</a>";
			} else {
				echo "<br>Błąd. Skontaktuj się z administatorem";
			}
		}

		die(); // this is required to return a proper result
	}

	function load_answers() {
        $last_answers = $this->user->get_last_answers();
        foreach ($last_answers as $last_answer ) {
            ?>
            <script>
                jQuery( document ).ready(function() {
                    jQuery('[name*="<?php echo esc_html( $last_answer->name ); ?>"][value*="<?php echo esc_html( $last_answer->value ); ?>"]:radio').prop('checked',true);
                });
            </script>
            <?php
        }
	}

	function seek_results_callback() {
        $this->display_ranking_table( $this->user->get_last_results() , $this->user->get_first_results() );
		die();
	}

	public function display_ranking_table( $last_results , $first_results ) {

        if( !empty( $last_results ) ) :
            $sortArray = array();

            foreach($last_results as $result ){
                foreach( $result as $key=>$value ){
                    if( !isset( $sortArray[ $key ] ) ){
                        $sortArray[ $key ] = array();
                    }
                    $sortArray[ $key ][] = $value;
                }
            }

            $orderby = "user_nicename"; //change this to whatever key you want from the array
            array_multisort($sortArray[$orderby],SORT_DESC,$last_results);
            ?>
            <tbody>
            <tr>
                <th class="table-column name" onclick="sortTable( 0, 'str' )">Nazwa jednostki</th>
                <th class="table-column score" onclick="sortTable( 1, 'int' )">Liczba punktów</th>
                <th class="table-column change" onclick="sortTable( 2, 'int' )">Przyrost punktów w tym roku</th>
                <th class="table-column date" onclick="sortTable( 3, 'str' )">Ostatnie uzupełnienie</th>

            </tr>
            <?php
            $i = 0;
            foreach($last_results as $result ) {
                ?>
                <tr>
                    <td class="table-column name"><?php echo esc_html( $result->user_nicename ); ?></td>
                    <td class="table-column score"><?php echo esc_html( $result->user_sum ); ?></td>
                    <td class="table-column change"><?php echo esc_html( $result->user_sum - $first_results[ $i ]->user_sum ); ?></td>
                    <td class="table-column date"><?php echo  esc_html( $result->save_date ); ?></td>
                </tr>
                <?php
                $i++;
            }
            ?>
            </tbody>
        <?php
        else:
            echo 'Nikt z tej metodyki nie wypełnił jeszcze <a href="' . get_site_url() . '/ankieta/">ankiety</a>.';
        endif;
    }
}
add_action( 'wp_loaded', array( 'Draka', 'get_instance' ) );

?>
