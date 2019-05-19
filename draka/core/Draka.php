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
	private $user_info = array();

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
	}

	/**
	 * Destructor for class
	 */
	public function __destruct() {
		// self::uninstall();
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

		$table_name = $wpdb->prefix . "draka_save";
		$sql = "CREATE TABLE $table_name (
			`save_id` MEDIUMINT(9) UNSIGNED NOT NULL AUTO_INCREMENT ,
			`user_id` BIGINT(20) UNSIGNED NOT NULL ,
			`user_nicename` VARCHAR(100) NOT NULL ,
			`save_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,
			`user_sum` MEDIUMINT(8) NOT NULL ,
			`user_met` CHAR(2) NOT NULL COMMENT 'Metodyka uzytkownika' ,
			`user_level` VARCHAR(8) NOT NULL,
			PRIMARY KEY (`save_id`)
		) ENGINE = InnoDB;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		add_option("jal_db_version", $jal_db_version);
	}

	function uninstall() {
		global $wpdb;
		global $jal_db_version;

		$table_name = $wpdb->prefix . "draka_save1"; // tymczasowo dodane 1, aby nie usunąć przez przypadek bazy danych.
		$wpdb->query("DROP TABLE IF EXISTS $table_name");

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		add_option("jal_db_version", $jal_db_version);
	}

	function add_answer( $args ) {
		$this->answers[ $args['name'] ] = $args;
	}

	function add_answer_score( $id, $score ) {
		if( $this->answers[ $id ] ) {
			array_push( $this->answers[ $id ]['scores'], $score );
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
		// return $this->answers[ $id ]['scores'][ $i ];
	}

	function submit_callback() {
		global $jal_db_version;
		$jal_db_version = "1.0";
	  global $wpdb; // this is how you get access to the database
	  $table_name = $wpdb->prefix . "draka_save";

	  $this->user_answers = array();
	  $this->user_answers = json_decode( str_replace("\\", null, $_POST['user_answers'] ) , false);

	  $score = 0;

		// TODO: błędny wynik zwraca problem w żadaniu AJAX
	  foreach ($this->user_answers as $answer) {
	    $score += $this->answers[ $answer['name'] ]['scores'];
	  }

		// TODO: rozdzielić odpowiedzi pozyskiwane z pliku page-ankieta.php od tych uzyskanych za pomocą AJAX
		echo "Suma punktów: " . $score . "<br>";
	  $rows_affected = $wpdb->insert( $table_name, array(
	    'save_id' => null,
	    'user_id' => get_current_user_id(),
	    'user_nicename' => $this->user_info->data->display_name,
	    'save_date' => current_time('mysql'),
	    'user_sum' => $score,
	    'user_met' => $this->user_info->user_met,
	    'user_level' => $this->get_user_level()
	  ));

	  if( $rows_affected == 1 ){
	    echo "Your message was sent.";
	  } else {
	    echo "Error, try again later.";
	  }
	  die(); // this is required to return a proper result
	}

	function get_user_level() {
		return 'gamma';
	}

	function fill_answers() {
		$args = array(
			'post_type' => 'draka'
		);
		$pytania = new WP_Query( $args );

		while ( $pytania->have_posts() ) : $pytania->the_post();
			$pyt_args = array(
			  'name' => get_the_ID(),
			  'scores' => array(),
			);

			$this->add_answer( $pyt_args );

			if( have_rows('odpowiedzi') ):
			  while ( have_rows('odpowiedzi') ) : the_row();
			    $punkty = (int) get_sub_field('ilosc_punktow_za_pytanie') * (int) get_field('punkty')[ 'mnoznik_' . $this->user_info->user_met ];
					$this->add_answer_score( get_the_ID(), $punkty);
			  endwhile;
			endif;

		endwhile; wp_reset_postdata();
	}


}

add_action( 'wp_loaded', array( 'Draka', 'get_instance' ) );

?>
