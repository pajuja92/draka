<?php


class User
{

    /**
     * A reference to an instance of this class.
     */
    private static $instance;

    private $id;
    private $userName;
    private $ageGroup;
    private $scoreSum;
    private $level;
    private $answers;
    private $database;

    /**
     * User constructor.
     * @param $score
     * @param $level
     * @param $answers
     * @throws Exception
     */
    public function __construct( )
    {
        $this->id = get_current_user_id();
        if( get_field( 'nazwa_jednostki', 'user_'. $this->id ) ) {
            $this->userName = get_field('nazwa_jednostki', 'user_' . $this->id);
        } else {
            throw new Exception('Brak nazwy użytkownika. Aby poprawić przejdź do <a href="' . get_site_url() . '/wp-admin/profile.php">ustawień profilu</a>' );
        }
        $this->ageGroup = get_field('wybrana_metodyka', 'user_'. $this->id );
        $this->answers = $this->get_all_results();
        $this->scoreSum = 0;
        $this->level = 'brak';
    }

    public static function get_instance() {
        if ( null == self::$instance ) {
            self::$instance = new User();
        }
        return self::$instance;
    }

    /**
     * @return int
     */
    public function get_scoreSum() {
        return $this->scoreSum;
    }

    public function update_score( $answers ) {
        $newQuestions = Questions::get_instance();
        $sum = 0;
        foreach ( $answers as $answer ) {
            $sum += $newQuestions->get_question_score( $answer->name, $answer->value, $this->ageGroup );
        }
        $this->scoreSum = $sum;
    }

    public function get_age_group() {
        return $this->ageGroup;
    }

    /**
     * @return mixed
     */
    public function get_name()
    {
        return $this->userName;
    }

    /**
     * @return array
     */
    public function get_last_answers() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'draka_save';
        $user_id = $this->id;
        $user_met = $this->ageGroup;
        $answers_id_sql = "SELECT `answers` FROM ( SELECT `user_id`, MAX(`save_date`) AS `save_date`, `user_met` FROM `wp_draka_save` GROUP BY `user_id`, `user_met` ) AS latest_orders INNER JOIN `wp_draka_save` ON `wp_draka_save`.user_id = '$user_id' AND `wp_draka_save`.`save_date` = latest_orders.`save_date` AND `wp_draka_save`.`user_met` = '$user_met'";

        $res = $wpdb->get_results( $answers_id_sql );
        if( $res ) {
            return unserialize( $res[0]->answers );
        }
        return array();
    }

    /**
     * @return array
     */
    public function get_first_answers() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'draka_save';
        $user_id = $this->id;
        $user_met = $this->ageGroup;
        $answers_id_sql = "SELECT `answers` FROM ( SELECT `user_id`, MIN(`save_date`) AS `save_date`, `user_met` FROM `wp_draka_save` GROUP BY `user_id`, `user_met` ) AS latest_orders INNER JOIN `wp_draka_save` ON `wp_draka_save`.user_id = '$user_id' AND `wp_draka_save`.`save_date` = latest_orders.`save_date` AND `wp_draka_save`.`user_met` = '$user_met'";

        return unserialize( $wpdb->get_results( $answers_id_sql )[0]->answers );
    }

    /**
     * @return array
     */
    function get_last_results() {
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
        return $wpdb->get_results( $select );
    }


    /**
     * @return array
     */
    function get_first_results() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'draka_save';
        $select = "
		SELECT
		  *
		FROM
		  (SELECT
		     `user_id`, MIN(`save_date`) AS `save_date`, `user_met`
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

        return $wpdb->get_results( $select );
    }

    /**
     * @return array
     */
    function get_all_results() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'draka_save';
        $select = "
		SELECT
		  *
		FROM `wp_draka_save`
		WHERE
		  `wp_draka_save`.user_id = user_id AND
		  `wp_draka_save`.`user_met` = '" . $this->ageGroup . "';
		";

        return $wpdb->get_results( $select );
    }

    /**
     * @return string
     */
    public function get_level() {
        return $this->level;
    }
}