<?php
class Draka {

	/**
	 * A reference to an instance of this class.
	 */
	private static $instance;

	/**
	 * The array of templates that this plugin tracks.
	 */
	private $is_installed;
	private $is_answers_empty;
	private $user_answers;
	private $answers;
	private $answers_levels;
	private $user_info = array();
	private $db_answers;
	private $user_level;

	/**
	 * Constructor for class
	 */
	public function __construct()	{
		if( null == $this->is_installed ) {
			$this->install();
			$this->is_installed = true;
		}

		$this->user_answers = array (
			'name' => get_the_ID(),
			'scores' => array(),
		);

		$obj = new stdClass();
		$obj->user_met = "";
		$this->user_info = $obj;
		$this->user_info->user_met = get_field('wybrana_metodyka', 'user_'. get_current_user_id() );

		if( $this->is_answers_empty || $this->is_answers_empty == null) {
			$this->fill_answers();
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
		dbDelta($sql);
		add_option("jal_db_version", $jal_db_version);
	}

	function delete_after_deactivate() {
		return true;
	}
	
	function add_answer( $args ) {
		$this->answers[ $args['name'] ] = $args;
	}

	function add_answer_score( $id, $score ) {
		if( $this->answers[ $id ] ) {
			array_push( $this->answers[ $id ]['scores'], $score );
		}
	}

	function add_answer_level( $id, $level ) {
		if( $this->answers[ $id ] ) {
			array_push( $this->answers[ $id ]['levels'], $level );
			$this->answers['levels'][ $level ] += 1;
		}
	}

	function get_answers() {
		return $this->answers;
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
		return $this->answers[ $id ]['scores'][ $i ];
	}

	function get_level( $id, $i ) {
		return $this->answers[ $id ]['levels'][ $i ];
	}

	function submit_callback() {
		global $jal_db_version;
		$jal_db_version = "1.0";
	 	global $wpdb; // this is how you get access to the database
	  	$user_table_name = $wpdb->prefix . "draka_save";
		$answers_table_name = $wpdb->prefix . "draka_answers";

	  	$this->user_answers = array();
	  	$this->user_answers = json_decode( str_replace("\\", null, $_POST['user_answers'] ) , false); 	// grola: https://developer.wordpress.org/reference/functions/wp_unslash/
      																									// grola: tutaj trzeba zrobić sanityzację tego co przychodzi od użytkownika

		$score = 0;
		foreach ($this->user_answers as $answer) {
			$level_name = $this->answers[ $answer->name ][ 'levels' ][ $answer->value ];
			$this->user_answers['levels'][ $level_name ] += 1;

			$multi = $this->is_level_smaller( $this->get_user_level(1), $level_name );
			if( $multi && $multi != 1) {
				$score += ($this->get_points( $answer->name, $answer->value ) * $multi );
			} else {
				$score += $this->answers[ $answer->name ]['scores'][ $answer->value ];
			}
		}

			$current_time = current_time('mysql');
			$user_table_query = array(
			'save_id' => null,
			'user_id' => get_current_user_id(),
			'user_nicename' => $this->user_info->data->display_name,
			'save_date' => $current_time,
			'user_sum' => $score,
			'user_met' => $this->user_info->user_met,
			'user_level' => $this->get_user_level(1)
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
			`user_level`='" . $this->get_user_level(1) . "';";

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
			echo "Wynik został zapisany.<br>Suma punktów: $score<br>Obecny poziom: " . $this->get_user_level(1);
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

		foreach ($this->db_answers as $answer) {
			$level_name = $this->answers[ $answer->name ][ 'levels' ][ $answer->val ];
			$this->db_answers['levels'][ $level_name ] += 1;
		}

		foreach ($this->db_answers as $last_answer ) {
			?>
			<script>
			jQuery( document ).ready(function() {
				jQuery('[name*="<?php echo $last_answer->name; ?>"][value*="<?php echo $last_answer->val; ?>"]:radio').prop('checked',true); // grola: wszystko co trafia do przeglądarki warto przepuścić przez funkcje escapujące: https://codex.wordpress.org/Validating_Sanitizing_and_Escaping_User_Data
                                                                                                                                             // grola: czyli: jQuery('[name*="<?php //echo esc_html( $last_answer->name ); ?>"][value*="<?php //echo esc_html( $last_answer->val ); ?>"]:radio').prop('checked',true);
			});
			</script>
			<?php
		}
	}

	function get_user_level( $mod ) {
		$lvl_names = array( 'alfa', 'beta', 'gamma', 'delta' );
		// if ( $mod ) {
		// 	$this->db_answers = $this->user_answers;
		// }

		$i = 0;
		foreach ($lvl_names as $lvl_name) {
			// echo "start<br>";
			// echo "val1: " . $this->answers['levels'][$lvl_name];
			// echo "<br>val2: " . $this->db_answers['levels'][$lvl_name] . "<br><br>";

			if( (int) $this->answers['levels'][$lvl_name] - $i <= (int) $this->db_answers['levels'][$lvl_name] &&
					$this->db_answers['levels'][$lvl_name] != 0 ) {
				return $lvl_name;
			}
			$i += $this->db_answers['levels'][$lvl_name];
		}
		return 'brak';
	}

	function get_user_answers() {
		return $this->user_answers;
	}

	function fill_answers() {
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

		  $orderby = "user_sum"; //change this to whatever key you want from the array
		  array_multisort($sortArray[$orderby],SORT_DESC,$results);
			?>
			<tbody>
				<tr>
					<th class="table-column lp">LP</th>
					<th class="table-column name">Nazwa jednostki</th>
					<th class="table-column score">Liczba punktów</th>
					<th class="table-column badge">Odnzaka</th>
					<th class="table-column met">Metodyka</th>
				</tr>
				<?php
					$i = 1;
					if( !empty( $results ) ) :
						foreach( $results as $result ) {
							?>
							<tr>
								<td class="table-column lp"><?php echo $i++; ?></td>
								<td class="table-column name"><?php echo $result->user_nicename; ?></td> <!-- // grola: escapowanie: <?php //echo esc_html( $result->user_nicename ); ?> -->
								<td class="table-column score"><?php echo $result->user_sum; ?></td> <!-- // grola: escapowanie -->
								<td class="table-column badge"><?php echo $result->user_level; ?></td> <!-- // grola: escapowanie -->
								<td class="table-column met"><?php echo $result->user_met; ?></td> <!-- // grola: escapowanie -->
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
		die();
	}

	function is_level_greater( $level1, $level2 ) {
		$levels = array(
			'alfa' 	=> 4,
			'beta' 	=> 3,
			'gamma' => 2,
			'delta' => 1,
			'brak' 	=> 0
		);

		if( $levels[ $level1 ] > $levels[ $level2 ] ) {
			return true;
		} else {
			return false;
		}
	}

	function is_level_smaller( $level1, $level2 ) {
		$levels = array(
			'alfa' 	=> 4,
			'beta' 	=> 3,
			'gamma' => 2,
			'delta' => 1,
			'brak' 	=> 0
		);

		if( $levels[ $level1 ] < $levels[ $level2 ] ) {
			return ($levels[ $level1 ] + 1) / $levels[ $level2 ];
		} else {
			return false;
		}
	}


}

add_action( 'wp_loaded', array( 'Draka', 'get_instance' ) );

?>
