<?php


class User
{

    /**
     * A reference to an instance of this class.
     */
    private static $instance;

    private $id;
    private $ageGroup;
    private $scoreSum;
    private $level;
    private $answers;

    /**
     * User constructor.
     * @param $score
     * @param $level
     * @param $answers
     */
    public function __construct( )
    {
        $this->id = get_current_user_id();
        $this->ageGroup = get_field('wybrana_metodyka', 'user_'. $this->id );
        $this->answers = $this->get_answers_from_db();
        $this->scoreSum = 0;
    }

    public static function get_instance() {
        if ( null == self::$instance ) {
            self::$instance = new User();
        }
        return self::$instance;
    }

    function get_answers_from_db() {

        if( empty( $this->id ) || empty( $this->ageGroup ) ) {
            echo "Brak id lub wybranej metodyki";
            return 0;
        }

        $answers = array();

        return array( 'Pytanie 1', 'Pytanie 2' );
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

    public function get_ageGroup() {
        return $this->ageGroup;
    }

}