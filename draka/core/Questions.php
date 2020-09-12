<?php

class Questions
{

    /**
     * A reference to an instance of this class.
     */
    private static $instance;


    /**
     * @questions zmienna zawierająca wszysktie pytania dodane w panelu administacyjnym
     */
    private $questions;

    /**
     * Questions constructor.
     */
    public function __construct()
    {
        $this->questions = array();

        $args = array(
            'post_type' => 'draka',
            'posts_per_page' => -1,
        );
        $pytania = new WP_Query( $args );

        while ( $pytania->have_posts() ) : $pytania->the_post();


            $id = get_the_ID();
            $content = get_the_title();
            $tipText = get_the_excerpt();


            $options = array();
            if( have_rows( 'odpowiedzi' ) ):
                while ( have_rows( 'odpowiedzi' ) ) : the_row();
                    array_push( $options, array(
                            'content' => get_sub_field( 'tresc_odpowiedzi' ),
                            'tip' => get_sub_field( 'podpowiedz_pytania'),
                            'score' => get_sub_field( 'ilosc_punktow_za_pytanie')
                        )
                    );
                endwhile;
            endif;

            $description = get_the_excerpt();
            $category = "";
            $subcategory = "";
            $multiplier = array();
            if( have_rows( 'punkty' ) ):
                while ( have_rows( 'punkty' ) ) : the_row();
                    $multiplier = array(
                        'z' => get_sub_field( 'mnoznik_z' ),
                        'h' => get_sub_field( 'mnoznik_h' ),
                        'hs' => get_sub_field( 'mnoznik_hs' ),
                        'w' => get_sub_field( 'mnoznik_w' ),
                    );
                endwhile;
            endif;

            $question = new Question( $id, $content, $tipText, $options, $description, $category, $subcategory, $multiplier );
            $this->questions[ get_the_ID() ] = $question;

        endwhile; wp_reset_postdata();
    }

    public static function get_instance() {
        if ( null == self::$instance ) {
            self::$instance = new Questions();
        }
        return self::$instance;
    }

    public function get_question( $id ) {
        return $id;
    }

    public function get_question_content( $id ) {
        return $this->questions[ $id ]->get_content();
    }

    public function get_tip_text( $id ) {
        return $this->questions[ $id ]->get_tipText();
    }

    public function get_options( $id ) {
        return $this->questions[ $id ]->get_options();
    }

    /**
     * @param $name - to jest ID pytania, ale jest zwalona nazwa - trzeba poprawić
     * @param $value
     * @param $ageGroup
     * @return mixed
     */
    public function get_question_score( $name, $value, $ageGroup ) {
        return $this->questions[ $name ]->get_option_score( $value, $ageGroup );
    }



}