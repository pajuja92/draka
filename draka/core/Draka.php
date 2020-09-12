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
	private $questions;
	private $newQuestions;
	private $answers_levels;
	private $user_info = array();
	private $db_answers;
	private $user_level;

	private $user;

	/*
	 * User - zawiera informacje o użytkowniku
	 * Database - dane pobrane z bazy danych
	 * Question - zawiera informacje o pytaniu
	 * Answer - zawiera informacje na temat odpowiedzi, zawrte w pytaniu
	 * Score - odpowiedzialna za przeliczanie i przetrzymywanie info o punktach
	 */

	/**
	 * Constructor for class
	 */
	public function __construct()	{
		if( null == $this->is_installed ) {
			$this->install();
			$this->is_installed = true;
		}

		$this->user = User::get_instance();
        $this->newQuestions = Questions::get_instance();


		$this->user_answers = array (
			'name' => get_the_ID(),
			'scores' => array(),
		);

		$obj = new stdClass();
		$obj->user_met = "";
		$this->user_info = $obj;
		$this->user_info->user_met = get_field('wybrana_metodyka', 'user_'. get_current_user_id() );

		if( $this->is_answers_empty || $this->is_answers_empty == null) {
//			$this->fill_questions();
			$this->is_answers_empty = false;
		} else {
			$this->is_answers_empty = true;
		}

		$this->set_user_info( get_userdata( get_current_user_id() ) );


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
			PRIMARY KEY (`save_id`)
		) ENGINE = InnoDB;";

		$answer_table_name = $wpdb->prefix . "draka_answers";
		$sql_answers = "CREATE TABLE $answer_table_name (
		  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		  `save_id` mediumint(9) UNSIGNED NOT NULL,
		  `name` char(9) NOT NULL,
		  `val` char(1) NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql_user);
		dbDelta($sql_answers);
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
	
	function add_answer( $args ) {
		$this->questions[ $args['name'] ] = $args;
	}

	function add_answer_score( $id, $score ) {
		if( $this->questions[ $id ] ) {
			array_push( $this->questions[ $id ]['scores'], $score );
		}
	}

	function add_answer_level( $id, $level ) {
		if( $this->questions[ $id ] ) {
			array_push( $this->questions[ $id ]['levels'], $level );
			$this->questions['levels'][ $level ] += 1;
		}
	}

	function get_questions() {
		return $this->questions;
	}

	function set_user_info( $user_info ) {
		$temp = new stdClass();
		$temp->user_met = $this->user_info->user_met;
		$this->user_info = (object) array_merge( (array) $user_info, (array) $temp );
	}

	function get_user_met() {
		return $this->user_info->user_met;
	}

	function get_points( $id, $i ) {
		return $this->questions[ $id ]['scores'][ $i ];
	}

	function get_level( $id, $i ) {
		return $this->questions[ $id ]['levels'][ $i ];
	}


	/**
	 *  Callback zapisujący odpowiedzi z ankiety do bazy danych
	 */
	function submit_callback() {
		global $jal_db_version;
		$jal_db_version = "1.0";
	 	global $wpdb; // this is how you get access to the database
	  	$user_table_name = $wpdb->prefix . "draka_save";
		$answers_table_name = $wpdb->prefix . "draka_answers";

	
	  	$this->user_answers = array();
	  	$this->user_answers = json_decode( str_replace("\\", null, wp_unslash( $_POST['user_answers'] ) ) , false);



		$this->user->update_score( $this->user_answers );
		$score = $this->user->get_scoreSum();


        $current_time = current_time('mysql');
        $user_table_query = array(
            'save_id' => null,
            'user_id' => get_current_user_id(),
            'user_nicename' => $this->user_info->data->display_name,
            'save_date' => $current_time,
            'user_sum' => $score,
            'user_met' => $this->user_info->user_met,
            'user_level' => $this->get_user_level()
        );
        $user_rows_affected = $wpdb->insert( $user_table_name, $user_table_query);

		$id_select = "
			SELECT
				`save_id`
			FROM
				`wp_draka_save`
			WHERE
				`user_id`='". get_current_user_id() . "' AND
				`user_nicename`='" . $this->user_info->data->display_name . "' AND
				`save_date`='" . $current_time . "' AND
				`user_met`='" . $this->user_info->user_met . "' AND
				`user_level`='" . $this->get_user_level() . "';";

		$id = $wpdb->get_results( $id_select )[0]->save_id;

		foreach ($this->user_answers as $answer ) {
			$answers_rows_affected = $wpdb->insert( $answers_table_name, array(
				'id' 		=> null,
				'save_id' 	=> $id,
				'name'		=> $answer->name,
				'val'		=> $answer->value

			));
		}


		if( $user_rows_affected == 1 ){
			echo "Wynik został zapisany.<br>Suma punktów: $score<br>Obecny poziom: " . $this->get_user_level();
		} else {
			if( !is_user_logged_in() ) {
				echo "Zmiany NIE zostały zapisane. <a href='" . wp_logout_url() . "'>Zaloguj się!</a>";
			} elseif( !$this->user_info->user_met ) {
				echo "Zmiany NIE zostały zapisane. <a href='" . get_edit_profile_url() . "'>Nie masz wybranej metodyki</a>";
			} else {
				echo "<br>Błąd. Skontaktuj się z administatorem";
			}
		}

		die(); // this is required to return a proper result
	}

	function load_answers() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'draka_save';
		$user_id = get_current_user_id();
		$user_met = $this->user_info->user_met;
		$answers_id_sql = "SELECT `save_id` FROM ( SELECT `user_id`, MAX(`save_date`) AS `save_date`, `user_met` FROM `wp_draka_save` GROUP BY `user_id`, `user_met` ) AS latest_orders INNER JOIN `wp_draka_save` ON `wp_draka_save`.user_id = '$user_id' AND `wp_draka_save`.`save_date` = latest_orders.`save_date` AND `wp_draka_save`.`user_met` = '$user_met'";
		$id = $wpdb->get_results( $answers_id_sql )[0]->save_id;

		$db_answers_sql = "SELECT * FROM `wp_draka_answers` WHERE `save_id`='$id'";
		$this->db_answers = $wpdb->get_results( $db_answers_sql );

		foreach ($this->db_answers as $last_answer ) {
			?>
			<script>
                jQuery( document ).ready(function() {
                    jQuery('[name*="<?php echo esc_html( $last_answer->name ); ?>"][value*="<?php echo esc_html( $last_answer->val ); ?>"]:radio').prop('checked',true);
                });
			</script>
			<?php
		}
	}

	function get_user_level( ) {
		return 'brak';
	}

	function get_user_answers() {
		return $this->user_answers;
	}

	function fill_questions() {
		$args = array(
			'post_type' => 'draka',
			'posts_per_page' => -1,
		);
		$pytania = new WP_Query( $args );

		while ( $pytania->have_posts() ) : $pytania->the_post();
			$pyt_args = array(
			  	'name' => get_the_ID(),
			  	'scores' => array(),
				'levels' => array(),
			);

			$this->add_answer( $pyt_args );

			if( have_rows('odpowiedzi') ):
			  while ( have_rows('odpowiedzi') ) : the_row();
			    $punkty = (int) get_sub_field('ilosc_punktow_za_pytanie') * (int) get_field('punkty')[ 'mnoznik_' . $this->user_info->user_met ];
					$poziom = get_sub_field('obowiazkowe_' . $this->user_info->user_met );
					$this->add_answer_score( get_the_ID(), $punkty);
					$this->add_answer_level( get_the_ID(), $poziom );
			  endwhile;
			endif;

		endwhile; wp_reset_postdata();
	}

	function seek_results_callback() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'draka_save';
		$select = "
		SELECT
		  *
		FROM
		  (SELECT
		     `user_id`, MAX(`save_date`) AS `save_date`, `user_met`
		   FROM
		     `wp_draka_save`
		   GROUP BY
		     `user_id`, `user_met`) AS latest_orders
		INNER JOIN
		  `wp_draka_save`
		ON
		  `wp_draka_save`.user_id = latest_orders.user_id AND
		  `wp_draka_save`.`save_date` = latest_orders.`save_date` AND
		  `wp_draka_save`.`user_met` = '" . $_POST['metodyka'] . "';
		";
		$results = $wpdb->get_results( $select ); // Query to fetch data from database table and storing in $results

        $this->display_ranking_table( $results );

		die();
	}

	public function display_ranking_table( $results ) {

        if(!empty($results)) :
            $sortArray = array();

            foreach($results as $result){
                foreach($result as $key=>$value){
                    if(!isset($sortArray[$key])){
                        $sortArray[$key] = array();
                    }
                    $sortArray[$key][] = $value;
                }
            }

            $orderby = "user_nicename"; //change this to whatever key you want from the array
            array_multisort($sortArray[$orderby],SORT_DESC,$results);
            ?>
            <tbody>
            <tr>
                <th class="table-column lp" onclick="sortTable( 0, 'int' )">LP</th>
                <th class="table-column name" onclick="sortTable( 1, 'str' )">Nazwa jednostki</th>
                <th class="table-column score" onclick="sortTable( 2, 'int' )">Liczba punktów</th>
                <th class="table-column change" onclick="sortTable( 3, 'int' )">Przyrost punktów w tym roku</th>
                <th class="table-column date" onclick="sortTable( 4, 'str' )">Ostatnie uzupełnienie</th>

            </tr>
            <?php
            $i = 1;
            if( !empty( $results ) ) :
                foreach( $results as $result ) {
                    ?>
                    <tr>
                        <td class="table-column lp"><?php echo $i++; ?></td>
                        <td class="table-column name"><?php echo esc_html( $result->user_nicename ); ?></td>
                        <td class="table-column score"><?php echo esc_html( $result->user_sum ); ?></td>
                        <td class="table-column change"><?php echo esc_html( 0 ); ?></td>
                        <td class="table-column date"><?php echo  esc_html( $result->save_date ); ?></td>
                    </tr>
                    <?php
                }
            endif;
            ?>
            </tbody>
        <?php
        else:
            echo "Nikt z tej metodyki nie wypełnił jeszcze ankiety";
        endif;

    }



}

add_action( 'wp_loaded', array( 'Draka', 'get_instance' ) );

?>
