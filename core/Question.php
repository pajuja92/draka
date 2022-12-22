<?php


class Question
{

    private $id;
    private $content;
    private $tipText;
    private $options;
    private $description;
    private $category;
    private $subcategory;
    private $multiplier;

    /**
     * Question constructor.
     * @param $id
     * @param $content
     * @param $tipText
     * @param $options
     * @param $description
     * @param $category
     * @param $subcategory
     * @param $multiplier
     */
    public function __construct($id, $content, $tipText, $options, $description, $category, $subcategory, $multiplier)
    {
        $this->id = $id;
        $this->content = $content;
        $this->tipText = $tipText;
        $this->options = $options;
        $this->description = $description;
        $this->category = $category;
        $this->subcategory = $subcategory;
        $this->multiplier = $multiplier;
    }


    /**
     * @return mixed
     */
    public function get_content()
    {
        return $this->content;
    }

    /**
     * @return mixed
     */
    public function get_tipText()
    {
        return $this->tipText;
    }

    /**
     * @return array
     */
    public function get_options() {
        return $this->options;
    }

    /**
     * @return array
     */
    public function get_option( $val ) {
        return $this->options[ $val ];
    }

    /**
     * @return int
     */
    public function get_option_score( $value, $multiplier ) {
        return $this->options[ $value ][ 'score' ] * (int) $this->multiplier[ $multiplier ] ;
    }




}